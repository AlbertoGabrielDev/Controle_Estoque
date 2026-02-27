<?php

use Illuminate\Support\Facades\Route;
use Modules\Categories\Http\Controllers\CategoriaController;
use Modules\Products\Http\Controllers\ProdutoController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/categoria')->group(function () {
        Route::get('/', [CategoriaController::class, 'inicio'])->name('categoria.inicio');
        Route::get('/index', [CategoriaController::class, 'index'])->name('categoria.index');
        Route::get('/data', [CategoriaController::class, 'data'])->name('categoria.data');
        Route::get('/cadastro', [CategoriaController::class, 'cadastro'])->name('categoria.cadastro');
        Route::post('/cadastro', [CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}', [CategoriaController::class, 'produto'])->name('categorias.produto');
        Route::get('/editar/{categoriaId}', [CategoriaController::class, 'editar'])->name('categorias.editar');
        Route::post('/editar/{categoriaId}', [CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
        Route::delete('/{categoriaId}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
        Route::post('/status/{modelName}/{id}', [CategoriaController::class, 'updateStatus'])->name('categoria.status');
        Route::post('/produto/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });
});
