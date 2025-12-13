<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchase';

    protected $fillable = [
        'user_id',       
        'supplier_id',
        'total_cost',
        'status', 
        'received_at',
        'void_reason',
        'void_by',
        'void_at',
    ];

    protected $casts = [
        // 'purchase_date' => 'datetime', <--- DIHAPUS
        'void_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // ... (Relasi lainnya tetap sama)
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function voidBy()
    {
        return $this->belongsTo(User::class, 'void_by');
    }
}