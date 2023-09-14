<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MarcaController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function(){
    Route::prefix('/categoria')->group(function(){
        Route::get('/',[CategoriaController::class, 'Inicio'])->name('categoria.inicio');
        Route::get('/index',[CategoriaController::class, 'Index'])->name('categoria.index');
        Route::get('/cadastro',[CategoriaController::class, 'cadastro'])->name('categoria.cadastro');
        Route::post('/cadastro',[CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}',[CategoriaController::class, 'produto'])->name('categorias.produto');
    });

    Route::prefix('/produtos')->group(function(){
        Route::get('/',[ProdutoController::class, 'produtos'])->name('produtos.inicio');
        Route::get('/index',[ProdutoController::class, 'Index'])->name('produtos.index');
        Route::get('/cadastro',[ProdutoController::class, 'cadastro'])->name('produtos.cadastro');
        Route::post('/salvar-cadastro',[ProdutoController::class, 'inserirCadastro'])->name('produtos.salvarCadastro');
        Route::get('/buscar-produto',[ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
    });
     
    Route::prefix('/estoque')->group(function(){
        Route::get('/',[EstoqueController::class, 'Index'])->name('estoque.index');
        Route::get('/cadastro',[EstoqueController::class, 'Cadastro'])->name('estoque.cadastro');
        Route::post('/cadastro',[EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
        Route::get('/buscar-estoque',[EstoqueController::class, 'buscar'])->name('estoque.buscar');
    });

    Route::prefix('/fornecedor')->group(function(){
        Route::get('/',[FornecedorController::class, 'index'])->name('fornecedor.index');
        Route::get('/cadastro',[FornecedorController::class, 'Cadastro'])->name('fornecedor.cadastro');
        Route::post('/cadastro',[FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro');
        Route::get('/cidade/{estado}',[FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor',[FornecedorController::class, 'Buscar'])->name('fornecedor.buscar');
    });

    Route::prefix('/marca')->group(function(){
        Route::get('/index',[MarcaController::class, 'index'])->name('marca.index');
        Route::get('/cadastro',[MarcaController::class, 'cadastro'])->name('marca.cadastro');
        Route::get('/buscar-marca',[MarcaController::class, 'Buscar'])->name('marca.buscar');
        Route::get('/editar/{id}',[MarcaController::class, 'Editar'])->name('marca.editar');
        Route::post('/cadastro',[MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
    });

});
