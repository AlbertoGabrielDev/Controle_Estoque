<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\BotCustomerController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/customers', [BotCustomerController::class, 'byPhone']);
    Route::get('/customers/{id}/summary', [BotCustomerController::class, 'summary'])->where('id', '[0-9]+');
});
