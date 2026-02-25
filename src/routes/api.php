<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;



Route::post('/carts/upsert', [CartController::class, 'upsert']);
Route::get('/carts/by-client/{client}', [CartController::class, 'getByclient']);

Route::post('/carts/remove', [CartController::class, 'remove']);
Route::post('/orders', [OrderController::class, 'store']);

Route::get('/vendas/month', [CalendarController::class, 'month'])->name('api.vendas.month');
