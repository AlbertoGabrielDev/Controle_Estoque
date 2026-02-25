<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\Api\CartController;
use Modules\Sales\Http\Controllers\Api\OrderController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::post('/carts/upsert', [CartController::class, 'upsert']);
    Route::get('/carts/by-client/{client}', [CartController::class, 'getByclient']);
    Route::post('/carts/remove', [CartController::class, 'remove']);
    Route::post('/orders', [OrderController::class, 'store']);
});
