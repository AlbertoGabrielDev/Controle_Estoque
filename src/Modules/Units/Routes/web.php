<?php

use Illuminate\Support\Facades\Route;
use Modules\Units\Http\Controllers\UnidadeController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/unidades')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidade.index')->middleware('check.permission:view_post,unidades');
        Route::get('/data', [UnidadeController::class, 'data'])->name('unidade.data')->middleware('check.permission:view_post,unidades');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro')->middleware('check.permission:create_post,unidades');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'buscar'])->name('unidade.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar')->middleware('check.permission:edit_post,unidades');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{modelName}/{id}', [UnidadeController::class, 'updateStatus'])->name('unidades.status');
    });
});
