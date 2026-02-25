<?php

use Illuminate\Support\Facades\Route;
use Modules\Taxes\Http\Controllers\TaxRuleController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
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
