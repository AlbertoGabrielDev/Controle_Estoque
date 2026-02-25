<?php

use App\Http\Controllers\BotWhatsappController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\TaxRuleController;
use App\Http\Controllers\WhatsAppContactsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RoleController;
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
    Route::prefix('/cadastros')->group(function () {
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
