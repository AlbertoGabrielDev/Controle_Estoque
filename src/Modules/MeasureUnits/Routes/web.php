<?php

use Illuminate\Support\Facades\Route;
use Modules\MeasureUnits\Http\Controllers\UnidadeMedidaController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/cadastros')->group(function () {
        Route::prefix('/unidades-medida')->name('unidades_medida.')->group(function () {
            Route::get('/', [UnidadeMedidaController::class, 'index'])->name('index');
            Route::get('/data', [UnidadeMedidaController::class, 'data'])->name('data');
            Route::get('/create', [UnidadeMedidaController::class, 'create'])->name('create');
            Route::post('/', [UnidadeMedidaController::class, 'store'])->name('store');
            Route::get('/{unidade_medida}/edit', [UnidadeMedidaController::class, 'edit'])->name('edit');
            Route::put('/{unidade_medida}', [UnidadeMedidaController::class, 'update'])->name('update');
            Route::delete('/{unidade_medida}', [UnidadeMedidaController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [UnidadeMedidaController::class, 'updateStatus'])->name('status');
        });
    });
});
