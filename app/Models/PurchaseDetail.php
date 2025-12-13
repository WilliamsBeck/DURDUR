<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    
    protected $table = 'purchase_detail';

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'price',    // REVISI: Sesuai schema (sebelumnya unit_price)
        'subtotal',
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}