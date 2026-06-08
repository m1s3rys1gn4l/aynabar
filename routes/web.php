<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/create', [CodeController::class, 'create']);
    Route::post('/dashboard/create', [CodeController::class, 'store']);
    Route::post('/dashboard/dynamic-target', [CodeController::class, 'updateDynamicTarget']);

    Route::get('/codes/{code}/image', [CodeController::class, 'image']);
    Route::get('/dashboard/code/{code}', [CodeController::class, 'analytics']);
    Route::get('/codes/{code}/scans/export', [CodeController::class, 'exportScansCsv']);

    Route::middleware('superadmin')->group(function (): void {
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users', [AdminUserController::class, 'store']);
    });
});

Route::get('/r/{slug}', [RedirectController::class, 'handle']);
