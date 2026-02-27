<?php

use App\Http\Controllers\Api\GraficosApiController;
use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\EstoqueController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/estoque')->group(function () {
        Route::get('/', [EstoqueController::class, 'index'])->name('estoque.index');
        Route::get('/data', [EstoqueController::class, 'data'])->name('estoque.data');
        Route::get('/cadastro', [EstoqueController::class, 'cadastro'])->name('estoque.cadastro');
        Route::post('/cadastro', [EstoqueController::class, 'inserirEstoque'])->name('estoque.inserirEstoque');
        Route::get('/buscar-estoque', [EstoqueController::class, 'buscar'])->name('estoque.buscar');
        Route::post('/calc-impostos', [EstoqueController::class, 'calcImpostos'])->name('estoque.calcImpostos');
        Route::get('/editar/{estoqueId}', [EstoqueController::class, 'editar'])->name('estoque.editar');
        Route::put('/editar/{estoqueId}', [EstoqueController::class, 'salvarEditar'])->name('estoque.salvarEditar');
        Route::post('/status/{modelName}/{id}', [EstoqueController::class, 'updateStatus'])->name('estoque.status');
        Route::get('/quantidade/{estoqueId}/{operacao}', [EstoqueController::class, 'atualizarEstoque'])->name('estoque.quantidade');
        Route::get('grafico-filtro', [EstoqueController::class, 'graficoFiltro'])->name('estoque.graficoFiltro');
        Route::get('/grafico', [GraficosApiController::class, 'months'])->name('months');
        Route::get('/historico', [EstoqueController::class, 'historico'])->name('estoque.historico');
    });
});
