<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;


class Supplier extends Model
{

   use SoftDeletes;
   
    protected $table = 'supplier';
    
    protected $fillable = [
    'supplier_name', 
    'pic_supplier',
    'supplier_email', 
    'supplier_phone', 
    'supplier_address'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function get_supplier()
    {
        return $this->select("*")->get();
    }

    public static function storeSupplier(Request $request)
    {
        return self::create($request->all());
    }

    public static function updateSupplier($id, Request $request)
    {
        $supplier = self::find($id);
        if ($supplier) {
            $supplier->update($request->all());
            return $supplier;
        }
        return null;
    }
}