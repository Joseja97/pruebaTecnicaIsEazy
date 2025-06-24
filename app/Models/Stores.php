<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stores extends Model
{
    protected $fillable = ["name", "description"];

    public static function allInfo(){
        return self::select('stores.name', DB::raw('COALESCE(SUM(s.quantity), 0) AS total_quantity'))
        ->leftJoin('stocks as s','stores.id','=','s.store_id')
        ->groupBy('stores.id', 'stores.name')
        ->get();
    }

    public static function saveInfo($data){
        try {
            DB::beginTransaction();
            if (isset($data->id)) {
                $store = self::find($data->id);
                if (!$store) {
                    throw new \Exception('Tienda no encontrada');
                }
                $store->update([
                    'name' => $data->name,
                    'description' => $data->description,
                ]);
                $code = 200;
                $id = $data->id;
            } else {
                $store = self::where('name', $data->name)->first();
                if (!$store) {
                    $store = self::create([
                        'name' => $data->name,
                        'description' => $data->description
                    ]);
                    $code = 201;
                }else {
                    $store->update([
                        'description' => $data->description,
                    ]);
                    $code = 200;
                }
                $id = $store->id;
            }
            DB::commit();
            return (object) ["code" => $code, "id" => $id];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function deleteStore($id){
        try {
            DB::beginTransaction();
            self::where('id','=',$id)->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
