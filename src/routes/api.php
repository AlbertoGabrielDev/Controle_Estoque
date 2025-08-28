<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;



Route::get('/products/search', [ProdutoController::class, 'search']);
Route::get('/products/{sku}', [ProdutoController::class, 'show']);

Route::post('/carts/upsert', [CartController::class, 'upsert']);
Route::get('/carts/by-client/{client}', [CartController::class, 'getByclient']);

Route::post('/carts/remove', [CartController::class, 'remove']);
Route::post('/orders', [OrderController::class, 'store']);

