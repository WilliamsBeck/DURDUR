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
     * index
     *
     * @return View
     */
    public function index(Request $request): View
{
   
    $product_model = new Product;
    $productsQuery = $product_model->get_product();

    if ($request->filled('search')) {
        $searchTerm = $request->input('search');
        $productsQuery->where(function($query) use ($searchTerm) {
            $query->where('products.title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category_product.product_category_name', 'like', '%' . $searchTerm . '%') 
                  ->orWhere('supplier.supplier_name', 'like', '%' . $searchTerm . '%');
        });
    }

    $products = $productsQuery->latest('products.created_at')->paginate(10);

    return view('products.index', compact('products'));
}

    /**
     * create
     *
     * @return View
     */
    public function create(): View
    {
        $category_product = new Category_product;
        $supplier = new Supplier;

        $data['categories'] = $category_product->get_category_product()->get();
        $data['suppliers'] = $supplier->get_supplier();

        return view('products.create', compact('data'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // var_dump($request); exit;
        //validate form
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',    
            'title' => 'required|min:5',
            'supplier_id'   => 'required|integer',
            'product_category_id' => 'required|integer',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $store_image = $image->store('images', 'public');

            $product = new product;
            $insert_product = $product->storeProduct($request, $image);

            return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }

        return redirect()->route('products.index')->with(['error' => 'Failed to upload image (request).']);
    }

    /**
    * @param mixed Sid
    * @return View
    */
    public function show(string $id): View
    {
        //get product by ID
        $product_model = new Product;
        $product = $product_model->get_product()->where("products.id", $id)->firstOrFail();

        //render view with product
        return view('products.show', compact('product'));
    }

    /**
    * edit
    *
    * @param mixed Sid
    * @return View
    */
    public function edit(string $id): View
    {
        //get product by ID
        $product_model = new Product;
        $data['product'] = $product_model->get_product()->where("products.id", $id)->firstOrFail();
        
        $category_product = new Category_product;
        $supplier = new Supplier;
        $data['categories'] = $category_product->get_category_product()->get();
        $data['suppliers'] = $supplier->get_supplier();
   
        return view('products.edit', compact('data'));
    }

    /**
    * update
    *
    * @param mixed $request
    * @param mixed $id
    * @return RedirectResponse
    */
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        //get product by ID
        $product_model = new Product;
        $nama_gambar = null;

        if ($request->hasFile('image')) {
            
            $image = $request->file('image');
            $store_image = $image->store('images', 'public'); 
            $nama_gambar = $image->hashName();

            $data_product = $product_model->get_product()->where("products.id", $id)->firstOrFail();
            
            Storage::disk('public')->delete('images/'.$data_product->image);
            
        } 
            $request_data = [
                'title'                 => $request->title,
                'product_category_id'   => $request->product_category_id,
                'supplier_id'           => $request->supplier_id,
                'description'           => $request->description,
                'price'                 => $request->price,
                'stock'                 => $request->stock
            ];

            $update_product = $product_model->updateProduct($id, $request, $nama_gambar);

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
    * @param mixed Sid
    * @return RedirectResponse
    */
    public function destroy($id): RedirectResponse
    {
        $product_model = new Product;
        $product = $product_model->get_product()->where("products.id", $id)->firstOrFail();

        Storage::disk('public')->delete('images/'.$product->image);

        $product->delete();

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
















    
    public function lihat()
    {
        return Product::all();

    }

    public function lihat_id($id)
    {
        $product = Product::find($id);
            if (!$product) return response()->json(['message' => 'Product not found'], 404);
            return $product;

    }

    public function store_api(Request $request)
{
    try {
        
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',    
            'title' => 'required|min:5',
            'supplier_id'   => 'required|integer',
            'product_category_id' => 'required|integer',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        // 2. Cek dan Simpan Gambar
        if (!$request->hasFile('image')) {
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: File tidak ditemukan.'
            ], 400); 
        }

        $image = $request->file('image');
      
        $store_image_path = $image->store('images', 'public'); 
        
        $product = new Product;
        
        $inserted_product = $product->storeProduct($request, $image);

        
        
        return response()->json([
            'success' => true,
            'message' => 'Data Produk Berhasil Disimpan!',
            'data'    => $inserted_product 
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors'  => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server.',
            // 'debug'   => $e->getMessage() 
        ], 500);
    }
}
    // API: Update Produk
    public function update_api(Request $request, $id)
{
    $product = Product::find($id);
    if (!$product) {
        return response()->json(['message' => 'Produk tidak ditemukan'], 404);
    }

    // VALIDASI
    $validated = $request->validate([
        'title' => 'sometimes|required|min:5',
        'description' => 'sometimes|required|min:10',
        'price' => 'sometimes|required|numeric',
        'stock' => 'sometimes|required|numeric',
        'product_category_id' => 'sometimes|required|integer',
        'supplier_id' => 'sometimes|required|integer',
        'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120'
    ]);

    // UPDATE FIELD BIASA
    $product->fill($validated);

    // PROSES GAMBAR BARU
    if ($request->hasFile('image')) {

        // Hapus gambar lama
        if ($product->image && Storage::disk('public')->exists('images/' . $product->image)) {
            Storage::disk('public')->delete('images/' . $product->image);
        }

        // Simpan gambar baru
        $path = $request->file('image')->store('images', 'public');
        $product->image = basename($path);
    }

    $product->save();

    return response()->json([
        'message' => 'Produk berhasil diupdate',
        'data' => $product
    ]);
}

    // API: Hapus Produk
    public function delete_api($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // jika menyimpan gambar di storage dan ingin menghapus gambar:
        if ($product->image) {
            // Contoh: hapus file jika disimpan di public disk
            Storage::disk('public')->delete('images/' . $product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }





}