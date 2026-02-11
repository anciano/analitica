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

    // Administración
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    });

    // Finance Management Views
    Route::get('/finanzas/resumen', [\App\Http\Controllers\FinanceController::class, 'resumen'])->name('finance.resumen');
    Route::get('/finanzas/tendencia', [\App\Http\Controllers\FinanceController::class, 'tendencia'])->name('finance.tendencia');
    Route::get('/finanzas/powerbi', [\App\Http\Controllers\FinanceController::class, 'powerbi'])->name('finance.powerbi');

    // Budget Programming
    Route::group(['prefix' => 'programacion', 'as' => 'programacion.'], function () {
        Route::get('/resumen', [\App\Http\Controllers\BudgetProgrammingController::class, 'index'])->name('index');
        Route::resource('planes', \App\Http\Controllers\BudgetProgrammingController::class);
        Route::post('planes/{id}/aprobar', [\App\Http\Controllers\BudgetProgrammingController::class, 'aprobar'])->name('planes.aprobar');
        Route::post('planes/{id}/versionar', [\App\Http\Controllers\BudgetProgrammingController::class, 'versionar'])->name('planes.versionar');

        // Items
        Route::get('planes/{plan}/items/create', [\App\Http\Controllers\BudgetPlanItemController::class, 'create'])->name('planes.items.create');
        Route::post('planes/{plan}/items', [\App\Http\Controllers\BudgetPlanItemController::class, 'store'])->name('planes.items.store');

        // Distribution
        Route::get('planes/items/{item}/distribuir', [\App\Http\Controllers\BudgetPlanItemController::class, 'distribuir'])->name('planes.items.distribuir');
        Route::post('planes/items/{item}/distribuir', [\App\Http\Controllers\BudgetPlanItemController::class, 'saveDistribuir'])->name('planes.items.save-distribuir');

        // Catalogs (Mantenedores)
        Route::resource('clasificador', \App\Http\Controllers\ClasificadorController::class);
        Route::resource('centros-costo', \App\Http\Controllers\CentroCostoController::class);
    });
});