<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $table = 'sales_transactions';

    protected $fillable = [
        'cashier_id',
        'payment_id',
        'customer_email',
        'transaction_date',
        'grand_total',
        'status',
        'void_reason',
        'void_user_id',
        'void_at',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'void_at' => 'datetime',
    ];

    // ================= RELATION =================

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function voidBy()
    {
        return $this->belongsTo(User::class, 'void_user_id');
    }

    public function details()
    {
        return $this->hasMany(
            SalesTransactionDetail::class,
            'sales_transaction_id'
        );
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

     public function get_transaksi_penjualan_detail()
{
    return $this->select(
            'sales_transactions.*',
            'sales_transaction_details.*',
            'products.title as product_title',
            'products.price as product_price',
            'category_product.product_category_name as product_category_name',
            DB::raw('(sales_transaction_details.quantity * products.price) as total_harga')
        )
            ->join('sales_transaction_details', 'sales_transaction_details.sales_transaction_id', '=', 'sales_transactions.id')
            ->join('products', 'sales_transaction_details.product_id', '=', 'products.id')
            ->join('category_product', 'category_product.id', '=', 'products.product_category_id');



        return $sql;
    }
}