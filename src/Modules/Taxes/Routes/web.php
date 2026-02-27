<?php

use Illuminate\Support\Facades\Route;
use Modules\Taxes\Http\Controllers\TaxRuleController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
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
