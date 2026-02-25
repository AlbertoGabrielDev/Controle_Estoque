<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\VendaController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/vendas')->group(function () {
        Route::get('/', [VendaController::class, 'vendas'])->name('vendas.venda')->middleware('check.permission:view_post,vendas');
        Route::post('/buscar-produto', [VendaController::class, 'buscarProduto'])->name('buscar.produto');
        Route::post('/verificar-estoque', [VendaController::class, 'verificarEstoque'])->name('verificar.estoque');
        Route::post('/registrar-venda', [VendaController::class, 'registrarVenda'])->name('registrar.venda');
        Route::post('/carrinho', [VendaController::class, 'carrinho'])->name('carrinho.venda');
        Route::post('/carrinho/adicionar', [VendaController::class, 'adicionarItem'])->name('adicionar.venda');
        Route::post('/carrinho/quantidade', [VendaController::class, 'atualizarQuantidade'])->name('atualizar_quantidade.venda');
        Route::post('/carrinho/remover', [VendaController::class, 'removerItem'])->name('remover.venda');
        Route::get('/vendas', [VendaController::class, 'historicoVendas'])->name('vendas.historico_vendas')->middleware('check.permission:view_post,vendas');
        // Dashboard/Calendar permanecem em controllers compartilhados nesta etapa.
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('check.permission:view_post,vendas');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('vendas.calendar');
    });
});
