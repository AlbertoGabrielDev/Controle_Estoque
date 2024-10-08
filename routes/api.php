<?php

use App\Http\Controllers\Api\GraficosApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UnidadeController;

// Versão da API
Route::middleware('api')->group(function () {
Route::prefix('v1')->group(function() {

    Route::prefix('/verdurao')->middleware(['auth:sanctum', 'verified'])->group(function() {
        Route::prefix('/categoria')->group(function() {
            Route::get('/',[CategoriaController::class, 'Inicio'])->name('categoria.inicio');
            Route::get('/index',[CategoriaController::class, 'Index'])->name('categoria.index');
            Route::get('/cadastro',[CategoriaController::class, 'cadastro'])->name('categoria.cadastro');
            Route::post('/cadastro',[CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
            Route::get('/produto/{categoria}',[CategoriaController::class, 'produto'])->name('categorias.produto');
            // Route::put('/editar/{categoriaId}',[CategoriaController::class, 'editar'])->name('categorias.editar');
            Route::put('/editar/{categoriaId}',[CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
            Route::post('/status/{categoriaId}',[CategoriaController::class, 'status'])->name('categorias.status');
            Route::post('/produto/status/{produtoId}',[ProdutoController::class, 'status'])->name('produtos.status');
        });

        Route::prefix('/produto')->group(function() {
            // Route::get('/',[ProdutoController::class, 'produtos'])->name('produtos.inicio');
            Route::get('/index',[ProdutoController::class, 'Index'])->name('produtos.index');
            // Route::get('/cadastro',[ProdutoController::class, 'cadastro'])->name('produtos.cadastro');
            Route::post('/cadastro',[ProdutoController::class, 'inserirCadastro']);
            Route::get('/buscar-produto',[ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
            Route::get('/editar/{produtoId}',[ProdutoController::class, 'editar'])->name('produtos.editar');
            Route::put('/editar/{produtoId}',[ProdutoController::class, 'salvarEditar'])->name('produtos.salvarEditar');
            Route::post('/status/{produtoId}',[ProdutoController::class, 'status'])->name('produtos.status');
        });

        Route::prefix('/estoque')->group(function() {
            Route::get('/index',[EstoqueController::class, 'Index'])->name('estoque.index');
            // Route::get('/cadastro',[EstoqueController::class, 'Cadastro'])->name('estoque.cadastro');
            Route::post('/cadastro',[EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
            Route::get('/buscar-estoque',[EstoqueController::class, 'buscar'])->name('estoque.buscar');
            // Route::get('/editar/{estoqueId}',[EstoqueController::class, 'editar'])->name('estoque.editar');
            Route::post('/editar/{estoqueId}',[EstoqueController::class, 'salvarEditar'])->name('estoque.salvarEditar');
            Route::post('/status/{estoqueId}',[EstoqueController::class, 'status'])->name('estoque.status');
            Route::get('/quantidade/{estoqueId}/{operacao}',[EstoqueController::class, 'atualizarEstoque'])->name('estoque.quantidade');
            Route::get('grafico-filtro',[EstoqueController::class, 'graficoFiltro'])->name('estoque.graficoFiltro');
            Route::get('/grafico',[GraficosApiController::class, 'months'])->name('months');

            Route::get('/historico',[EstoqueController::class, 'historico'])->name('estoque.historico')->middleware('can:permissao');
        });

        Route::prefix('/fornecedor')->group(function() {
            Route::get('/index',[FornecedorController::class, 'index'])->name('fornecedor.index');
            Route::get('/cadastro',[FornecedorController::class, 'Cadastro'])->name('fornecedor.cadastro');
            Route::post('/cadastro',[FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro');
            Route::get('/cidade/{estado}',[FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
            Route::get('/buscar-fornecedor',[FornecedorController::class, 'Buscar'])->name('fornecedor.buscar');
            // Route::get('/editar/{fornecedorId}',[FornecedorController::class, 'editar'])->name('fornecedor.editar');
            Route::put('/editar/{fornecedorId}',[FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar');
            Route::post('/status/{fornecedorId}',[FornecedorController::class, 'status'])->name('fornecedor.status');
        });

        Route::prefix('/marca')->group(function() {
            Route::get('/index',[MarcaController::class, 'index'])->name('marca.index');
            Route::post('/cadastro',[MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
            Route::get('/buscar-marca',[MarcaController::class, 'Buscar'])->name('marca.buscar');
            Route::get('/editar/{marcaId}',[MarcaController::class, 'editar'])->name('marca.editar');
            Route::put('/editar/{marcaId}',[MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
            Route::post('/status/{marcaId}',[MarcaController::class, 'status'])->name('marca.status');
        });

        Route::prefix('/usuario')->middleware('can:permissao')->group(function() {
            Route::get('/index', [UsuarioController::class , 'index'])->name('usuario.index');
            Route::get('/cadastro', [UsuarioController::class , 'cadastro'])->name('usuario.cadastro');
            Route::post('/cadastro',[UsuarioController::class, 'inserirusuario'])->name('usuario.inserirUsuario');
            Route::get('/editar/{userId}', [UsuarioController::class , 'editar'])->name('usuario.editar');
            Route::post('/status/{userId}',[UsuarioController::class, 'status'])->name('usuario.status');
            Route::put('/editar/{userid}',[UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar');
            Route::get('/buscar-usuario',[UsuarioController::class, 'Buscar'])->name('usuario.buscar');
        });

      
    });

  
    Route::prefix('/unidades')->group(function() {
        Route::get('/index', [UnidadeController::class , 'index'])->name('unidades.index');
        Route::get('/cadastro',[UnidadeController::class, 'cadastro'])->name('unidades.cadastro');
        Route::post('/cadastro',[UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade',[UnidadeController::class, 'Buscar'])->name('unidades.buscar');
        Route::get('/editar/{unidadeId}',[UnidadeController::class, 'editar'])->name('unidades.editar');
        Route::post('/editar/{unidadeId}',[UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{unidadeId}',[UnidadeController::class, 'status'])->name('unidades.status');
    });
    
    Route::post('login', [AuthController::class, 'login']);
    Route::get('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
    
    Route::post('register', [AuthController::class, 'register']);
    });
});


