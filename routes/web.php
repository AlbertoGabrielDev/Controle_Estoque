<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;

Route::prefix('/verdurao')->group(function(){
    // return view('welcome');
    Route::prefix('/categoria')->group(function(){
        Route::get('/',[CategoriaController::class, 'Inicio'])->name('categoria.inicio');
        Route::get('/index',[CategoriaController::class, 'Index'])->name('categoria.index');
    });
     
    Route::prefix('/produtos')->group(function(){
        Route::get('/',[ProdutoController::class, 'produtos'])->name('produtos.inicio');
        Route::get('/index',[ProdutoController::class, 'Index'])->name('produtos.index');
        Route::get('/cadastro',[ProdutoController::class, 'cadastro'])->name('produtos.cadastro');
    });
     
    Route::prefix('/estoque')->group(function(){
        Route::get('/',[EstoqueController::class, 'Index'])->name('estoque.index');
    });

    Route::prefix('/fornecedor')->group(function(){
        Route::get('/',[FornecedorController::class, 'index'])->name('fornecedor.index');
    });

});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
