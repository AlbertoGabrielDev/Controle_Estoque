<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpreadsheetController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->prefix('/verdurao')->group(function () {

    Route::prefix('/spreadsheet')->name('spreadsheet.')->group(function () {
        Route::post('/upload', [SpreadsheetController::class, 'upload'])->name('upload');
        Route::get('/data/{filename}', [SpreadsheetController::class, 'readFile'])->name('data');
        Route::get('/', [SpreadsheetController::class, 'index'])->name('index');
        Route::post('/compare', [SpreadsheetController::class, 'compare'])->name('compare');
    });
});
