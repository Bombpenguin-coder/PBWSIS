<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\WastageController;
<<<<<<< Updated upstream
use App\Http\Controllers\CategoryController; // Make sure this is at the top of the file
=======
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VatController;
use App\Http\Controllers\DiscountController;
>>>>>>> Stashed changes

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
Route::get('/inventory/wastage', [WastageController::class, 'index'])->name('wastage.index');
Route::post('/inventory/wastage', [WastageController::class, 'store'])->name('wastage.store');
// ---------------------------------------------------------
// Sales History & Reports Routes
// ---------------------------------------------------------
Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
Route::get('/sales/reports', [SalesController::class, 'reports'])->name('sales.reports');
<<<<<<< Updated upstream

// Category Routes
Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');
=======
// ---------------------------------------------------------
// Suppliers, VAT settigs and Discouts Routes
// ---------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    // Existing routes here...

    // File Maintenance Routes
    Route::resource('suppliers', SupplierController::class);
    Route::get('/vat', [VatController::class, 'index'])->name('vat.index');
    Route::put('/vat/{vat}', [VatController::class, 'update'])->name('vat.update');
    Route::resource('discounts', DiscountController::class);
});
>>>>>>> Stashed changes
