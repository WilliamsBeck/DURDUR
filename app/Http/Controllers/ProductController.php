<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category_product;
use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk (Web)
     *
     * @return View
     */
    public function index(Request $request): View
    {
        // HANYA ambil produk yang statusnya 'active'
        $query = Product::active()->with(['category_product', 'supplier']);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category_product', function ($subQ) use ($searchTerm) {
                      $subQ->where('product_category_name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('supplier', function ($subQ) use ($searchTerm) {
                      $subQ->where('supplier_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $products = $query->latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    // ... (Metode create, store, show, edit, update TIDAK diubah, kecuali di Model) ...

    /**
     * Hapus produk (Web) - Sekarang menjadi Soft Delete (Status: inactive)
     *
     * @param mixed $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        // --- Perubahan Utama: Mengubah status menjadi 'inactive' ---
        $product->update(['status' => 'inactive']); 

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus (Soft Deleted)!']);
    }

    // =========================================================================
    // API METHODS
    // =========================================================================

    public function lihat()
    {
        // HANYA tampilkan produk yang statusnya 'active'
        return response()->json([
            'success' => true,
            'data'    => Product::active()->get()
        ]);
    }

    // ... (Metode lihat_id dan store_api TIDAK diubah) ...


    // ... (Metode update_api TIDAK diubah) ...

    public function delete_api($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        try {
            // --- Perubahan Utama: Mengubah status menjadi 'inactive' ---
            $product->update(['status' => 'inactive']); 

            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus (Soft Deleted)']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus produk', 'error' => $e->getMessage()], 500);
        }
    }
}