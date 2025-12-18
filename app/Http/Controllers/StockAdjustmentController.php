<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{

    public function index(Request $request)
    {
        $query = StockAdjustment::with('user');

        // Logika Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            $query->where(function($q) use ($search) {
                // Cari berdasarkan ID Transaksi (misal ketik: 5)
                $q->where('id', $search)
                // ATAU Cari berdasarkan Nama User (Adjusted By)
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  })
                // ATAU Cari berdasarkan Tanggal (misal ketik: 2025-12)
                  ->orWhere('transaction_date', 'like', '%' . $search . '%');
            });
        }

        $adjustments = $query->latest()->paginate(10);
        
        // Agar pagination tidak reset saat pindah halaman search
        $adjustments->appends(['search' => $request->search]); 

        return view('stock_adjustments.index', compact('adjustments'));
    }

    // Tampilan Create Form 
    public function create()
    {
        // Ambil semua produk untuk dropdown
        $products = Product::select('id', 'title', 'stock')->get();
        
        // Data default untuk tampilan
        $currentUser = Auth::user();
        $currentTime = now();

        return view('stock_adjustments.create', compact('products', 'currentUser', 'currentTime'));
    }

    // Proses Simpan Data 
    public function store(Request $request)
    {
        $request->validate([
            // Validasi array items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.new_stock' => 'required|numeric|min:0',
            'items.*.reason' => 'required|string',
            'items.*.note' => 'nullable|string',
        ]);

        try {
            // Gunakan DB Transaction agar atomik (semua sukses atau semua gagal)
            DB::beginTransaction();

            // 1. Buat Header Adjustment
            $adjustment = StockAdjustment::create([
                'user_id' => Auth::id(),
                'transaction_date' => now(),
            ]);

            // 2. Loop items yang dikirim dari form
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $oldStock = $product->stock;
                $newStock = $itemData['new_stock'];
                $difference = $newStock - $oldStock;

                // Jika tidak ada perubahan stok, skip saja (opsional)
                if ($difference == 0) continue;

                // 3. Simpan Detail Adjustment
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'difference' => $difference,
                    'reason' => $itemData['reason'],
                    'note' => $itemData['note'] ?? null,
                ]);

                // 4. UPDATE STOK PRODUK ASLI
                // Ini langkah terpenting untuk mengubah stok di inventory
                $product->update(['stock' => $newStock]);
            }

            DB::commit(); // Jika semua lancar, simpan permanen

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment has been saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, batalkan semua perubahan
            return redirect()->back()
                ->with('error', 'Failed to save adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Tampilan Detail Show (Gambar 0 / 1)
    public function show($id)
    {
        $adjustment = StockAdjustment::with(['user', 'details.product'])->findOrFail($id);
        return view('stock_adjustments.show', compact('adjustment'));
    }
}