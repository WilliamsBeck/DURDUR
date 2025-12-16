<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionDetail;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. PENDAPATAN HARI INI
        $pendapatanHariIni = SalesTransaction::whereDate('created_at', Carbon::today())
            ->sum('grand_total');
            
        // 1.5. TOTAL PRODUK TERJUAL HARI INI
        $totalProdukTerjualHariIni = SalesTransactionDetail::whereDate('created_at', Carbon::today())
            ->sum('quantity');

        // 2. GRAFIK PENJUALAN 7 HARI TERAKHIR
        $tanggal7Hari = [];
        $penjualan7Hari = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $tanggal7Hari[] = $date->format('D, d M'); 

            $total = SalesTransaction::whereDate('created_at', $date)
                ->sum('grand_total');

            $penjualan7Hari[] = $total; 
        }

        // 3. 3 PRODUK PENJUALAN TERTINGGI HARI INI
        $produkTertinggi = SalesTransactionDetail::select(
                'product_id', 
                DB::raw('SUM(quantity) as total_quantity'), 
                DB::raw('SUM(grand_total) as total_omzet') 
            )
            ->whereDate('created_at', Carbon::today())
            ->groupBy('product_id')
            ->orderByDesc('total_quantity') 
            ->limit(3)
            ->with('product') 
            ->get();
            
        // 4. PRODUK DENGAN STOK RENDAH
        // Ambang Batas Stok Rendah
        $low_stock_threshold = 5; 

        $produkRendahStok = Product::where('stock', '<', $low_stock_threshold)
            ->orderBy('stock', 'asc')
            ->with('supplier')
            ->get();

        return view('dashboard.index', compact(
            'pendapatanHariIni',
            'totalProdukTerjualHariIni',
            'tanggal7Hari',
            'penjualan7Hari',
            'produkTertinggi',
            'produkRendahStok'
        ));
    }
}