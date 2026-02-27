<?php

use Illuminate\Support\Facades\Route;
use Modules\Suppliers\Http\Controllers\FornecedorController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/fornecedor')->group(function () {
        Route::get('/', [FornecedorController::class, 'index'])->name('fornecedor.index');
        Route::get('/data', [FornecedorController::class, 'data'])->name('fornecedor.data');
        Route::get('/cadastro', [FornecedorController::class, 'cadastro'])->name('fornecedor.cadastro');
        Route::post('/cadastro', [FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro');
        Route::get('/cidade/{estado}', [FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor', [FornecedorController::class, 'buscar'])->name('fornecedor.buscar');
        Route::get('/editar/{fornecedorId}', [FornecedorController::class, 'editar'])->name('fornecedor.editar');
        Route::post('/editar/{fornecedorId}', [FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar');
        Route::delete('/{fornecedorId}', [FornecedorController::class, 'destroy'])->name('fornecedor.destroy');
        Route::post('/status/{modelName}/{id}', [FornecedorController::class, 'updateStatus'])->name('fornecedor.status');
    });
});
