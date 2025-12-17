<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Supplier; 
use App\Models\Category_product;

class Product extends Model
{
    use SoftDeletes;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'title',
        'product_category_id',
        'supplier_id',
        'description',
        'price',
        'cost_price',
        'stock',
        
    ];

    /**
     * Relasi ke Supplier. 
     */
    public function category_product()
        {
            // Parameter ke-2 ('product_category_id') adalah nama kolom foreign key di tabel products
            return $this->belongsTo(Category_product::class, 'product_category_id')->withTrashed();
        }

        // Relasi ke tabel Supplier
    public function supplier()
        {
            return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
        }

    public function get_product()
    {
        
        $sql = $this->select(
            "products.*", 
            "category_product.product_category_name as product_category_name",
            "supplier.supplier_name as supplier_name"
        )
        ->leftjoin('category_product', 'category_product.id', '=', 'products.product_category_id')
        ->leftjoin('supplier', 'supplier.id', '=', 'products.supplier_id');
       

        return $sql;
    }
   


    public static function storeProduct($request, $image)
    {
        return self::create([
            'image'               => $image->hashName(),
            'title'               => $request->title,
            'product_category_id' => $request->product_category_id,
            'supplier_id'         => $request->supplier_id,
            'description'         => $request->description,
            'price'               => $request->price,
            'cost_price'          => $request->cost_price,
            
        ]);
    }

   
    public static function updateProduct($id, $request, $image = null)
    {
        $product = self::find($id);

        if ($product) {
            $data = [
                'title'               => $request['title'],
                'product_category_id' => $request['product_category_id'],
                'supplier_id'         => $request['supplier_id'],
                'description'         => $request['description'],
                'price'               => $request['price'],   
                'cost_price'          => $request['cost_price'],
               
            ];

            if (!empty($image)) {
                $data['image'] = $image;
            }

            $product->update($data);
            return $product;
            
        } else {
            return "tidak ada data yang diupdate";
        }
    }
   
}