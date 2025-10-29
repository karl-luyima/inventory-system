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
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', [AdminController::class, 'home'])->name('home');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');

    // Inventory
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');

    // KPIs
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('kpis');

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
// ------------------------
// Inventory Clerk Routes
// ------------------------
// ==================== INVENTORY CLERK ROUTES ====================

// Dashboard
Route::get('/clerk/dashboard', [InventoryClerkController::class, 'dashboard'])
    ->name('clerk.dashboard');

// Search products
Route::get('/clerk/search', [InventoryClerkController::class, 'search'])
    ->name('clerk.search');

// Update stock
Route::put('/clerk/update-stock/{id}', [InventoryClerkController::class, 'updateStock'])
    ->name('clerk.updateStock');

// Save sale
Route::post('/clerk/save-sale', [InventoryClerkController::class, 'saveSale'])
    ->name('clerk.saveSale');

// Create clerk
Route::post('/clerk/create', [InventoryClerkController::class, 'createClerk'])
    ->name('clerk.create');

// Metrics
Route::get('/clerk/metrics', [InventoryClerkController::class, 'metrics'])
    ->name('clerk.metrics');

// Create product form
Route::get('/clerk/products/create', [InventoryClerkController::class, 'createProduct'])
    ->name('clerk.products.create');

// Store product
Route::post('/clerk/products/store', [InventoryClerkController::class, 'storeProduct'])
    ->name('clerk.products.store');

// Create inventory form
Route::get('/clerk/inventory/create', [InventoryClerkController::class, 'createInventory'])
    ->name('clerk.inventory.create');

// Store inventory
Route::post('/clerk/inventory/store', [InventoryClerkController::class, 'storeInventory'])
    ->name('clerk.inventory.store');


// Show the Add Product form
Route::get('/products/create', [InventoryClerkController::class, 'create'])->name('products.create');

// Handle the form submission
Route::post('/products', [InventoryClerkController::class, 'store'])->name('products.store');


// Show the "Add Inventory" form
Route::get('/inventories/create', [InventoryClerkController::class, 'createInventory'])->name('inventories.create');

// Handle form submission
Route::post('/inventories', [InventoryClerkController::class, 'storeInventory'])->name('inventories.store');

// Inventory
Route::get('/inventories/create', [InventoryClerkController::class, 'createInventory'])->name('inventories.create');
Route::post('/inventories', [InventoryClerkController::class, 'storeInventory'])->name('inventories.store');

// Products
Route::get('/products/create', [InventoryClerkController::class, 'createProduct'])->name('products.create');
Route::post('/products', [InventoryClerkController::class, 'storeProduct'])->name('products.store');

// Dashboard
Route::get('/clerk/dashboard', [InventoryClerkController::class, 'dashboard'])->name('clerk.dashboard');




// ------------------------
// Sales Analyst Routes
// ------------------------
// Sales Analyst Routes
// ------------------------

// Dashboard
Route::get('/sales/dashboard', [SalesAnalystController::class, 'dashboard'])->name('sales.dashboard');

// Record a Sale
Route::post('/sales/store', [SalesAnalystController::class, 'store'])->name('analyst.sales.store');

// View Reports
Route::get('/sales/reports', [SalesAnalystController::class, 'reports'])->name('sales.reports');

// Download Report as PDF
Route::get('/sales/download', [SalesAnalystController::class, 'downloadReport'])->name('sales.downloadReport');

// Fetch Sales Data (for JS/AJAX)
Route::get('/sales/fetch-sales-data', [SalesAnalystController::class, 'fetchSalesData'])->name('analyst.sales.data');
