<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;



    Route::get('/products/search', [ProdutoController::class, 'search']);
    Route::get('/products/{sku}', [ProdutoController::class, 'show']);

    Route::post('/carts/upsert', [CartController::class, 'upsert']);
    Route::get('/carts/by-msisdn/{msisdn}', [CartController::class, 'getByMsisdn']);

    Route::post('/orders', [OrderController::class, 'store']);

