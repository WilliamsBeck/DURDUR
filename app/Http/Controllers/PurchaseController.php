<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PurchaseController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request): View
    {
        $purchases = Purchase::with(['supplier', 'user', 'voidBy']); 

        if ($request->filled('search')) {
            $search = $request->search;
            $purchases->where('id', 'like', "%$search%")
                ->orWhereHas('supplier', function ($q) use ($search) {
                    $q->where('supplier_name', 'like', "%$search%"); 
                });
        }

        $purchases = $purchases
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    // ================= CREATE =================
    public function create(): View
    {
        // Pastikan scopeActive() hanya memfilter status, bukan stok
        $products = Product::active()->orderBy('title')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get(); 

        return view('purchases.create', compact('products', 'suppliers'));
    }

    // ================= STORE (Status Awal: Pending) =================
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalCost = 0;
                $details = [];
                
                foreach ($request->products as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $totalCost += $subtotal;

                    $details[] = [
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'], 
                        'subtotal' => $subtotal,
                        // created_at/updated_at akan otomatis oleh createMany
                    ];
                    
                    // TIDAK ADA PENAMBAHAN STOK DI SINI (Status Awal Pending)
                }

                // Create Header Purchase
                $purchase = Purchase::create([
                    'user_id' => Auth::id(), 
                    'supplier_id' => $request->supplier_id,
                    'total_cost' => $totalCost,
                    'status' => 'pending', // STATUS AWAL
                ]);

                // Create Details
                $purchase->details()->createMany($details);
            });

            return redirect()
                ->route('purchases.index')
                ->with('success', 'Purchase Order created successfully (Status: Pending)');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Transaction failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    // ================= SHOW =================
    public function show($id): View
    {
        $purchase = Purchase::with([
            'details.product',
            'supplier',
            'voidBy',
            'user',
            
        ])->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }

    // ================= UPDATE STATUS / RECEIVE STOCK (Pending -> Done) =================
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        if ($purchase->status !== 'pending') {
            return back()->withErrors(['error' => 'Transaction status must be "pending" to be marked as "done".']);
        }

        try {
            DB::transaction(function () use ($purchase) {
                
                // 1. Tambah Stok (Hanya terjadi saat status diubah menjadi DONE)
                foreach ($purchase->details as $detail) {
                    Product::where('id', $detail->product_id)->increment('stock', $detail->quantity);
                }

                // 2. Update Status Purchase Header
                $purchase->update([
                    'status' => 'done',
                   
                    'received_at' => now(),
                ]);
            });

            return redirect()
                ->route('purchases.show', $id)
                ->with('success', "Purchase #{$id} received successfully. Stock has been added.");

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Failed to update status: " . $e->getMessage()
            ]);
        }
    }

    // ================= VOID FORM =================
    public function voidForm($id)
    {
        $purchase = Purchase::with(['supplier', 'voidBy'])->findOrFail($id);

        // Hanya transaksi berstatus 'done' atau 'pending' yang bisa di-void
        if ($purchase->status !== 'done' && $purchase->status !== 'pending') {
            return redirect()
                ->route('purchases.show', $id)
                ->withErrors(['error' => 'Purchase cannot be voided because its status is already ' . strtoupper($purchase->status) . '.']);
        }

        return view('purchases.void', compact('purchase'));
    }

    // ================= VOID PROCESS =================
    public function void(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'void_reason' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $purchase = Purchase::with('details')->findOrFail($id);
        
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password incorrect.'
            ])->withInput(); 
        }

        if ($purchase->status !== 'done' && $purchase->status !== 'pending') {
            return back()->withErrors(['error' => 'Transaction cannot be voided because its status is already ' . strtoupper($purchase->status) . '.']);
        }

        try {
            DB::transaction(function () use ($purchase, $request) {
                
                $reverted = false;
                // LOGIKA PENTING: Pengurangan Stok hanya jika status LAMA adalah 'done'
                if ($purchase->status == 'done') {
                    foreach ($purchase->details as $detail) {
                        $product = Product::find($detail->product_id);
                        
                        if ($product) {
                            if ($product->stock < $detail->quantity) {
                                 throw new \Exception("Cannot void: Stock of '{$product->title}' is insufficient. Inventory movement has occurred.");
                            }
                            $product->decrement('stock', $detail->quantity);
                        }
                    }
                    $reverted = true;
                }
                
                // Update status pembelian menjadi 'void'
                $purchase->update([
                    'status' => 'void',
                    'void_reason' => $request->void_reason,
                    'void_by' => Auth::id(),
                    'void_at' => now(),
                ]);

                return $reverted; // Mengembalikan nilai untuk digunakan di luar transaction
            });

            $message = "Purchase #{$purchase->id} voided. ";
            // Cek hasil dari DB::transaction (meskipun biasanya lebih baik dicek berdasarkan status lama $purchase)
            $message .= ($purchase->status == 'done') ? "Stock reverted." : "No stock adjustment needed.";
            
            return redirect()
                ->route('purchases.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Void failed: " . $e->getMessage()
            ])->withInput();
        }
    }

    // ================= AJAX: GET PRODUCTS BY SUPPLIER =================
    public function getProductsBySupplier($supplierId)
    {
        if (empty($supplierId)) {
            return response()->json([], 400);
        }
        
        $products = Product::where('supplier_id', $supplierId)
                         ->active() 
                         ->orderBy('title', 'asc') 
                         ->get(['id', 'title', 'cost_price', 'stock']); 

        return response()->json($products);
    }
}