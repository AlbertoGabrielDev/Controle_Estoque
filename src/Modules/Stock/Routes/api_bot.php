<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\BotStockController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/stock', [BotStockController::class, 'search']);
    Route::get('/stock/{id}', [BotStockController::class, 'show'])->where('id', '[0-9]+');
});
