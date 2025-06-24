<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Stores;
use App\Models\Products;
use App\Models\Stocks;

class StoresController
{
    // Funcion para devolver todas las tiendas guardadas en la base de datos 
    public function allStores()
    {
        try {
            $stores = Stores::allInfo();
            return response()->json([
                'message' => 'Operación realizada con exito',
                'data' => $stores
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al recuperar las tiendas: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ha ocurrido un error con la petición. Error al recuperar las tiendas'
            ], 500);
        }
    }

    // Devuelve la informacion de una tienda especifica con la suma de todo 
    // el stock de todos los productos que tenga
    public function storeInfo($id){
        try {
            $store = Stores::find($id);
            
            if (!$store) {
                return response()->json([
                    'error' => 'Tienda no encontrada'
                ], 404);
            }
            $products = Products::productsByStore($id);
            return response()->json([
                'message' => 'Operación realizada con exito',
                'data' => [
                    'store' => $store,
                    'products' => $products
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al recuperar la información de la tienda: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ha ocurrido un error con la petición. Error al recuperar la información de la tienda'
            ], 500);
        }
    }

    //Guarda o crea la informacion de una tienda dependiendo de si extiste o no y añade tambien 
    //los productos si se le pasa en el cuerpo de la petición. Si algo falla la transacción se encarga 
    //de que no se guarden los datos a medias. Ejemplo de body:
    /*
    {
        "store": {
            "name": "store3",
            "description": "descripcion de la store3"
        },
        "products": [
            {
                "id": 3,
                "quantity": 5
            },
            {
                "id": 4,
                "quantity": 4
            }
        ]
    }*/
    public function saveStores(Request $request){
        try {
            $storeData = (object) $request->input('store');
            if (empty($storeData)) {
                return response()->json([
                    'error' => 'No hay datos de tienda'
                ], 500);
            }
            $info = Stores::saveInfo($storeData);
            if ($request->has('products') && !empty($request->input('products'))) {
                $productsData = (object) $request->input('products');
                Stocks::addStoreStock($productsData, $info->id);
            }

            return response()->json([
                'message' => 'Datos guardados con exito'
            ], $info->code);
        } catch (\Exception $e) {
            Log::error('Error al guardar los datos: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ha ocurrido un error con la petición. Error al guardar los datos de la tienda.'
            ], 500);
        }
    }

    //Esta funcion borra una tienda. Hay dos opciones, la posibilidad de poder borrarla directamente y se borran en cascada los stocks, 
    //o que salte un aviso de que no se puede borrar porque tiene stocks asignados de producto. 
    //Para comprobar que funciona correcto todo voy a hacer la primera opción.
    public function deleteStores($id){
        try {
            $store = Stores::find($id);
            if (!$store) {
                return response()->json([
                    'error' => 'Tienda no encontrada'
                ], 404);
            }
            $deleted = Stores::deleteStore($id);
            if ($deleted) {
                return response()->json([
                    'message' => 'Tienda eliminada con exito.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No se ha podido eliminar la tienda seleccionada.'
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar la tienda: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ha ocurrido un error con la petición.Error al eliminar la tienda.'
            ], 500);
        }
    }

    //Esta funcion es la de vender productos, simplemente me voy a ceñir a quitar stock y 
    //hacer los avisos de poco stock, ninguno, o no el suficiente.
    public function sellProducts(Request $request){
        try {
            $sellData = (object) $request->all();
            $storeId = $sellData->storeId;
            $info = [];
            foreach ($sellData->products as $key => $prod) {
                $info[] = Stocks::sellProduct($prod, $storeId);
            }
            return response()->json([
                    'message' => $info
                ], 200);
        } catch (\Exception $e) {
            Log::error('Error al hacer una venta: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ha ocurrido un error con la petición. Error al hacer una venta.'
            ], 500);
        }
    }
}
