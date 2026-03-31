<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\BotOrderController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/orders', [BotOrderController::class, 'search']);
    Route::get('/orders/{id}', [BotOrderController::class, 'show'])->where('id', '[0-9]+');
});
