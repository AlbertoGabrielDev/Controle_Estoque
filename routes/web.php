<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::prefix('/verdurao')->group(function(){
    // return view('welcome');
    Route::prefix('/categoria')->group(function(){
        Route::get('/',[CategoriaController::class, 'Inicio'])->name('categoria.inicio');
        Route::get('/index',[CategoriaController::class, 'Index'])->name('categoria.index');
    });
     
     Route::get('/produtos',[ProdutoController::class, 'produtos'])->name('produtos.index');
     

});