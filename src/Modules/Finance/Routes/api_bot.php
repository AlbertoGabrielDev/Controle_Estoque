<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\BotFinanceController;

Route::middleware(['api', 'bot.api.key'])->prefix('api/bot')->group(function () {
    Route::get('/finance/customer-balance', [BotFinanceController::class, 'customerBalance']);
});
