<?php

use Illuminate\Support\Facades\Route;
use Modules\Categories\Http\Controllers\BotCategoryController;

Route::prefix('api/bot/categories')
    ->middleware('bot.api.key')
    ->group(function () {
        Route::get('/', [BotCategoryController::class, 'search']);
        Route::get('/{id}', [BotCategoryController::class, 'show'])->whereNumber('id');
    });
