<?php

use Illuminate\Support\Facades\Route;
use Modules\Brands\Http\Controllers\MarcaController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/marca')->group(function () {
        Route::get('/index', [MarcaController::class, 'index'])->name('marca.index')->middleware('check.permission:view_post,marca');
        Route::get('/data', [MarcaController::class, 'data'])->name('marca.data')->middleware('check.permission:view_post,marca');
        Route::get('/cadastro', [MarcaController::class, 'cadastro'])->name('marca.cadastro')->middleware('check.permission:create_post,marca');
        Route::post('/cadastro', [MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
        Route::get('/buscar-marca', [MarcaController::class, 'buscar'])->name('marca.buscar');
        Route::get('/editar/{marcaId}', [MarcaController::class, 'editar'])->name('marca.editar')->middleware('check.permission:edit_post,marca');
        Route::post('/editar/{marcaId}', [MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
        Route::post('/status/{modelName}/{id}', [MarcaController::class, 'updateStatus'])->name('marca.status')->middleware('check.permission:status,marca');
    });
});
