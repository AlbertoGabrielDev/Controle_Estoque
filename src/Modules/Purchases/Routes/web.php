<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchases\Http\Controllers\PurchaseOrderController;
use Modules\Purchases\Http\Controllers\PurchasePayableController;
use Modules\Purchases\Http\Controllers\PurchaseQuotationController;
use Modules\Purchases\Http\Controllers\PurchaseReceiptController;
use Modules\Purchases\Http\Controllers\PurchaseRequisitionController;
use Modules\Purchases\Http\Controllers\PurchaseReturnController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/purchases')->group(function () {
        Route::prefix('/requisitions')->group(function () {
            Route::get('/', [PurchaseRequisitionController::class, 'index'])->name('purchases.requisitions.index');
            Route::get('/create', [PurchaseRequisitionController::class, 'create'])->name('purchases.requisitions.create');
            Route::post('/', [PurchaseRequisitionController::class, 'store'])->name('purchases.requisitions.store');
            Route::get('/{requisitionId}', [PurchaseRequisitionController::class, 'show'])->name('purchases.requisitions.show');
            Route::get('/{requisitionId}/edit', [PurchaseRequisitionController::class, 'edit'])->name('purchases.requisitions.edit');
            Route::match(['put', 'patch'], '/{requisitionId}', [PurchaseRequisitionController::class, 'update'])->name('purchases.requisitions.update');
            Route::patch('/{requisitionId}/approve', [PurchaseRequisitionController::class, 'approve'])->name('purchases.requisitions.approve');
            Route::patch('/{requisitionId}/cancel', [PurchaseRequisitionController::class, 'cancel'])->name('purchases.requisitions.cancel');
            Route::patch('/{requisitionId}/close', [PurchaseRequisitionController::class, 'close'])->name('purchases.requisitions.close');
        });

        Route::prefix('/quotations')->group(function () {
            Route::get('/', [PurchaseQuotationController::class, 'index'])->name('purchases.quotations.index');
            Route::get('/create', [PurchaseQuotationController::class, 'create'])->name('purchases.quotations.create');
            Route::post('/', [PurchaseQuotationController::class, 'store'])->name('purchases.quotations.store');
            Route::get('/{quotationId}', [PurchaseQuotationController::class, 'show'])->name('purchases.quotations.show');
            Route::get('/{quotationId}/edit', [PurchaseQuotationController::class, 'edit'])->name('purchases.quotations.edit');
            Route::match(['put', 'patch'], '/{quotationId}', [PurchaseQuotationController::class, 'update'])->name('purchases.quotations.update');
            Route::post('/{quotationId}/suppliers', [PurchaseQuotationController::class, 'addSupplier'])->name('purchases.quotations.addSupplier');
            Route::patch('/{quotationId}/suppliers/{quotationSupplierId}/prices', [PurchaseQuotationController::class, 'registerPrices'])->name('purchases.quotations.registerPrices');
            Route::patch('/{quotationId}/select-item', [PurchaseQuotationController::class, 'selectItem'])->name('purchases.quotations.selectItem');
            Route::patch('/{quotationId}/close', [PurchaseQuotationController::class, 'close'])->name('purchases.quotations.close');
            Route::patch('/{quotationId}/cancel', [PurchaseQuotationController::class, 'cancel'])->name('purchases.quotations.cancel');
        });

        Route::prefix('/orders')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('purchases.orders.index');
            Route::post('/from-quotation', [PurchaseOrderController::class, 'storeFromQuotation'])->name('purchases.orders.fromQuotation');
            Route::get('/{orderId}', [PurchaseOrderController::class, 'show'])->name('purchases.orders.show');
            Route::patch('/{orderId}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchases.orders.cancel');
            Route::patch('/{orderId}/close', [PurchaseOrderController::class, 'close'])->name('purchases.orders.close');
        });

        Route::prefix('/receipts')->group(function () {
            Route::get('/', [PurchaseReceiptController::class, 'index'])->name('purchases.receipts.index');
            Route::get('/create', [PurchaseReceiptController::class, 'create'])->name('purchases.receipts.create');
            Route::post('/', [PurchaseReceiptController::class, 'store'])->name('purchases.receipts.store');
            Route::get('/{receiptId}', [PurchaseReceiptController::class, 'show'])->name('purchases.receipts.show');
            Route::patch('/{receiptId}/check', [PurchaseReceiptController::class, 'check'])->name('purchases.receipts.check');
            Route::patch('/{receiptId}/accept-divergence', [PurchaseReceiptController::class, 'acceptDivergence'])->name('purchases.receipts.acceptDivergence');
            Route::patch('/{receiptId}/reverse', [PurchaseReceiptController::class, 'reverse'])->name('purchases.receipts.reverse');
        });

        Route::prefix('/returns')->group(function () {
            Route::get('/', [PurchaseReturnController::class, 'index'])->name('purchases.returns.index');
            Route::get('/create', [PurchaseReturnController::class, 'create'])->name('purchases.returns.create');
            Route::post('/', [PurchaseReturnController::class, 'store'])->name('purchases.returns.store');
            Route::get('/{returnId}', [PurchaseReturnController::class, 'show'])->name('purchases.returns.show');
            Route::patch('/{returnId}/confirm', [PurchaseReturnController::class, 'confirm'])->name('purchases.returns.confirm');
            Route::patch('/{returnId}/cancel', [PurchaseReturnController::class, 'cancel'])->name('purchases.returns.cancel');
        });

        Route::prefix('/payables')->group(function () {
            Route::get('/', [PurchasePayableController::class, 'index'])->name('purchases.payables.index');
            Route::get('/{payableId}', [PurchasePayableController::class, 'show'])->name('purchases.payables.show');
        });
    });
});
