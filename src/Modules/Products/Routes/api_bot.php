<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\BotProductController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/products', [BotProductController::class, 'search']);
    Route::get('/products/{id}', [BotProductController::class, 'show'])->where('id', '[0-9]+');
});
