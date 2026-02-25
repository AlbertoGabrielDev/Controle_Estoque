<?php

use App\Http\Controllers\BotWhatsappController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CustomerSegmentController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\TaxRuleController;
use App\Http\Controllers\WhatsAppContactsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\UnidadeMedidaController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CentroCustoController;
use App\Http\Controllers\ContaContabilController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\SpreadsheetController;
use App\Http\Controllers\SalesSettingsController;
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
        Route::get('/', [CategoriaController::class, 'inicio'])->name('categoria.inicio')->middleware('check.permission:view_post,categoria');
        Route::get('/index', [CategoriaController::class, 'index'])->name('categoria.index')->middleware('check.permission:view_post,categoria');
        Route::get('/data', [CategoriaController::class, 'data'])->name('categoria.data')->middleware('check.permission:view_post,categoria');
        Route::get('/cadastro', [CategoriaController::class, 'cadastro'])->name('categoria.cadastro')->middleware('check.permission:create_post,categoria');
        Route::post('/cadastro', [CategoriaController::class, 'inserirCategoria'])->name('categoria.inserirCategoria');
        Route::get('/produto/{categoria}', [CategoriaController::class, 'produto'])->name('categorias.produto');
        Route::get('/editar/{categoriaId}', [CategoriaController::class, 'editar'])->name('categorias.editar')->middleware('check.permission:edit_post,categoria');
        Route::post('/editar/{categoriaId}', [CategoriaController::class, 'salvarEditar'])->name('categorias.salvarEditar');
        Route::delete('/{categoriaId}', [CategoriaController::class, 'destroy'])->name('categorias.destroy')->middleware('check.permission:edit_post,categoria');
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

    Route::prefix('/marca')->group(function () {
        Route::get('/index', [MarcaController::class, 'index'])->name('marca.index')->middleware('check.permission:view_post,marca');
        Route::get('/data', [MarcaController::class, 'data'])->name('marca.data')->middleware('check.permission:view_post,marca');
        Route::get('/cadastro', [MarcaController::class, 'cadastro'])->name('marca.cadastro')->middleware('check.permission:create_post,marca');
        Route::post('/cadastro', [MarcaController::class, 'inserirMarca'])->name('marca.inserirMarca');
        Route::get('/buscar-marca', [MarcaController::class, 'buscar'])->name('marca.buscar');
        Route::get('/editar/{marcaId}', [MarcaController::class, 'editar'])->name('marca.editar')->middleware('check.permission:edit_post,marca');
        Route::post('/editar/{marcaId}', [MarcaController::class, 'salvarEditar'])->name('marca.salvarEditar');
        Route::post('/status/{modelName}/{id}', [MarcaController::class, 'updateStatus'])->name('marca.status')->middleware('check.permission:status,marca');
    });

    Route::prefix('/usuario')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index')->middleware('check.permission:view_post,perfil');
        Route::get('/data', [UsuarioController::class, 'data'])->name('usuario.data')->middleware('check.permission:view_post,perfil');
        Route::get('/cadastro', [UsuarioController::class, 'cadastro'])->name('usuario.cadastro')->middleware('check.permission:create_post,perfil');
        Route::post('/cadastro', [UsuarioController::class, 'inserirUsuario'])->name('usuario.inserirUsuario');
        Route::get('/editar/{userId}', [UsuarioController::class, 'editar'])->name('usuario.editar')->middleware('check.permission:edit_post,perfil');
        Route::post('/status/{modelName}/{id}', [UsuarioController::class, 'updateStatus'])->name('usuario.status')->middleware('check.permission:status,perfil');
        Route::put('/editar/{userId}', [UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar');
        Route::get('/buscar-usuario', [UsuarioController::class, 'buscar'])->name('usuario.buscar');
    });
    Route::prefix('/unidades')->group(function () {
        Route::get('/index', [UnidadeController::class, 'index'])->name('unidade.index')->middleware('check.permission:view_post,unidades');
        Route::get('/data', [UnidadeController::class, 'data'])->name('unidade.data')->middleware('check.permission:view_post,unidades');
        Route::get('/cadastro', [UnidadeController::class, 'cadastro'])->name('unidades.cadastro')->middleware('check.permission:create_post,unidades');
        Route::post('/cadastro', [UnidadeController::class, 'inserirUnidade'])->name('unidades.inserirUnidade');
        Route::get('/buscar-unidade', [UnidadeController::class, 'buscar'])->name('unidade.buscar');
        Route::get('/editar/{unidadeId}', [UnidadeController::class, 'editar'])->name('unidades.editar')->middleware('check.permission:edit_post,unidades');
        Route::post('/editar/{unidadeId}', [UnidadeController::class, 'salvarEditar'])->name('unidades.salvarEditar');
        Route::post('/status/{modelName}/{id}', [UnidadeController::class, 'updateStatus'])->name('unidades.status');
    });

    Route::prefix('/cadastros')->group(function () {
        Route::prefix('/unidades-medida')->name('unidades_medida.')->group(function () {
            Route::get('/', [UnidadeMedidaController::class, 'index'])->name('index');
            Route::get('/data', [UnidadeMedidaController::class, 'data'])->name('data');
            Route::get('/create', [UnidadeMedidaController::class, 'create'])->name('create');
            Route::post('/', [UnidadeMedidaController::class, 'store'])->name('store');
            Route::get('/{unidade_medida}/edit', [UnidadeMedidaController::class, 'edit'])->name('edit');
            Route::put('/{unidade_medida}', [UnidadeMedidaController::class, 'update'])->name('update');
            Route::delete('/{unidade_medida}', [UnidadeMedidaController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [UnidadeMedidaController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/itens')->name('itens.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/data', [ItemController::class, 'data'])->name('data');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/', [ItemController::class, 'store'])->name('store');
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [ItemController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/centros-custo')->name('centros_custo.')->group(function () {
            Route::get('/', [CentroCustoController::class, 'index'])->name('index');
            Route::get('/data', [CentroCustoController::class, 'data'])->name('data');
            Route::get('/create', [CentroCustoController::class, 'create'])->name('create');
            Route::post('/', [CentroCustoController::class, 'store'])->name('store');
            Route::get('/{centro_custo}/edit', [CentroCustoController::class, 'edit'])->name('edit');
            Route::put('/{centro_custo}', [CentroCustoController::class, 'update'])->name('update');
            Route::delete('/{centro_custo}', [CentroCustoController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [CentroCustoController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/contas-contabeis')->name('contas_contabeis.')->group(function () {
            Route::get('/', [ContaContabilController::class, 'index'])->name('index');
            Route::get('/data', [ContaContabilController::class, 'data'])->name('data');
            Route::get('/create', [ContaContabilController::class, 'create'])->name('create');
            Route::post('/', [ContaContabilController::class, 'store'])->name('store');
            Route::get('/{conta_contabil}/edit', [ContaContabilController::class, 'edit'])->name('edit');
            Route::put('/{conta_contabil}', [ContaContabilController::class, 'update'])->name('update');
            Route::delete('/{conta_contabil}', [ContaContabilController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [ContaContabilController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('/despesas')->name('despesas.')->group(function () {
            Route::get('/', [DespesaController::class, 'index'])->name('index');
            Route::get('/data', [DespesaController::class, 'data'])->name('data');
            Route::get('/create', [DespesaController::class, 'create'])->name('create');
            Route::post('/', [DespesaController::class, 'store'])->name('store');
            Route::get('/{despesa}/edit', [DespesaController::class, 'edit'])->name('edit');
            Route::put('/{despesa}', [DespesaController::class, 'update'])->name('update');
            Route::delete('/{despesa}', [DespesaController::class, 'destroy'])->name('destroy');
            Route::post('/status/{modelName}/{id}', [DespesaController::class, 'updateStatus'])->name('status');
        });
    });

    Route::prefix('/roles')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('roles.index')->middleware('check.permission:view_post,permissao');
        Route::get('/data', [RoleController::class, 'data'])->name('roles.data')->middleware('check.permission:view_post,permissao');
        Route::get('/cadastro', [RoleController::class, 'cadastro'])->name('roles.cadastro')->middleware('check.permission:create_post,permissao');
        Route::post('/cadastro', [RoleController::class, 'inserirRole'])->name('roles.inserirRole');
        Route::get('/buscar-unidade', [RoleController::class, 'buscar'])->name('roles.buscar');
        Route::get('/editar/{roleId}', [RoleController::class, 'editar'])->name('roles.editar')->middleware('check.permission:edit_post,permissao');
        Route::put('/editar/{roleId}', [RoleController::class, 'salvarEditar'])->name('roles.salvarEditar')->middleware('check.permission:edit_post,permissao');
        Route::post('/status/{modelName}/{id}', [RoleController::class, 'updateStatus'])->name('roles.status');
    });

    Route::prefix('/spreadsheet')->name('spreadsheet.')->group(function () {
        Route::post('/upload', [SpreadsheetController::class, 'upload'])->name('upload');
        Route::get('/data/{filename}', [SpreadsheetController::class, 'readFile'])->name('data');
        Route::get('/', [SpreadsheetController::class, 'index'])->name('index');
        Route::post('/compare', [SpreadsheetController::class, 'compare'])->name('compare');
    });



    Route::prefix('whatsapp')->group(function () {
        Route::get('/contacts', [WhatsAppContactsController::class, 'index'])->name('whatsapp.contacts');
        Route::post('/labels', [WhatsAppContactsController::class, 'createLabel'])->name('whatsapp.labels.store');
        Route::post('/labels/assign', [WhatsAppContactsController::class, 'assignLabel'])->name('whatsapp.labels.assign');
        Route::delete('/labels/{id}', [WhatsAppContactsController::class, 'deleteLabel'])->name('whatsapp.labels.destroy');
        Route::get('/verdurao/whatsapp/labels/{id}/members', [WhatsAppContactsController::class, 'labelMembers'])->name('whatsapp.labels.members');
    });

    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/vendas', [SalesSettingsController::class, 'index'])->name('vendas')->middleware('check.permission:view_post,config_vendas');
        Route::put('/vendas', [SalesSettingsController::class, 'update'])->name('vendas.update')->middleware('check.permission:edit_post,config_vendas');
    });

    Route::prefix('taxes')->name('taxes.')->group(function () {
        Route::get('/', [TaxRuleController::class, 'index'])->name('index')->middleware('check.permission:view_post,taxas');
        Route::get('/data', [TaxRuleController::class, 'data'])->name('data')->middleware('check.permission:view_post,taxas');
        Route::get('/create', [TaxRuleController::class, 'create'])->name('create')->middleware('check.permission:create_post,taxas');
        Route::post('/', [TaxRuleController::class, 'store'])->name('store')->middleware('check.permission:create_post,taxas');
        Route::get('/{rule}', [TaxRuleController::class, 'edit'])->name('edit')->middleware('check.permission:edit_post,taxas');
        Route::put('/{rule}', [TaxRuleController::class, 'update'])->name('update')->middleware('check.permission:edit_post,taxas');
        Route::delete('/{rule}', [TaxRuleController::class, 'destroy'])->name('destroy')->middleware('check.permission:edit_post,taxas');
    });
});

Route::get('login', [UsuarioController::class, 'unidade'])->name('login');
Route::get('register', action: [UsuarioController::class, 'unidadeRegister'])->name('register');
