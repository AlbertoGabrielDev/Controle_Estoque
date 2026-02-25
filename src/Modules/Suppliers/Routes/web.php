<?php

use Illuminate\Support\Facades\Route;
use Modules\Suppliers\Http\Controllers\FornecedorController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/fornecedor')->group(function () {
        Route::get('/', [FornecedorController::class, 'index'])->name('fornecedor.index')->middleware('check.permission:view_post,fornecedores');
        Route::get('/data', [FornecedorController::class, 'data'])->name('fornecedor.data')->middleware('check.permission:view_post,fornecedores');
        Route::get('/cadastro', [FornecedorController::class, 'cadastro'])->name('fornecedor.cadastro')->middleware('check.permission:create_post,fornecedores');
        Route::post('/cadastro', [FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro')->middleware('check.permission:create_post,fornecedores');
        Route::get('/cidade/{estado}', [FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor', [FornecedorController::class, 'buscar'])->name('fornecedor.buscar');
        Route::get('/editar/{fornecedorId}', [FornecedorController::class, 'editar'])->name('fornecedor.editar')->middleware('check.permission:edit_post,fornecedores');
        Route::post('/editar/{fornecedorId}', [FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar')->middleware('check.permission:edit_post,fornecedores');
        Route::delete('/{fornecedorId}', [FornecedorController::class, 'destroy'])->name('fornecedor.destroy')->middleware('check.permission:edit_post,fornecedores');
        Route::post('/status/{modelName}/{id}', [FornecedorController::class, 'updateStatus'])->middleware('check.permission:status,fornecedores')->name('fornecedor.status');
    });
});
