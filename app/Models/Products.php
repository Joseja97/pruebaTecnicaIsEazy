<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = ["name", "description"];

    public static function productsByStore($storeId){
        return self::select('products.id','products.name', 's.quantity')
        ->join('stocks as s','s.product_id','=','products.id')
        ->where('s.store_id','=',$storeId)
        ->get();
    }
}
