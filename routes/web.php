<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/lecturers', [DashboardController::class, 'lecturers'])->name('lecturers');
Route::get('/crawl', [DashboardController::class, 'crawl'])->name('crawl');
Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
Route::get('/sinta-proxy', [DashboardController::class, 'sintaProxy'])->name('sinta.proxy');
