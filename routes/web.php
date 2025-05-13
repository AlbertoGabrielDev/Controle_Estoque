<?php

use App\Http\Controllers\Api\GraficosApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\VendaController;
use App\Providers\FortifyServiceProvider;
use App\Http\Controllers\SpreadsheetController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {

    // Route::get('/vue', function (){
    //    return view('welcome');
    // });

    Route::prefix('/categoria')->group(function () {
        Route::get('/', [CategoriaController::class, 'Inicio'])->name('categoria.inicio')->middleware('check.permission:view_post,categoria');
        Route::get('/index', [CategoriaController::class, 'Index'])->name('categoria.index')->middleware('check.permission:view_post,categoria');
        Route::get('/cadastro', [CategoriaController::class, 'cadastro'])->name('categoria.cadastro')->middleware('check.permission:create_post,categoria');
        Route::post('/cadastro', [CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}', [CategoriaController::class, 'produto'])->name('categorias.produto');
        Route::get('/editar/{categoriaId}', [CategoriaController::class, 'editar'])->name('categorias.editar')->middleware('check.permission:edit_post,categoria');
        Route::post('/editar/{categoriaId}', [CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
        Route::post('/status/{categoriaId}', [CategoriaController::class, 'status'])->name('categorias.status');
        Route::post('/produto/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });

    Route::prefix('/produtos')->group(function () {
        Route::get('/index', [ProdutoController::class, 'Index'])->name('produtos.index')->middleware('check.permission:view_post,Produtos');
        Route::get('/cadastro', [ProdutoController::class, 'cadastro'])->name('produtos.cadastro')->middleware('check.permission:create_post,Produtos');
        Route::post('/salvar-cadastro', [ProdutoController::class, 'inserirCadastro'])->name('produtos.salvarCadastro');
        Route::get('/buscar-produto', [ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
        Route::get('/editar/{produtoId}', [ProdutoController::class, 'editar'])->name('produtos.editar')->middleware('check.permission:edit_post,Produtos');
        Route::post('/editar/{produtoId}', [ProdutoController::class, 'salvarEditar'])->name('produtos.salvarEditar');
        Route::post('/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });

    Route::prefix('/estoque')->group(function () {
        Route::get('/', [EstoqueController::class, 'Index'])->name('estoque.index')->middleware('check.permission:view_post,estoque');
        Route::get('/cadastro', [EstoqueController::class, 'Cadastro'])->name('estoque.cadastro')->middleware('check.permission:create_post,estoque');
        Route::post('/cadastro', [EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
        Route::get('/buscar-estoque', [EstoqueController::class, 'buscar'])->name('estoque.buscar');
        Route::get('/editar/{estoqueId}', [EstoqueController::class, 'editar'])->name('estoque.editar')->middleware('check.permission:edit_post,estoque');
        Route::put('/editar/{estoqueId}', [EstoqueController::class, 'salvarEditar'])->name('estoque.salvarEditar');
        Route::post('/status/{modelName}/{id}', [EstoqueController::class, 'updateStatus'])->middleware('check.permission:status,estoque')->name('estoque.status');
        Route::get('/quantidade/{estoqueId}/{operacao}', [EstoqueController::class, 'atualizarEstoque'])->name('estoque.quantidade');
        Route::get('grafico-filtro', [EstoqueController::class, 'graficoFiltro'])->name('estoque.graficoFiltro');
        Route::get('/grafico', [GraficosApiController::class, 'months'])->name('months');

        Route::get('/historico', [EstoqueController::class, 'historico'])->name('estoque.historico')->middleware('check.permission:view_post,historico');
    });

    Route::prefix('/fornecedor')->group(function () {
        Route::get('/', [FornecedorController::class, 'index'])->name('fornecedor.index')->middleware('check.permission:view_post,fornecedores');
        Route::get('/cadastro', [FornecedorController::class, 'Cadastro'])->name('fornecedor.cadastro')->middleware('check.permission:create_post,fornecedores');
        Route::post('/cadastro', [FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro');
        Route::get('/cidade/{estado}', [FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor', [FornecedorController::class, 'Buscar'])->name('fornecedor.buscar');
        Route::get('/editar/{fornecedorId}', [FornecedorController::class, 'editar'])->name('fornecedor.editar')->middleware('check.permission:edit_post,fornecedores');
        Route::post('/editar/{fornecedorId}', [FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar');
        Route::post('/status/{modelName}/{id}', [FornecedorController::class, 'updateStatus'])->middleware('check.permission:status,fornecedores')->name('fornecedor.status');
    });

    Route::prefix('/marca')->group(function () {
        Route::get('/index', [MarcaController::class, 'index'])->name('marca.index')->middleware('check.permission:view_post,marca');
        Route::get('/cadastro', [MarcaController::class, 'cadastro'])->name('marca.cadastro')->middleware('check.permission:create_post,marca');
        Route::post('/cadastro', [MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
        Route::get('/buscar-marca', [MarcaController::class, 'Buscar'])->name('marca.buscar');
        Route::get('/editar/{marcaId}', [MarcaController::class, 'editar'])->name('marca.editar')->middleware('check.permission:edit_post,marca');
        Route::post('/editar/{marcaId}', [MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
        Route::post('/status/{marcaId}', [MarcaController::class, 'status'])->name('marca.status');
    });

    Route::prefix('/usuario')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index')->middleware('check.permission:view_post,perfil');
        Route::get('/cadastro', [UsuarioController::class, 'cadastro'])->name('usuario.cadastro')->middleware('check.permission:create_post,perfil');
        Route::post('/cadastro', [UsuarioController::class, 'inserirUsuario'])->name('usuario.inserirUsuario');
        Route::get('/editar/{userId}', [UsuarioController::class, 'editar'])->name('usuario.editar')->middleware('check.permission:edit_post,perfil');
        Route::post('/status/{userId}', [UsuarioController::class, 'status'])->name('usuario.status')->middleware('check.permission:status,perfil');
        Route::put('/editar/{userid}', [UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar');
        Route::get('/buscar-usuario', [UsuarioController::class, 'Buscar'])->name('usuario.buscar');
    });
    Route::prefix('/unidades')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidade.index')->middleware('check.permission:view_post,unidades');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro')->middleware('check.permission:create_post,unidades');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'Buscar'])->name('unidades.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar')->middleware('check.permission:edit_post,unidades');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{unidadeId}', [UnidadeController::class, 'updateStatus'])->name('unidades.status');
    });

    Route::prefix('/roles')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('roles.index')->middleware('check.permission:view_post,roles');
        Route::get('/cadastro', [RoleController::class, 'cadastro'])->name('roles.cadastro')->middleware('check.permission:create_post,roles');
        Route::post('/cadastro', [RoleController::class, 'inserirRole'])->name('roles.inserirRole');
        Route::get('/buscar-unidade', [RoleController::class, 'Buscar'])->name('roles.buscar');
        Route::get('/editar/{roleId}', [RoleController::class, 'editar'])->name('roles.editar')->middleware('check.permission:edit_post,roles');
        Route::put('/editar/{rolesId}', [RoleController::class, 'salvarEditar'])->name('roles.salvarEditar');
        Route::post('/status/{rolesId}', [RoleController::class, 'status'])->name('roles.status');
    });

    Route::prefix('/vendas')->group(function () {
        Route::get('/', [VendaController::class, 'vendas'])->name('vendas.venda')->middleware('check.permission:view_post,vendas');
        Route::post('/buscar-produto', [VendaController::class, 'buscarProduto'])->name('buscar.produto');
        Route::post('/verificar-estoque', [VendaController::class, 'verificarEstoque'])->name('verificar.estoque');
        Route::post('/registrar-venda', [VendaController::class, 'registrarVenda'])->name('registrar.venda');
        Route::get('/vendas', [VendaController::class, 'historicoVendas'])->name('vendas.historico_vendas')->middleware('check.permission:view_post,vendas');
    });

    Route::prefix('/spreadsheet')->group(function () {
        Route::post('/upload', [SpreadsheetController::class, 'upload']);
        Route::get('/data/{filename}', [SpreadsheetController::class, 'readFile']);
        Route::get('/', [SpreadsheetController::class, 'index']);
        Route::post('/compare', [SpreadsheetController::class, 'compare']);

    });
});

Route::get('login', [UsuarioController::class, 'unidade'])->name('login');
Route::get('register', [UsuarioController::class, 'unidadeRegister'])->name('register');
