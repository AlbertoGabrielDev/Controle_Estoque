<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

Route::get('/vendas/month', [CalendarController::class, 'month'])->name('api.vendas.month');
