<?php

use Illuminate\Support\Facades\Route;
use Modules\Commercial\Http\Controllers\OpportunityController;
use Modules\Commercial\Http\Controllers\ProposalController;
use Modules\Commercial\Http\Controllers\DiscountPolicyController;
use Modules\Commercial\Http\Controllers\SalesOrderController;
use Modules\Commercial\Http\Controllers\SalesInvoiceController;
use Modules\Commercial\Http\Controllers\SalesReturnController;
use Modules\Commercial\Http\Controllers\SalesReceivableController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/commercial')->group(function () {

        // Oportunidades
        Route::prefix('/opportunities')->group(function () {
            Route::get('/', [OpportunityController::class, 'index'])->name('commercial.opportunities.index');
            Route::get('/data', [OpportunityController::class, 'data'])->name('commercial.opportunities.data');
            Route::get('/create', [OpportunityController::class, 'create'])->name('commercial.opportunities.create');
            Route::post('/', [OpportunityController::class, 'store'])->name('commercial.opportunities.store');
            Route::get('/{opportunityId}', [OpportunityController::class, 'show'])->name('commercial.opportunities.show');
            Route::get('/{opportunityId}/edit', [OpportunityController::class, 'edit'])->name('commercial.opportunities.edit');
            Route::match(['put', 'patch'], '/{opportunityId}', [OpportunityController::class, 'update'])->name('commercial.opportunities.update');
            Route::patch('/{opportunityId}/status', [OpportunityController::class, 'changeStatus'])->name('commercial.opportunities.status');
            Route::post('/{opportunityId}/convert-to-proposal', [OpportunityController::class, 'convertToProposal'])->name('commercial.opportunities.convertToProposal');
        });

        // Propostas
        Route::prefix('/proposals')->group(function () {
            Route::get('/', [ProposalController::class, 'index'])->name('commercial.proposals.index');
            Route::get('/data', [ProposalController::class, 'data'])->name('commercial.proposals.data');
            Route::get('/create', [ProposalController::class, 'create'])->name('commercial.proposals.create');
            Route::post('/', [ProposalController::class, 'store'])->name('commercial.proposals.store');
            Route::get('/{proposalId}', [ProposalController::class, 'show'])->name('commercial.proposals.show');
            Route::get('/{proposalId}/edit', [ProposalController::class, 'edit'])->name('commercial.proposals.edit');
            Route::match(['put', 'patch'], '/{proposalId}', [ProposalController::class, 'update'])->name('commercial.proposals.update');
            Route::patch('/{proposalId}/send', [ProposalController::class, 'send'])->name('commercial.proposals.send');
            Route::patch('/{proposalId}/approve', [ProposalController::class, 'approve'])->name('commercial.proposals.approve');
            Route::patch('/{proposalId}/reject', [ProposalController::class, 'reject'])->name('commercial.proposals.reject');
            Route::post('/{proposalId}/convert-to-order', [ProposalController::class, 'convertToOrder'])->name('commercial.proposals.convertToOrder');
        });

        // Politicas de desconto
        Route::prefix('/discount-policies')->group(function () {
            Route::get('/', [DiscountPolicyController::class, 'index'])->name('commercial.discount-policies.index');
            Route::get('/data', [DiscountPolicyController::class, 'data'])->name('commercial.discount-policies.data');
            Route::get('/create', [DiscountPolicyController::class, 'create'])->name('commercial.discount-policies.create');
            Route::post('/', [DiscountPolicyController::class, 'store'])->name('commercial.discount-policies.store');
            Route::get('/{policyId}', [DiscountPolicyController::class, 'show'])->name('commercial.discount-policies.show');
            Route::get('/{policyId}/edit', [DiscountPolicyController::class, 'edit'])->name('commercial.discount-policies.edit');
            Route::match(['put', 'patch'], '/{policyId}', [DiscountPolicyController::class, 'update'])->name('commercial.discount-policies.update');
        });

        // Pedidos de venda
        Route::prefix('/orders')->group(function () {
            Route::get('/', [SalesOrderController::class, 'index'])->name('commercial.orders.index');
            Route::get('/data', [SalesOrderController::class, 'data'])->name('commercial.orders.data');
            Route::get('/create', [SalesOrderController::class, 'create'])->name('commercial.orders.create');
            Route::post('/', [SalesOrderController::class, 'store'])->name('commercial.orders.store');
            Route::get('/{orderId}', [SalesOrderController::class, 'show'])->name('commercial.orders.show');
            Route::get('/{orderId}/edit', [SalesOrderController::class, 'edit'])->name('commercial.orders.edit');
            Route::match(['put', 'patch'], '/{orderId}', [SalesOrderController::class, 'update'])->name('commercial.orders.update');
            Route::patch('/{orderId}/confirm', [SalesOrderController::class, 'confirm'])->name('commercial.orders.confirm');
            Route::patch('/{orderId}/cancel', [SalesOrderController::class, 'cancel'])->name('commercial.orders.cancel');
        });

        // Faturas
        Route::prefix('/invoices')->group(function () {
            Route::get('/', [SalesInvoiceController::class, 'index'])->name('commercial.invoices.index');
            Route::get('/data', [SalesInvoiceController::class, 'data'])->name('commercial.invoices.data');
            Route::get('/create', [SalesInvoiceController::class, 'create'])->name('commercial.invoices.create');
            Route::post('/', [SalesInvoiceController::class, 'store'])->name('commercial.invoices.store');
            Route::get('/{invoiceId}', [SalesInvoiceController::class, 'show'])->name('commercial.invoices.show');
            Route::patch('/{invoiceId}/issue', [SalesInvoiceController::class, 'issue'])->name('commercial.invoices.issue');
            Route::patch('/{invoiceId}/cancel', [SalesInvoiceController::class, 'cancel'])->name('commercial.invoices.cancel');
        });

        // Devolucoes
        Route::prefix('/returns')->group(function () {
            Route::get('/', [SalesReturnController::class, 'index'])->name('commercial.returns.index');
            Route::get('/data', [SalesReturnController::class, 'data'])->name('commercial.returns.data');
            Route::get('/create', [SalesReturnController::class, 'create'])->name('commercial.returns.create');
            Route::post('/', [SalesReturnController::class, 'store'])->name('commercial.returns.store');
            Route::get('/{returnId}', [SalesReturnController::class, 'show'])->name('commercial.returns.show');
            Route::patch('/{returnId}/confirm', [SalesReturnController::class, 'confirm'])->name('commercial.returns.confirm');
            Route::patch('/{returnId}/cancel', [SalesReturnController::class, 'cancel'])->name('commercial.returns.cancel');
        });

        // Contas a receber
        Route::prefix('/receivables')->group(function () {
            Route::get('/', [SalesReceivableController::class, 'index'])->name('commercial.receivables.index');
            Route::get('/data', [SalesReceivableController::class, 'data'])->name('commercial.receivables.data');
            Route::get('/{receivableId}', [SalesReceivableController::class, 'show'])->name('commercial.receivables.show');
        });
    });
});
