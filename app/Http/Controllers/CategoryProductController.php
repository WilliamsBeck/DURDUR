<?php

namespace App\Http\Controllers;

use App\Models\Category_product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryProductController extends Controller
{
    /**
     * Menampilkan daftar kategori produk yang aktif (tidak terhapus).
     */
    public function index(Request $request): View
    {
        // Menggunakan withoutTrashed() agar hanya mengambil data yang TIDAK di-soft delete
        $category_products = Category_product::withoutTrashed();
        
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $category_products->where('product_category_name', 'like', '%' . $searchTerm . '%');
        }

        $category_products = $category_products->latest()->paginate(10);

        return view('category_products.index', compact('category_products'));
    }

    /**
     * Menampilkan form untuk membuat kategori produk baru.
     */
    public function create(): View
    {
        return view('category_products.create');
    }

    /**
     * Menyimpan kategori produk baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['product_category_name' => 'required|min:3']);

        Category_product::create(['product_category_name' => $request->product_category_name]);

        return redirect()->route('category_products.index')->with(['success' => 'Kategori Produk Berhasil Disimpan!']);
    }

    /**
     * Menampilkan form untuk mengedit kategori produk.
     */
    public function edit(Category_product $category_product): View
    {
        return view('category_products.edit', compact('category_product'));
    }

    /**
     * Memperbarui kategori produk di database.
     */
    public function update(Request $request, Category_product $category_product): RedirectResponse
    {
        $request->validate(['product_category_name' => 'required|min:3']);

        $category_product->update(['product_category_name' => $request->product_category_name]);

        return redirect()->route('category_products.index')->with(['success' => 'Kategori Produk Berhasil Diubah!']);
    }

    /**
     * Mengarsipkan (Soft Delete) kategori produk.
     */
    public function destroy(Category_product $category_product): RedirectResponse
    {
        // Menggunakan delete() untuk melakukan Soft Delete
        $category_product->delete();
        return redirect()->route('category_products.index')->with(['success' => 'Kategori Produk Berhasil Diarsipkan!']);
    }

    /**
     * Menampilkan daftar kategori produk yang diarsip (Soft Deleted).
     */
    public function archived(Request $request): View
    {
        // Menggunakan onlyTrashed() untuk hanya mengambil data yang di-soft delete
        $category_products = Category_product::onlyTrashed();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $category_products->where('product_category_name', 'like', '%' . $searchTerm . '%');
        }

        $category_products = $category_products->latest()->paginate(10);

        // Memanggil view archived.blade.php
        return view('category_products.archived', compact('category_products'));
    }

    /**
     * Memulihkan kategori produk yang di-soft delete (Restore).
     */
    public function restore(string $id): RedirectResponse
    {
        // Menggunakan withTrashed() agar bisa menemukan data yang di-soft delete berdasarkan ID
        $category_product = Category_product::withTrashed()->findOrFail($id); 
        
        // Melakukan restore (mengatur deleted_at menjadi NULL)
        $category_product->restore();
        
        return redirect()->route('category_products.archived')->with(['success' => 'Kategori Produk Berhasil Dipulihkan!']);
    }
}