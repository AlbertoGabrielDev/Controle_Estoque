<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\CentroCustoController;
use Modules\Finance\Http\Controllers\ContaContabilController;
use Modules\Finance\Http\Controllers\DespesaController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/cadastros')->group(function () {
        Route::prefix('/centros-custo')->name('centros_custo.')->group(function () {
            Route::get('/', [CentroCustoController::class, 'index'])->name('index');
            Route::get('/data', [CentroCustoController::class, 'data'])->name('data');
            Route::get('/create', [CentroCustoController::class, 'create'])->name('create');
            Route::post('/', [CentroCustoController::class, 'store'])->name('store');
            Route::get('/{centro_custo}/edit', [CentroCustoController::class, 'edit'])->name('edit');
            Route::put('/{centro_custo}', [CentroCustoController::class, 'update'])->name('update');
            Route::delete('/{centro_custo}', [CentroCustoController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [CentroCustoController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/contas-contabeis')->name('contas_contabeis.')->group(function () {
            Route::get('/', [ContaContabilController::class, 'index'])->name('index');
            Route::get('/data', [ContaContabilController::class, 'data'])->name('data');
            Route::get('/create', [ContaContabilController::class, 'create'])->name('create');
            Route::post('/', [ContaContabilController::class, 'store'])->name('store');
            Route::get('/{conta_contabil}/edit', [ContaContabilController::class, 'edit'])->name('edit');
            Route::put('/{conta_contabil}', [ContaContabilController::class, 'update'])->name('update');
            Route::delete('/{conta_contabil}', [ContaContabilController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [ContaContabilController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/despesas')->name('despesas.')->group(function () {
            Route::get('/', [DespesaController::class, 'index'])->name('index');
            Route::get('/data', [DespesaController::class, 'data'])->name('data');
            Route::get('/create', [DespesaController::class, 'create'])->name('create');
            Route::post('/', [DespesaController::class, 'store'])->name('store');
            Route::get('/{despesa}/edit', [DespesaController::class, 'edit'])->name('edit');
            Route::put('/{despesa}', [DespesaController::class, 'update'])->name('update');
            Route::delete('/{despesa}', [DespesaController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [DespesaController::class, 'updateStatus'])->name('status');
        });
    });
});
