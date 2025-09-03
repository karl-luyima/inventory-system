<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryClerkController;
use App\Http\Controllers\SalesAnalystController;
use App\Http\Controllers\RegisterController; 

Route::get('/', function () {
    return view('welcome');


// Show login page when visiting "/"
Route::get('/', function () {
    return view('auth.login');
})->name('login');

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




});

