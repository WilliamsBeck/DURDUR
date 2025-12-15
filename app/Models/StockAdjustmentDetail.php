<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentDetail extends Model
{
    protected $guarded = ['id'];

    // Relasi balik ke header
    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}