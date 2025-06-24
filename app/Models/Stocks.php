<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stocks extends Model
{
    protected $fillable = ["store_id", "product_id", "quantity"];

    public static function addStoreStock($data, $storeId){
        try {
            DB::beginTransaction();
            foreach ($data as $key => $product) {
                $exist = self::where([
                    ['store_id','=',$storeId],
                    ['product_id','=',$product['id']]
                ])->first();
                if ($exist) {
                    $exist->update([
                        'quantity' => $product['quantity']
                    ]);
                } else {
                    self::create([
                        'store_id' => $storeId,
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity']
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function sellProduct($prodData, $storeId){
        try {
            DB::beginTransaction();
            $info = self::where([
                    ['stocks.store_id','=',$storeId],
                    ['stocks.product_id','=',$prodData['id']]
                ])
                ->join('products as p','stocks.product_id','=','p.id')
                ->select('stocks.id','p.name','stocks.quantity')
                ->first();

            switch (true) {
                case $info->quantity == $prodData['quantity']:
                    self::where('id','=',$info->id)->update([
                        'quantity' => 0
                    ]);
                    $message = 'Se ha vendido el producto ' . $info->name . ' y no queda stock.';
                    break;
                case $info->quantity > $prodData['quantity']:
                    $q = ($info->quantity - $prodData['quantity']);
                    self::where('id','=',$info->id)->update([
                        'quantity' => $q
                    ]);
                    $message = 'Se ha vendido el producto ' . $info->name . ' y queda ' . ($q <= 5 ? 'poco' : 'bastante') . ' stock (Stock: ' . $info->quantity . ').';
                    break;
                case $info->quantity < $prodData['quantity']:
                    $message = 'No hay suficiente stock, para este pedido, del producto ' . $info->name . ' (Stock: ' . $info->quantity . ').';
                    break;
            }
            DB::commit();
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
