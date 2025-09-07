<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;

// ------------------------
// Root redirect
// ------------------------
Route::get('/', fn() => redirect()->route('login'));

// ------------------------
// Auth Routes
// ------------------------
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout (available globally)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'registerSubmit'])->name('register.submit');

// ------------------------
// General Dashboard
// ------------------------
Route::get('/dashboard/general', [DashboardController::class, 'index'])->name('general.dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

// ------------------------
// Admin Routes
// ------------------------
Route::prefix('admin')->group(function () {
    Route::get('/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // Inventory
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/inventory/data', [AdminController::class, 'inventoryData'])->name('admin.inventory.data');

    // Sales
    Route::get('/sales', [AdminController::class, 'sales'])->name('admin.sales');
    Route::get('/sales/data', [AdminController::class, 'salesData'])->name('admin.sales.data');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/data', [AdminController::class, 'reportsData'])->name('admin.reports.data');
    Route::get('/reports/download', [AdminController::class, 'downloadReport'])->name('admin.reports.download');

    // KPIs
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('admin.kpis');
    Route::post('/kpis', [AdminController::class, 'addKpi'])->name('admin.kpis.add');
    Route::delete('/kpis/{id}', [AdminController::class, 'deleteKpi'])->name('admin.kpis.delete');
    Route::get('/kpis/{id}/edit', [AdminController::class, 'editKpi'])->name('admin.kpis.edit');
    Route::put('/kpis/{id}', [AdminController::class, 'updateKpi'])->name('admin.kpis.update');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});

// ------------------------
// Inventory Clerk Routes
// ------------------------
Route::prefix('clerk')->group(function () {
    Route::get('/dashboard', [InventoryClerkController::class, 'dashboard'])->name('clerk.dashboard');
    Route::get('/search', [InventoryClerkController::class, 'search'])->name('clerk.search');
    Route::put('/update-stock/{id}', [InventoryClerkController::class, 'updateStock'])->name('clerk.updateStock');
});

// ------------------------
// Sales Analyst Routes
// ------------------------
Route::prefix('analyst')->group(function () {
    Route::get('/dashboard', [SalesAnalystController::class, 'dashboard'])->name('sales.dashboard');

    // Point to your existing views
    Route::get('/sales/form', [SalesAnalystController::class, 'form'])->name('sales.form');
    Route::get('/sales/reports', [SalesAnalystController::class, 'reports'])->name('sales.reports');
    Route::get('/sales/report/pdf', [SalesAnalystController::class, 'downloadReport'])->name('sales.report.pdf');


    // Store sales (AJAX or form)
    Route::post('/sales/store', [SalesAnalystController::class, 'store'])->name('sales.store');
});
