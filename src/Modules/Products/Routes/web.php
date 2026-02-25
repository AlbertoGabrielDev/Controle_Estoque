<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ProdutoController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/produtos')->group(function () {
        Route::get('/index', [ProdutoController::class, 'index'])->name('produtos.index')->middleware('check.permission:view_post,Produtos');
        Route::get('/data', [ProdutoController::class, 'data'])->name('produtos.data')->middleware(['web', 'auth']);
        Route::get('/cadastro', [ProdutoController::class, 'cadastro'])->name('produtos.cadastro')->middleware('check.permission:create_post,Produtos');
        Route::post('/salvar-cadastro', [ProdutoController::class, 'inserirCadastro'])->name('produtos.salvarCadastro');
        Route::get('/buscar-produto', [ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
        Route::get('/editar/{produtoId}', [ProdutoController::class, 'editar'])->name('produtos.editar')->middleware('check.permission:edit_post,Produtos');
        Route::post('/editar/{produtoId}', [ProdutoController::class, 'salvarEditar'])->name('produtos.salvarEditar');
        Route::post('/status/{modelName}/{id}', [ProdutoController::class, 'updateStatus'])->name('produto.status');
    });
});
