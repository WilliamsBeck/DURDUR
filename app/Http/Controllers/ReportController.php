<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\SalesTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Filter Tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query Dasar (Done status only for sales stats)
        $query = SalesTransaction::whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);

        // --- SECTION 1: GENERAL STATS ---
        $totalSales = (clone $query)->where('status', 'done')->sum('grand_total');
        $totalBill = (clone $query)->where('status', 'done')->count();
        // Hindari pembagian dengan nol
        $averageSales = $totalBill > 0 ? $totalSales / $totalBill : 0;

        // --- SECTION 2: STATUS TRANSACTION ---
        // Kita butuh hitung yg void juga, jadi buat query baru tanpa filter status dulu
        $allTrxQuery = SalesTransaction::whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);

        $successfulTrxCount = (clone $allTrxQuery)->where('status', 'done')->count();
        $successfulTrxValue = (clone $allTrxQuery)->where('status', 'done')->sum('grand_total');
        
        $voidTrxCount = (clone $allTrxQuery)->where('status', 'void')->count();
        // Asumsi: Void value adalah nilai transaksi yang dibatalkan
        $voidTrxValue = (clone $allTrxQuery)->where('status', 'void')->sum('grand_total');

        // --- SECTION 3: PAYMENT METHODS (Cash, QRIS, dll) ---
        $paymentStats = (clone $query)->where('status', 'done')
            ->join('payment_method', 'sales_transactions.payment_id', '=', 'payment_method.id')
            ->select(
                'payment_method.method_name', 
                DB::raw('count(*) as total_count'), 
                DB::raw('sum(grand_total) as total_amount')
            )
            ->groupBy('payment_method.method_name')
            ->get();

        return view('reports.index', compact(
            'startDate', 'endDate',
            'totalSales', 'totalBill', 'averageSales',
            'successfulTrxCount', 'successfulTrxValue',
            'voidTrxCount', 'voidTrxValue',
            'paymentStats'
        ));
    }

    public function purchasement(Request $request): View
    {
        // 1. Filter Tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. Query Dasar
        $query = Purchase::with('supplier')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // Clone query untuk perhitungan agar tidak menumpuk
        $allPurchases = (clone $query)->get();

        // --- SECTION 1: GENERAL STATS (Berdasarkan Status DONE/RECEIVED) ---
        // Biasanya laporan keuangan menghitung yang sudah selesai (received)
        $receivedPurchases = $allPurchases->where('status', 'done');
        
        $totalPurchaseAmount = $receivedPurchases->sum('total_cost');
        $totalBill = $receivedPurchases->count();
        // Rata-rata per transaksi
        $averagePurchase = $totalBill > 0 ? $totalPurchaseAmount / $totalBill : 0;

        // --- SECTION 2: STATUS BREAKDOWN ---
        $pendingPurchases = $allPurchases->where('status', 'pending');
        $voidPurchases = $allPurchases->where('status', 'void');

        $receivedCount = $receivedPurchases->count();
        $receivedValue = $receivedPurchases->sum('total_cost');

        $pendingCount = $pendingPurchases->count();
        $pendingValue = $pendingPurchases->sum('total_cost');

        $voidCount = $voidPurchases->count();
        $voidValue = $voidPurchases->sum('total_cost');

        // --- SECTION 3: SUPPLIER BREAKDOWN ---
        // Kita kelompokkan berdasarkan Supplier untuk menghitung total belanja ke masing-masing supplier
        // Hanya menghitung yang statusnya 'done' (Barang sudah diterima)
        $supplierStats = $receivedPurchases->groupBy('supplier_id')->map(function ($row) {
            return [
                'supplier_name' => $row->first()->supplier->supplier_name ?? 'Unknown',
                'count' => $row->count(),
                'total_amount' => $row->sum('total_cost')
            ];
        })->values(); // Reset keys array

        return view('reports.purchase', compact(
            'startDate', 'endDate',
            'totalPurchaseAmount', 'totalBill', 'averagePurchase',
            'receivedCount', 'receivedValue',
            'pendingCount', 'pendingValue',
            'voidCount', 'voidValue',
            'supplierStats'
        ));
    }

    // ================= 3. PRODUCT SALES REPORT =================
    public function productSales(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Ambil data
        $soldItems = SalesTransactionDetail::whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate)
                  ->where('status', 'done');
            })
            // Gunakan withTrashed() jika ingin memuat produk yang soft-deleted
            ->with(['product' => function($q) {
                $q->withTrashed(); 
            }, 'product.category_product'])
            ->get();

        // 2. Hitung Total Statistik
        $totalProductQuantity = $soldItems->sum('quantity');
        $totalProductSales = $soldItems->sum('subtotal');

        // 3. Grouping Data (DIPERBAIKI AGAR TIDAK ERROR JIKA PRODUK NULL)
        $reportData = $soldItems->groupBy(function ($item) {
            // Gunakan tanda tanya (?) agar tidak error jika product sudah dihapus
            return $item->product?->category_product?->product_category_name ?? 'Uncategorized';
        })->map(function ($itemsByCategory) {
            return $itemsByCategory->groupBy('product_id')->map(function ($itemsByProduct) {
                $firstItem = $itemsByProduct->first();
                
                // FIX ERROR: Cek apakah product ada, jika null beri teks default
                $productName = $firstItem->product?->title ?? 'Produk Terhapus (ID: ' . $firstItem->product_id . ')';

                return [
                    'product_name' => $productName,
                    'total_qty'    => $itemsByProduct->sum('quantity'),
                    'total_price'  => $itemsByProduct->sum('subtotal')
                ];
            });
        })->sortKeys();

        return view('reports.product_sales', compact(
            'startDate', 
            'endDate', 
            'totalProductQuantity', 
            'totalProductSales', 
            'reportData'
        ));
    }

    // ================= 4. REMAINING STOCK REPORT =================
    public function remainingStock(Request $request): View
    {
        // 1. Query Dasar Produk (Load relasi kategori)
        // Gunakan withTrashed() jika ingin melihat stok produk yang sudah dihapus juga, 
        // tapi biasanya laporan stok aktif tidak pakai withTrashed(). Sesuaikan kebutuhan.
        $query = Product::with('category_product');

        // 2. Filter Search (Sesuai Desain)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('category_product', function($sub) use ($search) {
                      $sub->where('product_category_name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->get();

        // 3. Grouping Berdasarkan Kategori
        $reportData = $products->groupBy(function ($product) {
            return $product->category_product->product_category_name ?? 'Uncategorized';
        })->sortKeys();

        return view('reports.remaining_stock', compact('reportData'));
    }

}