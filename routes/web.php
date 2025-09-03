<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\RegisterController; 
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('welcome');


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


// Handle login form submission (placeholder)
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');

// Dashboards
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/clerk/dashboard', function () {
    return view('clerk.dashboard');
})->name('clerk.dashboard');

Route::get('/analyst/dashboard', function () {
    return view('analyst.dashboard');
})->name('analyst.dashboard');



// Clerk
Route::get('/clerk/dashboard', [InventoryClerkController::class, 'dashboard'])->name('clerk.dashboard');
Route::get('/clerk/search', [InventoryClerkController::class, 'search'])->name('clerk.search');
Route::put('/clerk/update-stock/{id}', [InventoryClerkController::class, 'updateStock'])->name('clerk.updateStock');

// Analyst
Route::get('/analyst/dashboard', [SalesAnalystController::class, 'dashboard'])->name('analyst.dashboard');
Route::post('/analyst/sales', [SalesAnalystController::class, 'store'])->name('analyst.store');


Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/sales', [AdminController::class, 'sales'])->name('admin.sales');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/kpis', [AdminController::class, 'kpis'])->name('admin.kpis');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});



});

