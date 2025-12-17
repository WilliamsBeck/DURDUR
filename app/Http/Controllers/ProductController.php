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
       $query = Product::with(['category_product', 'supplier']);

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

    /**
     * Form tambah produk (Web)
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category_product::all();
        $suppliers = Supplier::all();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Simpan produk baru (Web)
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image'               => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'               => 'required|min:5',
            'supplier_id'         => 'required|exists:supplier,id',
            'product_category_id' => 'required|exists:category_product,id',
            'description'         => 'required|min:10',
            'price'               => 'required|numeric',
            'cost_price'          => 'required|numeric',
            
        ]);

        try {
            
            // Simpan ke folder 'images' di dalam disk 'public'
            $image = $request->file('image');
            $image->storeAs('images', $image->hashName(), 'public');

            // Simpan Data
            Product::create([
                'image'               => $image->hashName(),
                'title'               => $request->title,
                'product_category_id' => $request->product_category_id,
                'supplier_id'         => $request->supplier_id,
                'description'         => $request->description,
                'price'               => $request->price,
                'cost_price'          => $request->cost_price,
                
            ]);

            return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Detail produk (Web)
     *
     * @param string $id
     * @return View
     */
    public function show(string $id): View
    {
        $product = Product::with(['category_product', 'supplier'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Form edit produk (Web)
     *
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        // Kita bungkus semua dalam array $data agar cocok dengan view lama kamu
        $data['product']    = Product::findOrFail($id);
        $data['categories'] = Category_product::all();
        $data['suppliers']  = Supplier::all();

        return view('products.edit', compact('data'));
    }

    /**
     * Update produk (Web)
     *
     * @param mixed $request
     * @param mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'image'               => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'title'               => 'required|min:5',
            'product_category_id' => 'required|numeric',
            'supplier_id'         => 'required|numeric',
            'description'         => 'required|min:10',
            'price'               => 'required|numeric',
            'cost_price'          => 'required|numeric',
            
        ]);

        $product = Product::findOrFail($id);

        $dataToUpdate = [
            'title'               => $request->title,
            'product_category_id' => $request->product_category_id,
            'supplier_id'         => $request->supplier_id,
            'description'         => $request->description,
            'price'               => $request->price,
            'cost_price'          => $request->cost_price,
            
        ];

        if ($request->hasFile('image')) {
            // 1. Upload Gambar Baru (PERBAIKAN UTAMA DI SINI)
            $image = $request->file('image');
            $image->storeAs('images', $image->hashName(), 'public');

            // 2. Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete('images/' . $product->image);
            }

            // 3. Masukkan nama gambar baru ke array update
            $dataToUpdate['image'] = $image->hashName();
        }

        $product->update($dataToUpdate);

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Hapus produk (Web)
     *
     * @param mixed $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);


        $product->delete();

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }


        /**
         * Memulihkan produk yang di-soft delete (Restore)
         *
         * @param mixed $id
         * @return RedirectResponse
         */
        public function restore($id): RedirectResponse
        {
            // Menggunakan withTrashed() agar bisa mencari record yang sudah dihapus
            $product = Product::withTrashed()->findOrFail($id); 

            if ($product) {
                $product->restore(); // Metode bawaan SoftDeletes untuk mengisi deleted_at = NULL
            }
            
            return redirect()->route('products.index')->with(['success' => 'Produk Berhasil Dipulihkan!']);
        }

     

/**
             * Menampilkan daftar produk yang diarsip (Soft Deleted)
             *
             * @return View
             */
            public function archived(): View
            {
                $products = Product::onlyTrashed()->with(['category_product', 'supplier'])->latest()->paginate(10);

                return view('products.archived', compact('products'));
            }
    

    // =========================================================================
    // API METHODS
    // =========================================================================

    public function lihat()
    {
        return response()->json([
            'success' => true,
            'data'    => Product::all()
        ]);
    }

    public function lihat_id($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $product]);
    }

    public function store_api(Request $request)
    {
        try {
            $request->validate([
                'image'               => 'required|image|mimes:jpeg,jpg,png|max:2048',
                'title'               => 'required|min:5',
                'supplier_id'         => 'required|integer',
                'product_category_id' => 'required|integer',
                'description'         => 'required|min:10',
                'price'               => 'required|numeric',
                'stock'               => 'required|numeric'
            ]);

            // Upload Gambar (PERBAIKAN UNTUK API JUGA)
            $image = $request->file('image');
            $image->storeAs('images', $image->hashName(), 'public');

            $product = Product::create([
                'image'               => $image->hashName(),
                'title'               => $request->title,
                'product_category_id' => $request->product_category_id,
                'supplier_id'         => $request->supplier_id,
                'description'         => $request->description,
                'price'               => $request->price,
                'stock'               => $request->stock
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Produk Berhasil Disimpan!',
                'data'    => $product
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update_api(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            'title'               => 'sometimes|required|min:5',
            'description'         => 'sometimes|required|min:10',
            'price'               => 'sometimes|required|numeric',
            'stock'               => 'sometimes|required|numeric',
            'product_category_id' => 'sometimes|required|integer',
            'supplier_id'         => 'sometimes|required|integer',
            'image'               => 'nullable|image|mimes:jpeg,jpg,png|max:5120'
        ]);

        try {
            $product->fill($request->except('image'));

            if ($request->hasFile('image')) {
                // Hapus gambar lama
                if ($product->image) {
                    Storage::disk('public')->delete('images/' . $product->image);
                }

                // Upload gambar baru (PERBAIKAN UNTUK API)
                $image = $request->file('image');
                $image->storeAs('images', $image->hashName(), 'public');
                $product->image = $image->hashName();
            }

            $product->save();

            return response()->json(['success' => true, 'message' => 'Produk berhasil diupdate', 'data' => $product]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal update produk', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete_api($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        try {
            if ($product->image) {
                Storage::disk('public')->delete('images/' . $product->image);
            }

            $product->delete();

            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus produk', 'error' => $e->getMessage()], 500);
        }
    }
}