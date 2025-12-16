<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}