<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\ClienteController;
use Modules\Customers\Http\Controllers\CustomerSegmentController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/clientes')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/data', [ClienteController::class, 'data'])->name('clientes.data');
        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/status/{modelName}/{id}', [ClienteController::class, 'updateStatus'])
            ->middleware('check.permission:status,clientes')
            ->name('cliente.status');
        Route::get('/clientes-autocomplete', [ClienteController::class, 'autocomplete'])->name('clientes.autocomplete');

        Route::get('/segmentos', [CustomerSegmentController::class, 'index'])->name('segmentos.index');
        Route::get('/segmentos/data', [CustomerSegmentController::class, 'data'])->name('segmentos.data');
        Route::get('/segmentos/create', [CustomerSegmentController::class, 'create'])->name('segmentos.create');
        Route::post('/segmentos', [CustomerSegmentController::class, 'store'])->name('segmentos.store');
        Route::get('/segmentos/{segment}', [CustomerSegmentController::class, 'edit'])->name('segmentos.edit');
        Route::put('/segmentos/{segment}', [CustomerSegmentController::class, 'update'])->name('segmentos.update');
        Route::delete('/segmentos/{segment}', [CustomerSegmentController::class, 'destroy'])->name('segmentos.destroy');
    });
});
