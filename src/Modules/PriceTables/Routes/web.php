<?php

use Illuminate\Support\Facades\Route;
use Modules\PriceTables\Http\Controllers\TabelaPrecoController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/cadastros')->group(function () {
        Route::prefix('/tabelas-preco')->name('tabelas_preco.')->group(function () {
            Route::get('/', [TabelaPrecoController::class, 'index'])->name('index');
            Route::get('/data', [TabelaPrecoController::class, 'data'])->name('data');
            Route::get('/create', [TabelaPrecoController::class, 'create'])->name('create');
            Route::post('/', [TabelaPrecoController::class, 'store'])->name('store');
            Route::get('/{tabela_preco}/edit', [TabelaPrecoController::class, 'edit'])->name('edit');
            Route::put('/{tabela_preco}', [TabelaPrecoController::class, 'update'])->name('update');
            Route::delete('/{tabela_preco}', [TabelaPrecoController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [TabelaPrecoController::class, 'updateStatus'])->name('status');
        });
    });
});
