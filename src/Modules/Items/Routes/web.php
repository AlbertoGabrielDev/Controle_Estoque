<?php

use Illuminate\Support\Facades\Route;
use Modules\Items\Http\Controllers\ItemController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    'check.permission.auto',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/cadastros')->group(function () {
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
    });
});
