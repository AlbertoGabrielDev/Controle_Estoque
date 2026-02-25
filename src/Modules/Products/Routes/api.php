<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ProdutoController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('/products/search', [ProdutoController::class, 'search']);
});
