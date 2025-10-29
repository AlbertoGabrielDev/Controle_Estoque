<?php

use App\Http\Controllers\Api\GraficosApiController;
use App\Http\Controllers\BotWhatsappController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CustomerSegmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\TaxRuleController;
use App\Http\Controllers\WhatsAppContactsController;
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
use App\Http\Controllers\SpreadsheetController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth'])
    ->prefix('/verdurao/wpp')
    ->name('wpp.')
    ->group(function () {
        Route::prefix('/bot')->group(function () {
            Route::get('/', [BotWhatsappController::class, 'index'])->name('bot.index');
            Route::get('/dashboard', [BusinessController::class, 'dasboard'])->name('bot.dashboard');
            Route::post('/whatsapp/send-mass', [BotWhatsappController::class, 'sendMass']);

            Route::get('/business-extractor', [BusinessController::class, 'index'])->name('business.index');
            Route::post('/api/business/extract', [BusinessController::class, 'extractFromUrl'])->name('business.extract');
            Route::post('/business/export', [BusinessController::class, 'exportToCsv'])->name('business.export');

            Route::prefix('/configuracoes')->group(function () {
                Route::get('/modelos-mensagem', [MessageTemplateController::class, 'index'])->name('configuracoes.modelos-mensagem');
                Route::post('/modelos-mensagem', [MessageTemplateController::class, 'store']);
                Route::put('/modelos-mensagem/{messageTemplate}', [MessageTemplateController::class, 'update']);
                Route::delete('/modelos-mensagem/{messageTemplate}', [MessageTemplateController::class, 'destroy']);
            });
        });
    });


Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {

    Route::prefix('/categoria')->group(function () {
        Route::get('/', [CategoriaController::class, 'Inicio'])->name('categoria.inicio')->middleware('check.permission:view_post,categoria');
        Route::get('/index', [CategoriaController::class, 'Index'])->name('categoria.index')->middleware('check.permission:view_post,categoria');
        Route::get('/cadastro', [CategoriaController::class, 'cadastro'])->name('categoria.cadastro')->middleware('check.permission:create_post,categoria');
        Route::post('/cadastro', [CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}', [CategoriaController::class, 'produto'])->name('categorias.produto');
        Route::get('/editar/{categoriaId}', [CategoriaController::class, 'editar'])->name('categorias.editar')->middleware('check.permission:edit_post,categoria');
        Route::post('/editar/{categoriaId}', [CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
        Route::post('/status/{modelName}/{id}', [CategoriaController::class, 'updateStatus'])->name('categoria.status');
        Route::post('/produto/status/{produtoId}', [ProdutoController::class, 'status'])->name('produtos.status');
    });

    Route::prefix('/clientes')->group(function () {
        // Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/data', [ClienteController::class, 'data'])->name('clientes.data');

        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/status/{modelName}/{id}', [ClienteController::class, 'updateStatus'])->middleware('check.permission:status,clientes')->name('cliente.status');
        Route::get('/clientes-autocomplete', [ClienteController::class, 'autocomplete'])->name('clientes.autocomplete');

        // Segmentos
        Route::get('/segmentos', [CustomerSegmentController::class, 'index'])->name('segmentos.index');
        Route::get('/segmentos/data', [CustomerSegmentController::class, 'data'])->name('segmentos.data');
        Route::get('/segmentos/create', [CustomerSegmentController::class, 'create'])->name('segmentos.create');
        Route::post('/segmentos', [CustomerSegmentController::class, 'store'])->name('segmentos.store');
        Route::get('/segmentos/{segment}', [CustomerSegmentController::class, 'edit'])->name('segmentos.edit');
        Route::put('/segmentos/{segment}', [CustomerSegmentController::class, 'update'])->name('segmentos.update');
        Route::delete('/segmentos/{segment}', [CustomerSegmentController::class, 'destroy'])->name('segmentos.destroy');
    });


    Route::prefix('/produtos')->group(function () {
        Route::get('/index', [ProdutoController::class, 'Index'])->name('produtos.index')->middleware('check.permission:view_post,Produtos');
        Route::get('/cadastro', [ProdutoController::class, 'cadastro'])->name('produtos.cadastro')->middleware('check.permission:create_post,Produtos');
        Route::post('/salvar-cadastro', [ProdutoController::class, 'inserirCadastro'])->name('produtos.salvarCadastro');
        Route::get('/buscar-produto', [ProdutoController::class, 'buscarProduto'])->name('produtos.buscar');
        Route::get('/editar/{produtoId}', [ProdutoController::class, 'editar'])->name('produtos.editar')->middleware('check.permission:edit_post,Produtos');
        Route::post('/editar/{produtoId}', [ProdutoController::class, 'salvarEditar'])->name('produtos.salvarEditar');
        Route::post('/status/{modelName}/{id}', [ProdutoController::class, 'updateStatus'])->name('produto.status');
        Route::get('/produtos/data', [ProdutoController::class, 'data'])->name('produtos.data')->middleware(['web', 'auth']);
    });

    Route::prefix('/estoque')->group(function () {
        Route::get('/', [EstoqueController::class, 'Index'])->name('estoque.index')->middleware('check.permission:view_post,estoque');
        Route::get('/cadastro', [EstoqueController::class, 'Cadastro'])->name('estoque.cadastro')->middleware('check.permission:create_post,estoque');
        Route::post('/cadastro', [EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
        Route::get('/buscar-estoque', [EstoqueController::class, 'buscar'])->name('estoque.buscar');
        Route::post('/estoque/calc-impostos', [EstoqueController::class, 'calcImpostos'])->name('estoque.calcImpostos');
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
        Route::post('/cadastro', [FornecedorController::class, 'inserirCadastro'])->name('fornecedor.inserirCadastro')->middleware('check.permission:create_post,fornecedores');
        Route::get('/cidade/{estado}', [FornecedorController::class, 'getCidade'])->name('fornecedor.cidade');
        Route::get('/buscar-fornecedor', [FornecedorController::class, 'Buscar'])->name('fornecedor.buscar');
        Route::get('/editar/{fornecedorId}', [FornecedorController::class, 'editar'])->name('fornecedor.editar')->middleware('check.permission:edit_post,fornecedores');
        Route::post('/editar/{fornecedorId}', [FornecedorController::class, 'salvarEditar'])->name('fornecedor.salvarEditar')->middleware('check.permission:edit_post,fornecedores');
        Route::post('/status/{modelName}/{id}', [FornecedorController::class, 'updateStatus'])->middleware('check.permission:status,fornecedores')->name('fornecedor.status');
    });

    Route::prefix('/marca')->group(function () {
        Route::get('/index', [MarcaController::class, 'index'])->name('marca.index')->middleware('check.permission:view_post,marca');
        Route::get('/cadastro', [MarcaController::class, 'cadastro'])->name('marca.cadastro')->middleware('check.permission:create_post,marca');
        Route::post('/cadastro', [MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
        Route::get('/buscar-marca', [MarcaController::class, 'Buscar'])->name('marca.buscar');
        Route::get('/editar/{marcaId}', [MarcaController::class, 'editar'])->name('marca.editar')->middleware('check.permission:edit_post,marca');
        Route::post('/editar/{marcaId}', [MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
        Route::post('/status/{modelName}/{id}', [MarcaController::class, 'updateStatus'])->name('marca.status')->middleware('check.permission:status,marca');
    });

    Route::prefix('/usuario')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index')->middleware('check.permission:view_post,perfil');
        Route::get('/cadastro', [UsuarioController::class, 'cadastro'])->name('usuario.cadastro')->middleware('check.permission:create_post,perfil');
        Route::post('/cadastro', [UsuarioController::class, 'inserirUsuario'])->name('usuario.inserirUsuario');
        Route::get('/editar/{userId}', [UsuarioController::class, 'editar'])->name('usuario.editar')->middleware('check.permission:edit_post,perfil');
        Route::post('/status/{modelName}/{id}', [UsuarioController::class, 'updateStatus'])->name('usuario.status')->middleware('check.permission:status,perfil');
        Route::put('/editar/{userid}', [UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar');
        Route::get('/buscar-usuario', [UsuarioController::class, 'Buscar'])->name('usuario.buscar');
    });
    Route::prefix('/unidades')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidade.index')->middleware('check.permission:view_post,unidades');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro')->middleware('check.permission:create_post,unidades');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'Buscar'])->name('unidade.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar')->middleware('check.permission:edit_post,unidades');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{modelName}/{id}', [UnidadeController::class, 'updateStatus'])->name('unidades.status');
    });

    Route::prefix('/roles')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('roles.index')->middleware('check.permission:view_post,permissao');
        Route::get('/cadastro', [RoleController::class, 'cadastro'])->name('roles.cadastro')->middleware('check.permission:create_post,permissao');
        Route::post('/cadastro', [RoleController::class, 'inserirRole'])->name('roles.inserirRole');
        Route::get('/buscar-unidade', [RoleController::class, 'Buscar'])->name('roles.buscar');
        Route::get('/editar/{roleId}', [RoleController::class, 'editar'])->name('roles.editar')->middleware('check.permission:edit_post,permissao');
        Route::put('/editar/{rolesId}', [RoleController::class, 'salvarEditar'])->name('roles.salvarEditar')->middleware('check.permission:edit_post,permissao');
        Route::post('/status/{rolesId}', [RoleController::class, 'updateStatus'])->name('roles.status');
    });

    Route::prefix('/vendas')->group(function () {
        Route::get('/', [VendaController::class, 'vendas'])->name('vendas.venda')->middleware('check.permission:view_post,vendas');
        Route::post('/buscar-produto', [VendaController::class, 'buscarProduto'])->name('buscar.produto');
        Route::post('/verificar-estoque', [VendaController::class, 'verificarEstoque'])->name('verificar.estoque');
        Route::post('/registrar-venda', [VendaController::class, 'registrarVenda'])->name('registrar.venda');
        Route::post('/carrinho', [VendaController::class, 'carrinho'])->name('carrinho.venda'); // <-- era /carrinho/carrinho
        Route::post('/carrinho/adicionar', [VendaController::class, 'adicionarItem'])->name('adicionar.venda');
        Route::post('/carrinho/quantidade', [VendaController::class, 'atualizarQuantidade'])->name('atualizar_quantidade.venda');
        Route::post('/carrinho/remover', [VendaController::class, 'removerItem'])->name('remover.venda');
        Route::get('/vendas', [VendaController::class, 'historicoVendas'])->name('vendas.historico_vendas')->middleware('check.permission:view_post,vendas');
        Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('check.permission:view_post,vendas');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('vendas.calendar');
    });

    Route::prefix('/spreadsheet')->group(function () {
        Route::post('/upload', [SpreadsheetController::class, 'upload']);
        Route::get('/data/{filename}', [SpreadsheetController::class, 'readFile']);
        Route::get('/', [SpreadsheetController::class, 'index']);
        Route::post('/compare', [SpreadsheetController::class, 'compare']);
    });



    Route::prefix('whatsapp')->group(function () {
        Route::get('/contacts', [WhatsAppContactsController::class, 'index'])->name('whatsapp.contacts');
        Route::post('/labels', [WhatsAppContactsController::class, 'createLabel'])->name('whatsapp.labels.store');
        Route::post('/labels/assign', [WhatsAppContactsController::class, 'assignLabel'])->name('whatsapp.labels.assign');
        Route::delete('/labels/{id}', [WhatsAppContactsController::class, 'deleteLabel'])->name('whatsapp.labels.destroy');
        Route::get('/verdurao/whatsapp/labels/{id}/members', [WhatsAppContactsController::class, 'labelMembers'])->name('whatsapp.labels.members');
    });

    Route::prefix('taxes')->name('taxes.')->group(function () {
        Route::get('/', [TaxRuleController::class, 'index'])->name('index');
        Route::get('/data', [TaxRuleController::class, 'data'])->name('data');
        Route::get('/create', [TaxRuleController::class, 'create'])->name('create');
        Route::post('/', [TaxRuleController::class, 'store'])->name('store');
        Route::get('/{rule}', [TaxRuleController::class, 'edit'])->name('edit');
        Route::put('/{rule}', [TaxRuleController::class, 'update'])->name('update');
        Route::delete('/{rule}', [TaxRuleController::class, 'destroy'])->name('destroy');
    });
});

Route::get('login', [UsuarioController::class, 'unidade'])->name('login');
Route::get('register', action: [UsuarioController::class, 'unidadeRegister'])->name('register');
