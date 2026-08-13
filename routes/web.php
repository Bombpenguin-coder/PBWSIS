<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\WastageController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VatController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\CategoryController;
use App\Models\Discount;

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
Route::delete('/inventory/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::delete('/inventory/ingredients/{id}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
Route::delete('/inventory/wastage/{id}', [WastageController::class, 'destroy'])->name('wastage.destroy');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->name('ingredients.update');
Route::put('/wastage/{id}', [WastageController::class, 'update'])->name('wastage.update');
Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');
// ---------------------------------------------------------
// Sales History & Reports Routes
// ---------------------------------------------------------
Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
Route::get('/sales/reports', [SalesController::class, 'reports'])->name('sales.reports');
// ---------------------------------------------------------
// Suppliers, VAT settigs and Discouts Routes
// ---------------------------------------------------------
Route::middleware(['auth'])->group(function () {

Route::get('/discounts/active', function () {
    return response()->json(
        \App\Models\Discount::where('is_active', true)
            ->get(['id', 'name', 'percentage'])
    );
});
    // Existing routes here...
    Route::post('/sales', [App\Http\Controllers\SalesController::class, 'store'])->name('sales.store');

    // File Maintenance Routes
    Route::resource('suppliers', SupplierController::class);
    Route::get('/vat', [VatController::class, 'index'])->name('vat.index');
    Route::put('/vat/{vat}', [VatController::class, 'update'])->name('vat.update');
    Route::resource('discounts', DiscountController::class);
});
