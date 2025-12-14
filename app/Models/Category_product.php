<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category_product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'category_product';

    protected $fillable = [
        'product_category_name',
    ];

    public function get_category_product()
    {
        $sql = $this->select("*");
        return $sql;
    }
}