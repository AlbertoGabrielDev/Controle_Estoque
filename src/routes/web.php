<?php

use App\Http\Controllers\BotWhatsappController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\WhatsAppContactsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RoleController;
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

});

Route::get('login', [UsuarioController::class, 'unidade'])->name('login');
Route::get('register', action: [UsuarioController::class, 'unidadeRegister'])->name('register');
