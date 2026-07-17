<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Imports your new Controller
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController; // Import the IngredientController

// Your existing dashboard route
Route::get('/', [DashboardController::class, 'index']);
// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Add this new route for the inventory table
Route::get('/inventory', [ProductController::class, 'index']);

Route::post('/inventory', [ProductController::class, 'store']);

// The default landing page
Route::get('/', function () {
    return view('dashboard');
});

// Your new POS Terminal route
Route::get('/pos', function () {
    return view('pointofsale');
});

Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');
Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');