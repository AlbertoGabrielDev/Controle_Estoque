<?php

use App\Http\Controllers\Api\GraficosApiController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/graficos')->group(function() {
    Route::get('/months',[GraficosApiController::class, 'months']);
    

});

