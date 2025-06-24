<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoresController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('stores')->group(function () {
    Route::get('/', [StoresController::class, 'allStores']);
    Route::get('/{storeId}', [StoresController::class, 'storeInfo']);
    Route::put('/save', [StoresController::class, 'saveStores']);
    Route::delete('/delete/{storeId}', [StoresController::class, 'deleteStores']);
    Route::post('/sell', [StoresController::class, 'sellProducts']);
});
