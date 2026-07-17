<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SalesController;

// ---------------------------------------------------------
// Dashboard & Landing Routes
// ---------------------------------------------------------
// Both the root URL and /dashboard now safely go through the controller
Route::get('/', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ---------------------------------------------------------
// Point of Sale (POS) Routes
// ---------------------------------------------------------
Route::get('/pos', function () {
    return view('pointofsale');
});
Route::get('/pos', [SalesController::class, 'index'])->name('pos');

// ---------------------------------------------------------
// File Maintenance & Inventory Routes
// ---------------------------------------------------------
Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');
Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');