<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\UsuarioController;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {
    Route::prefix('/usuario')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index')->middleware('check.permission:view_post,perfil');
        Route::get('/data', [UsuarioController::class, 'data'])->name('usuario.data')->middleware('check.permission:view_post,perfil');
        Route::get('/cadastro', [UsuarioController::class, 'cadastro'])->name('usuario.cadastro')->middleware('check.permission:create_post,perfil');
        Route::post('/cadastro', [UsuarioController::class, 'inserirUsuario'])->name('usuario.inserirUsuario');
        Route::get('/editar/{userId}', [UsuarioController::class, 'editar'])->name('usuario.editar')->middleware('check.permission:edit_post,perfil');
        Route::post('/status/{modelName}/{id}', [UsuarioController::class, 'updateStatus'])->name('usuario.status')->middleware('check.permission:status,perfil');
        Route::put('/editar/{userId}', [UsuarioController::class, 'salvarEditar'])->name('usuario.salvarEditar');
        Route::get('/buscar-usuario', [UsuarioController::class, 'buscar'])->name('usuario.buscar');
    });

    Route::prefix('/roles')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('roles.index')->middleware('check.permission:view_post,permissao');
        Route::get('/data', [RoleController::class, 'data'])->name('roles.data')->middleware('check.permission:view_post,permissao');
        Route::get('/cadastro', [RoleController::class, 'cadastro'])->name('roles.cadastro')->middleware('check.permission:create_post,permissao');
        Route::post('/cadastro', [RoleController::class, 'inserirRole'])->name('roles.inserirRole');
        Route::get('/buscar-unidade', [RoleController::class, 'buscar'])->name('roles.buscar');
        Route::get('/editar/{roleId}', [RoleController::class, 'editar'])->name('roles.editar')->middleware('check.permission:edit_post,permissao');
        Route::put('/editar/{roleId}', [RoleController::class, 'salvarEditar'])->name('roles.salvarEditar')->middleware('check.permission:edit_post,permissao');
        Route::post('/status/{modelName}/{id}', [RoleController::class, 'updateStatus'])->name('roles.status');
    });
});

Route::get('login', [UsuarioController::class, 'unidade'])->name('login');
Route::get('register', action: [UsuarioController::class, 'unidadeRegister'])->name('register');
