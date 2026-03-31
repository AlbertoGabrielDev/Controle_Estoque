<?php

use Illuminate\Support\Facades\Route;
use Modules\PriceTables\Http\Controllers\BotPriceTableController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/price-tables/active', [BotPriceTableController::class, 'active']);
    Route::get('/price-tables/quote', [BotPriceTableController::class, 'quote']);
});
