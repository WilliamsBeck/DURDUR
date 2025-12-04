<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk logging error
use Carbon\Carbon;

class SalesTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = SalesTransaction::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $transactions->where(function($query) use ($searchTerm) {
                $query->where('cashier_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('customer_email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('id', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('date_filter')) {
            $dateFilter = $request->input('date_filter');
            $now = Carbon::now();

            switch ($dateFilter) {
                case 'today':
                    $transactions->whereDate('created_at', $now->toDateString());
                    break;
                case 'last_7_days':
                    $transactions->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()]);
                    break;
                case 'last_month':
                    // Perbaikan logika bulan lalu
                    $lastMonth = $now->copy()->subMonth();
                    $transactions->whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year);
                    break;
                case 'this_month':
                    $transactions->whereMonth('created_at', $now->month)
                                 ->whereYear('created_at', $now->year);
                    break;
            }
        }

        $transactions = $transactions->orderBy('created_at', 'desc')->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        $products = Product::where('stock', '>', 0)->orderBy('title')->get();
        // Mengirim JSON untuk Javascript frontend jika diperlukan
        $productsJson = $products->keyBy('id'); 

        return view('transactions.create', compact('products', 'productsJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'cashier_name'      => 'required|min:3',
            'customer_email'    => 'nullable|email', // Tambahkan validasi email
            'products'          => 'required|array',
            'products.*.id'     => 'required|exists:products,id', // Pastikan ID produk valid
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            // Gunakan DB Transaction agar data aman
            $transaction = DB::transaction(function () use ($request) {
                $grandTotal = 0;
                $detailsData = [];
                
                // Ambil semua produk sekaligus untuk efisiensi
                $productIds = collect($request->products)->pluck('id');
                $productsInDb = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($request->products as $item) {
                    $product = $productsInDb[$item['id']] ?? null;

                    if (!$product) {
                        throw new \Exception("Product ID {$item['id']} not found.");
                    }

                    // Cek Stok Cukup atau Tidak
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock for {$product->title} is not enough. Available: {$product->stock}");
                    }

                    $price = $product->price;
                    $subtotal = $price * $item['quantity'];
                    $grandTotal += $subtotal;

                    $detailsData[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $price,
                        'subtotal'   => $subtotal, // Pastikan kolom ini ada di database atau gunakan logika controller
                    ];

                    // Kurangi stok
                    $product->decrement('stock', $item['quantity']);
                }

                // Buat Transaksi Utama
                $newTransaction = SalesTransaction::create([
                    'cashier_name'   => $request->cashier_name,
                    'customer_email' => $request->customer_email,
                    'grand_total'    => $grandTotal,
                ]);

                // Simpan Detail (menggunakan createMany untuk efisiensi)
                $newTransaction->details()->createMany($detailsData);

                return $newTransaction;
            });

            // === Kirim Email (Di luar DB Transaction agar tidak membatalkan sale jika email gagal) ===
            if ($transaction->customer_email) {
                try {
                    $this->sendEmail($transaction->id);
                } catch (\Exception $e) {
                    Log::error("Failed sending email for transaction " . $transaction->id . ": " . $e->getMessage());
                    return redirect()->route('transactions.index')
                        ->with(['success' => 'Transaction Created, but Email failed to send.']);
                }
            }

            return redirect()->route('transactions.index')
                ->with(['success' => 'Transaction Created and Email Sent Successfully!']);

        } catch (\Exception $e) {
            // Jika ada error stok atau database, kembali ke form dengan error
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(string $id): View
    {
        $transaction = SalesTransaction::with('details.product')->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(string $id): View
    {
        $transaction = SalesTransaction::with('details')->findOrFail($id);
        $products = Product::orderBy('title')->get();
        $productsJson = $products->keyBy('id');

        return view('transactions.edit', compact('transaction', 'products', 'productsJson'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'cashier_name'        => 'required|min:3',
            'products'            => 'required|array',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $transaction = SalesTransaction::with('details')->findOrFail($id);

                // 1. Kembalikan stok lama
                foreach ($transaction->details as $oldDetail) {
                    Product::where('id', $oldDetail->product_id)->increment('stock', $oldDetail->quantity);
                }

                // 2. Hapus detail lama
                $transaction->details()->delete();

                // 3. Proses detail baru (sama seperti store)
                $grandTotal = 0;
                $detailsData = [];
                
                $productIds = collect($request->products)->pluck('id');
                $productsInDb = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($request->products as $item) {
                    $product = $productsInDb[$item['id']];

                    // Cek Stok (Stok sudah dikembalikan di langkah 1, jadi ini aman)
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock for {$product->title} is not enough.");
                    }

                    $price = $product->price;
                    $subtotal = $price * $item['quantity'];
                    $grandTotal += $subtotal;

                    $detailsData[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $price,
                        'subtotal'   => $subtotal,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                // 4. Update Transaksi
                $transaction->update([
                    'cashier_name'   => $request->cashier_name,
                    'customer_email' => $request->customer_email,
                    'grand_total'    => $grandTotal,
                ]);

                // 5. Simpan detail baru
                $transaction->details()->createMany($detailsData);
            });

            return redirect()->route('transactions.index')->with(['success' => 'Transaction Updated Successfully!']);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $transaction = SalesTransaction::with('details')->findOrFail($id);
                
                // Kembalikan stok
                foreach ($transaction->details as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }

                // Hapus detail dan transaksi (jika cascade delete di db di set, line ini opsional, tapi aman ditulis)
                $transaction->details()->delete();
                $transaction->delete();
            });

            return redirect()->route('transactions.index')->with(['success' => 'Transaction Deleted Successfully!']);

        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->withErrors(['error' => 'Failed to delete transaction.']);
        }
    }

    public function sendEmail($id)
    {
        // Menggunakan Eloquent Relationship standar
        // Pastikan model SalesTransaction punya relasi: public function details() { return $this->hasMany(...); }
        // Dan SalesTransactionDetail punya relasi: public function product() { return $this->belongsTo(...); }
        
        $transaction = SalesTransaction::with(['details.product'])->findOrFail($id);
        
        // Siapkan data untuk view email
        // Kita tidak perlu menghitung total_harga manual karena sudah ada di $transaction->grand_total
        // Tapi jika view memaksa butuh struktur array tertentu:
        
        $data = [
            'transaction' => $transaction,
            'details'     => $transaction->details, // Collection detail
        ];

        Mail::send('emails.transaksi_detail', $data, function ($message) use ($transaction) {
            $to = $transaction->customer_email ?? 'default@email.com';
            
            $message->to($to)
                    ->subject("Detail Transaksi Anda - Total Rp " . number_format($transaction->grand_total, 0, ',', '.'));
        });
    }

    // --- API Methods (Sebaiknya dipisah ke ApiController, tapi jika ingin disini:) ---

    public function lihat()
    {
        return response()->json(SalesTransaction::all());
    }

    public function lihat_id($id)
    {
        $sales = SalesTransaction::with('details.product')->find($id);
        
        if (!$sales) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        
        return response()->json($sales);
    }
}