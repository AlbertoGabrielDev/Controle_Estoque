<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SalesSettingsController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/vendas', [SalesSettingsController::class, 'index'])->name('vendas');
        Route::put('/vendas', [SalesSettingsController::class, 'update'])->name('vendas.update');
    });
});
