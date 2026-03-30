<?php

use App\Http\Controllers\BotApiController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

Route::get('/vendas/month', [CalendarController::class, 'month'])->name('api.vendas.month');

Route::prefix('bot')
    ->middleware('bot.apikey')
    ->group(function () {
        Route::get('/products', [BotApiController::class, 'products']);
        Route::get('/products/{id}', [BotApiController::class, 'product']);

        Route::get('/stock', [BotApiController::class, 'stock']);
        Route::get('/stock/availability', [BotApiController::class, 'availability']);

        Route::get('/customers', [BotApiController::class, 'customerByPhone']);
        Route::get('/customers/{id}/summary', [BotApiController::class, 'customerSummary']);

        Route::get('/orders', [BotApiController::class, 'orders']);
        Route::get('/orders/{id}', [BotApiController::class, 'order']);

        Route::get('/finance/customer-balance', [BotApiController::class, 'customerBalance']);

        Route::get('/price-tables/active', [BotApiController::class, 'activePriceTable']);
        Route::get('/price-tables/quote', [BotApiController::class, 'quote']);
    });
