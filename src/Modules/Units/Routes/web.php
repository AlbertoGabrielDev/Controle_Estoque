<?php

use Illuminate\Support\Facades\Route;
use Modules\Units\Http\Controllers\UnidadeController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/unidades')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidade.index');
        Route::get('/data', [UnidadeController::class, 'data'])->name('unidade.data');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'buscar'])->name('unidade.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{modelName}/{id}', [UnidadeController::class, 'updateStatus'])->name('unidades.status');
    });
});
