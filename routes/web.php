<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;

// ------------------------
// Public / Home
// ------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');

// ------------------------
// Authentication
// ------------------------
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'registerSubmit'])->name('register.submit');

// ------------------------
// Admin Routes
// ------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', [AdminController::class, 'home'])->name('home');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');

    // Inventory
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/data', [AdminController::class, 'inventoryData'])->name('inventory.data');

    // KPIs
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('kpis');
    Route::post('/kpis/add', [AdminController::class, 'addKpi'])->name('kpis.add');
    Route::get('/kpis/edit/{id}', [AdminController::class, 'editKpi'])->name('kpis.edit');
    Route::put('/kpis/update/{id}', [AdminController::class, 'updateKpi'])->name('kpis.update');
    Route::delete('/kpis/delete/{id}', [AdminController::class, 'deleteKpi'])->name('kpis.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/generate', [AdminController::class, 'generateSummaryReport'])->name('reports.generate');
    Route::get('/reports/download/{id}', [AdminController::class, 'downloadSummaryReport'])->name('reports.download');
    Route::get('/reports/{id}', [AdminController::class, 'viewReport'])->name('reports.view');
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('reports.delete');

    // Top Products
    Route::get('/top-products', [AdminController::class, 'topProducts'])->name('topProducts');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

// ------------------------
// Inventory Clerk Routes
// ------------------------
Route::prefix('clerk')->name('clerk.')->group(function () {
    Route::get('/dashboard', [InventoryClerkController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [InventoryClerkController::class, 'search'])->name('search');
    Route::put('/update-stock/{id}', [InventoryClerkController::class, 'updateStock'])->name('updateStock');
    Route::post('/save-sale', [InventoryClerkController::class, 'saveSale'])->name('saveSale');
    Route::post('/create', [InventoryClerkController::class, 'createClerk'])->name('create');
    Route::get('/metrics', [InventoryClerkController::class, 'metrics'])->name('metrics');
    Route::get('/report', [InventoryClerkController::class, 'report'])->name('report');
    Route::get('/report/download', [InventoryClerkController::class, 'downloadReport'])->name('report.download');


    // Products
    Route::get('/products/create', [InventoryClerkController::class, 'createProduct'])->name('products.create');
    Route::post('/products/store', [InventoryClerkController::class, 'storeProduct'])->name('products.store');

    // Inventory
    Route::get('/inventory/create', [InventoryClerkController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory/store', [InventoryClerkController::class, 'storeInventory'])->name('inventory.store');
});

// ------------------------
// Sales Analyst Routes
// ------------------------
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/dashboard', [SalesAnalystController::class, 'dashboard'])->name('dashboard');
    Route::post('/store', [SalesAnalystController::class, 'store'])->name('store');
    Route::get('/reports', [SalesAnalystController::class, 'reports'])->name('reports');
    Route::get('/download', [SalesAnalystController::class, 'downloadReport'])->name('downloadReport');
    Route::get('/fetch-sales-data', [SalesAnalystController::class, 'fetchSalesData'])->name('data');
});
