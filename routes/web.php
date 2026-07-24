<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SalesController;

// ---------------------------------------------------------
// Login & Registration Routes
// ---------------------------------------------------------
Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Create Account / Register Routes (THIS IS THE MISSING ROUTE)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ---------------------------------------------------------
// System Routes
// ---------------------------------------------------------
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/pos', [SalesController::class, 'index'])->name('pos');
Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');
Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
Route::post('/pos/checkout', [SalesController::class, 'store'])->name('pos.checkout');
Route::get('/inventory/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');