<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Penting untuk verifikasi password

class SalesTransactionController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request): View
    {
        $transactions = SalesTransaction::with(['cashier', 'voidBy', 'payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $transactions->where('id', 'like', "%$search%")
                ->orWhereHas('cashier', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhere('customer_email', 'like', "%$search%");
        }

        $transactions = $transactions
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    // ================= CREATE =================
    public function create(): View
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('title')
            ->get();

        $payments = Payment::orderBy('method_name')->get();

        return view('transactions.create', compact('products', 'payments'));
    }

    // ================= STORE =================
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payment_method,id',
            'customer_email' => 'nullable|email',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $grandTotal = 0;
                $details = [];

                $products = Product::whereIn(
                    'id',
                    collect($request->products)->pluck('id')
                )->get()->keyBy('id');

                foreach ($request->products as $item) {
                    $product = $products[$item['id']];

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock {$product->title} not enough");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $grandTotal += $subtotal;

                    $details[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                $transaction = SalesTransaction::create([
                    'cashier_id' => Auth::id(),
                    'payment_id' => $request->payment_id,
                    'customer_email' => $request->customer_email,
                    'transaction_date' => now(),
                    'grand_total' => $grandTotal,
                    'status' => 'done',
                ]);

                $transaction->details()->createMany($details);
            });

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction created successfully');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    // ================= SHOW (Detail) =================
    public function show($id): View
    {
        $transaction = SalesTransaction::with([
            'details.product',
            'cashier',
            'voidBy',
            'payment'
        ])->findOrFail($id);

        // Hanya mengembalikan view detail
        return view('transactions.show', compact('transaction'));
    }
    
    // ================= VOID FORM (Menampilkan form void.blade.php) =================
    public function voidForm($id): View|RedirectResponse
    {
        $transaction = SalesTransaction::with(['cashier', 'voidBy'])->findOrFail($id);

        if ($transaction->status !== 'done') {
            return redirect()
                ->route('transactions.show', $id)
                ->withErrors(['error' => 'Transaction cannot be voided because its status is not "done".']);
        }

        // Mengembalikan view void.blade.php
        return view('transactions.void', compact('transaction'));
    }

    // ================= VOID PROCESS (Memproses pembatalan, verifikasi password, dan kembalikan stok) =================
    public function void(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'void_reason' => 'required|string|max:255',
            'password' => 'required|string', // Validasi input password harus ada
        ]);

        $transaction = SalesTransaction::with('details')->findOrFail($id);

        // --- VALIDASI PASSWORD ---
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            // Jika password tidak cocok
            return back()->withErrors([
                'password' => 'The provided password does not match your current password for confirmation.'
            ])->withInput(); 
        }
        // --- AKHIR VALIDASI PASSWORD ---

        // Cek Status Final
        if ($transaction->status !== 'done') {
            return back()->withErrors(['error' => 'Transaction cannot be voided because its status is not "done".']);
        }

        try {
            DB::transaction(function () use ($transaction, $request) {
                
                // 1. KEMBALIKAN STOK PRODUK
                foreach ($transaction->details as $detail) {
                    $product = Product::find($detail->product_id);
                    
                    if ($product) {
                        // Tambahkan stok produk dengan kuantitas yang ada di detail transaksi
                        $product->increment('stock', $detail->quantity);
                    }
                }

                // 2. Update status transaksi
                $transaction->update([
                    'status' => 'void',
                    'void_reason' => $request->void_reason,
                    'void_user_id' => Auth::id(), // ID user yang melakukan void
                    'void_at' => now(),
                ]);
            });

            return redirect()
                ->route('transactions.index')
                ->with('success', "Transaction #{$transaction->id} has been voided successfully. Stock has been returned.");

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Void failed: " . $e->getMessage()
            ])->withInput();
        }
    }
}