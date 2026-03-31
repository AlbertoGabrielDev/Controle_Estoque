<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\BotStockController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/stock', [BotStockController::class, 'byProduct']);
    Route::get('/stock/availability', [BotStockController::class, 'availability']);
});
