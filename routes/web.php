<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

// ------------------------
// Admin Routes
// ------------------------
Route::prefix('admin')->group(function () {

    // ================= Dashboard =================
    Route::get('/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ================= Users =================
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // ================= Inventory =================
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/inventory/data', [AdminController::class, 'inventoryData'])->name('admin.inventory.data');

    // ================= KPIs =================
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('admin.kpis');
    Route::post('/kpis', [AdminController::class, 'addKpi'])->name('admin.kpis.add');
    Route::get('/kpis/{id}/edit', [AdminController::class, 'editKpi'])->name('admin.kpis.edit');
    Route::put('/kpis/{id}', [AdminController::class, 'updateKpi'])->name('admin.kpis.update');
    Route::delete('/kpis/{id}', [AdminController::class, 'deleteKpi'])->name('admin.kpis.delete');

    // ================= Reports =================
    // ✅ Place these BEFORE /reports/{id} to avoid 404 conflicts
    Route::get('/reports/generate', [AdminController::class, 'generateSummaryReport'])
        ->name('admin.reports.generate');
    Route::get('/reports/download/{id}', [AdminController::class, 'downloadSummaryReport'])
        ->name('admin.reports.download');

    // Reports list and view
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/{id}', [AdminController::class, 'viewReport'])->name('admin.reports.view');
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('admin.reports.delete');

    // ================= Settings =================
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    // ================= Top Products =================
    Route::get('/top-products', [AdminController::class, 'topProducts'])->name('admin.topProducts');

    // ================= Authentication =================
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // ✅ Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
