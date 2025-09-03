<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;

// ================= Default =================
Route::get('/', function () {
    return redirect()->route('login'); // always go to login
});

// ================= Auth =================
// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    Route::get('/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/sales', [AdminController::class, 'sales'])->name('admin.sales');

    // Reports + PDF Download
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/download', [AdminController::class, 'downloadReport'])->name('admin.reports.download');

    Route::get('/kpis', [AdminController::class, 'kpis'])->name('admin.kpis');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});

// Inventory Clerk routes
Route::prefix('clerk')->group(function () {
    Route::get('/dashboard', [InventoryClerkController::class, 'dashboard'])->name('clerk.dashboard');
    Route::get('/search', [InventoryClerkController::class, 'search'])->name('clerk.search');
    Route::put('/update-stock/{id}', [InventoryClerkController::class, 'updateStock'])->name('clerk.updateStock');
});

// Sales Analyst routes
Route::prefix('analyst')->group(function () {
    Route::get('/dashboard', [SalesAnalystController::class, 'dashboard'])->name('analyst.dashboard');
    Route::post('/sales', [SalesAnalystController::class, 'store'])->name('analyst.store');


Route::prefix('admin')->group(function () {
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('admin.kpis');
    Route::post('/kpis', [AdminController::class, 'addKpi'])->name('admin.kpis.add');
    Route::delete('/kpis/{id}', [AdminController::class, 'deleteKpi'])->name('admin.kpis.delete');

    Route::get('/kpis/{id}/edit', [AdminController::class, 'editKpi'])->name('admin.kpis.edit');
    Route::put('/kpis/{id}', [AdminController::class, 'updateKpi'])->name('admin.kpis.update');
});


});
