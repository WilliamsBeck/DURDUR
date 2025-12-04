<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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
                    $transactions->whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()]);
                    break;
                case 'last_month':
                    $transactions->whereMonth('created_at', Carbon::now()->subMonth()->month)
                                 ->whereYear('created_at', Carbon::now()->subMonth()->year); // Pastikan tahun juga disesuaikan untuk bulan lalu
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
        $productsJson = $products->keyBy('id');

        return view('transactions.create', compact('products', 'productsJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'cashier_name'      => 'required|min:3',
            'products'          => 'required|array',
            'products.*.id'     => 'required',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $grandTotal = 0;
        $transactionDetails = [];

        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            $price = $product->price;
            $subtotal = $price * $item['quantity'];
            $grandTotal += $subtotal;

            $transactionDetails[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $price,
                'subtotal'   => $subtotal,
            ];
            $product->stock = $product->stock - $item['quantity'];
            $product->save();
        }
        
        $transaction = SalesTransaction::create([
            'cashier_name'    => $request->cashier_name,
            'customer_email'  => $request->customer_email,
            'grand_total'     => $grandTotal,
        ]);

        // Simpan detail transaksi
        foreach ($transactionDetails as $detail) {
            $transaction->details()->create($detail);
        }

      
        // === Kirim email ke customer (dengan parameter) ===
        $this->sendEmail($transaction->customer_email, $transaction->id);

        

        return redirect()->route('transactions.index')
            ->with(['success' => 'Transaction Created and Email Sent Successfully!']);
    }

    public function show(string $id): View
    {
        $transaction = SalesTransaction::with('details.product')->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(string $id): View
    {
        $transaction = SalesTransaction::find($id);
        $products = Product::orderBy('title')->get();
        

        $productsJson = $products->keyBy('id');
              
        $transaction->details = SalesTransactionDetail::where('sales_transaction_id', $id)->get();

        return view('transactions.edit', compact('transaction', 'products', 'productsJson'));
    }
    
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'cashier_name'      => 'required|min:3',
            'products'          => 'required|array',
            'products.*.id'     => 'required',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $transaction = SalesTransaction::findOrFail($id);
        
        // Kembalikan stok lama
        foreach ($transaction->details as $oldDetail) {
            $product = Product::find($oldDetail->product_id);
            if ($product) {
                $product->stock += $oldDetail->quantity;
                $product->save();
            }
        }
        
        // Hapus detail lama
        $transaction->details()->delete();

        $grandTotal = 0;
        $newTransactionDetails = [];

        // Hitung ulang total dan siapkan detail baru
        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            $price = $product->price;
            $subtotal = $price * $item['quantity'];
            $grandTotal += $subtotal;

            $newTransactionDetails[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $price,
                'subtotal'   => $subtotal,
            ];
            
            // Kurangi stok baru
            $product->stock -= $item['quantity'];
            $product->save();
        }

        // Update transaksi utama
        $transaction->update([
            'cashier_name'   => $request->cashier_name,
            'customer_email' => $request->customer_email,
            'grand_total'    => $grandTotal,
        ]);
        
        // Simpan detail transaksi yang baru
        foreach ($newTransactionDetails as $detail) {
            $transaction->details()->create($detail);
        }

        return redirect()->route('transactions.index')->with(['success' => 'Transaction Updated Successfully!']);
    }

    public function destroy(string $id): RedirectResponse
    {
        $transaction = SalesTransaction::find($id);
        
        $detailItems = SalesTransactionDetail::where('sales_transaction_id', $transaction->id)->get();

        foreach ($detailItems as $item) {
            $product = Product::find($item->product_id);
            $product->stock = $product->stock + $item->quantity;
            $product->save();
        }

        SalesTransactionDetail::where('sales_transaction_id', $transaction->id)->delete();
        $transaction->delete();

        return redirect()->route('transactions.index')->with(['success' => 'Transaction Deleted Successfully!']);
    }


       public function sendEmail($to, $id)
        {
            // get transaksi by ID
            $transaksi_penjualan = new SalesTransaction();
            $data = $transaksi_penjualan->get_transaksi_penjualan_detail()
                ->where("sales_transactions.id", $id)
                ->get();

            // hitung total harga
            $total_harga['transaksi'] = 0;
            foreach ($data as $key => $value) {
                $total_harga['transaksi'] += $value['total_harga'];
            }

            // ambil 1 transaksi utama (data pertama dari collection)
            $transaction = $data[0]; 

            // variabel yang dikirim ke view
            $transaksi_ = [
                'transaction' => $transaction,
                'data' => $data,
                'total_harga' => $total_harga
            ];

            // Mengirim email
            Mail::send('emails.transaksi_detail', $transaksi_, function ($message) use ($data, $total_harga) {
                $customerEmail = $data[0]->customer_email ?? 'default@email.com';

                $message->to($customerEmail)
                    ->subject("Detail Transaksi Anda - Total Rp " . number_format($total_harga['transaksi'], 0, ',', '.'));
            });

        }













        

     public function lihat()
            {
                return SalesTransaction::all();

            }

            public function lihat_id($id)
            {
                $sales = SalesTransaction::find($id);
                    if (!$sales) return response()->json(['message' => 'Transaction not found'], 404);
                    return $sales;

            }




}