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
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {

    // Route::get('/vue', function (){
    //    return view('welcome');
    // });

    Route::prefix('/categoria')->group(function () {
        Route::get('/', [CategoriaController::class, 'Inicio'])->name('categoria.inicio');
        Route::get('/index', [CategoriaController::class, 'Index'])->name('categoria.index');
        Route::get('/cadastro', [CategoriaController::class, 'cadastro'])->name('categoria.cadastro');
        Route::post('/cadastro', [CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}', [CategoriaController::class, 'produto'])->name('categorias.produto');
        Route::get('/editar/{categoriaId}', [CategoriaController::class, 'editar'])->name('categorias.editar');
        Route::post('/editar/{categoriaId}', [CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
        Route::post('/status/{categoriaId}', [CategoriaController::class, 'status'])->name('categorias.status');
        Route::post('/produto/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });

    Route::prefix('/produtos')->group(function () {
        Route::get('/', [ProdutoController::class, 'produtos'])->name('produtos.inicio');
        Route::get('/index', [ProdutoController::class, 'Index'])->name('produtos.index');
        Route::get('/cadastro', [ProdutoController::class, 'cadastro'])->name('produtos.cadastro');
        Route::post('/salvar-cadastro', [ProdutoController::class, 'inserirCadastro'])->name('produtos.salvarCadastro');
        Route::get('/buscar-produto', [ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
        Route::get('/editar/{produtoId}', [ProdutoController::class, 'editar'])->name('produtos.editar');
        Route::post('/editar/{produtoId}', [ProdutoController::class, 'salvarEditar'])->name('produtos.salvarEditar');
        Route::post('/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });

    Route::prefix('/estoque')->group(function () {
        Route::get('/', [EstoqueController::class, 'Index'])->name('estoque.index');
        Route::get('/cadastro', [EstoqueController::class, 'Cadastro'])->name('estoque.cadastro');
        Route::post('/cadastro', [EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
        Route::get('/buscar-estoque', [EstoqueController::class, 'buscar'])->name('estoque.buscar');
        Route::get('/editar/{estoqueId}', [EstoqueController::class, 'editar'])->name('estoque.editar');
        Route::post('/editar/{estoqueId}', [EstoqueController::class, 'salvarEditar'])->name('estoque.salvarEditar');
        Route::post('/status/{estoqueId}', [EstoqueController::class, 'status'])->name('estoque.status');
        Route::get('/quantidade/{estoqueId}/{operacao}', [EstoqueController::class, 'atualizarEstoque'])->name('estoque.quantidade');
        Route::get('grafico-filtro', [EstoqueController::class, 'graficoFiltro'])->name('estoque.graficoFiltro');
        Route::get('/grafico', [GraficosApiController::class, 'months'])->name('months');

        Route::get('/historico', [EstoqueController::class, 'historico'])->name('estoque.historico')->middleware('can:permissao');
    });

    Route::prefix('/fornecedor')->group(function () {
        Route::get('/', [FornecedorController::class, 'index'])->name('fornecedor.index');
        Route::get('/cadastro', [FornecedorController::class, 'Cadastro'])->name('fornecedor.cadastro');
        Route::post('/cadastro', [FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro');
        Route::get('/cidade/{estado}', [FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor', [FornecedorController::class, 'Buscar'])->name('fornecedor.buscar');
        Route::get('/editar/{fornecedorId}', [FornecedorController::class, 'editar'])->name('fornecedor.editar');
        Route::post('/editar/{fornecedorId}', [FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar');
        Route::post('/status/{fornecedorId}', [FornecedorController::class, 'status'])->name('fornecedor.status');
    });

    Route::prefix('/marca')->group(function () {
        Route::get('/index', [MarcaController::class, 'index'])->name('marca.index');
        Route::get('/cadastro', [MarcaController::class, 'cadastro'])->name('marca.cadastro');
        Route::post('/cadastro', [MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
        Route::get('/buscar-marca', [MarcaController::class, 'Buscar'])->name('marca.buscar');
        Route::get('/editar/{marcaId}', [MarcaController::class, 'editar'])->name('marca.editar');
        Route::post('/editar/{marcaId}', [MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
        Route::post('/status/{marcaId}', [MarcaController::class, 'status'])->name('marca.status');
    });

    Route::prefix('/usuario')->middleware('can:view_post')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('/cadastro', [UsuarioController::class, 'cadastro'])->name('usuario.cadastro')->middleware('can:create_post');
        Route::post('/cadastro', [UsuarioController::class, 'inserirUsuario'])->name('usuario.inserirUsuario')->middleware('can:create_post');
        Route::get('/editar/{userId}', [UsuarioController::class, 'editar'])->name('usuario.editar')->middleware('can:edit_post');
        Route::post('/status/{userId}', [UsuarioController::class, 'status'])->name('usuario.status');
        Route::put('/editar/{userid}', [UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar')->middleware('can:edit_post');
        Route::get('/buscar-usuario', [UsuarioController::class, 'Buscar'])->name('usuario.buscar');
    });
    Route::prefix('/unidades')->middleware('can:permissao')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidades.index');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'Buscar'])->name('unidades.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{unidadeId}', [UnidadeController::class, 'status'])->name('unidades.status');
    });

    Route::prefix('/roles')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/cadastro', [RoleController::class, 'cadastro'])->name('roles.cadastro');
        Route::post('/cadastro', [RoleController::class, 'inserirRole'])->name('roles.inserirRole');
        Route::get('/buscar-unidade', [RoleController::class, 'Buscar'])->name('roles.buscar');
        Route::get('/editar/{roleId}', [RoleController::class, 'editar'])->name('roles.editar');
        Route::post('/editar/{rolesId}', [RoleController::class, 'salvarEditar'])->name('roles.salvarEditar');
        Route::post('/status/{rolesId}', [RoleController::class, 'status'])->name('roles.status');
    });

    Route::prefix('/vendas')->group(function () {
        Route::post('/buscar-produto', [VendaController::class, 'buscarProduto'])->name('buscar.produto');
        Route::post('/verificar-estoque', [VendaController::class, 'verificarEstoque'])->name('verificar.estoque');
        Route::post('/registrar-venda', [VendaController::class, 'registrarVenda'])->name('registrar.venda');
        Route::get('/', [VendaController::class, 'vendas'])->name('vendas.venda');
        Route::get('/listar', [VendaController::class, 'listar']);
        Route::get('/vendas', [VendaController::class, 'historicoVendas'])->name('vendas.historico_vendas');
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
