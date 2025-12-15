<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    // 1. PENTING: Agar data bisa disimpan (Mass Assignment)
    protected $guarded = ['id'];

    // 2. PENTING: Agar Laravel tahu ini adalah tanggal, bukan teks biasa
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StockAdjustmentDetail::class);
    }
}