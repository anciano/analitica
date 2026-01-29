<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/login/local', [AuthController::class, 'localLogin'])->name('login.local');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ImportController::class, 'index'])->name('dashboard');
    Route::resource('imports', ImportController::class);

    // Finance Management Views
    Route::get('/finanzas/resumen', [\App\Http\Controllers\FinanceController::class, 'resumen'])->name('finance.resumen');
    Route::get('/finanzas/tendencia', [\App\Http\Controllers\FinanceController::class, 'tendencia'])->name('finance.tendencia');
    Route::get('/finanzas/powerbi', [\App\Http\Controllers\FinanceController::class, 'powerbi'])->name('finance.powerbi');
});