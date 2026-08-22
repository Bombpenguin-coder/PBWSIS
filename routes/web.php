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
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PosController;

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

// Administration Routes
Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.index');
Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.store');
Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

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
Route::put('/inventory/{id}', [ProductController::class, 'update'])->name('products.update');
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
    try {
        // Safe query returning all discounts without strict column checks
        $discounts = \App\Models\Discount::all();
        return response()->json($discounts);
    } catch (\Exception $e) {
        // Returns an empty array instead of crashing with a 500 error
        return response()->json([]);
    }
});
    // Existing routes here...
    Route::post('/sales', [App\Http\Controllers\SalesController::class, 'store'])->name('sales.store');

    // File Maintenance Routes
// 1. Categories (The one we made together)
Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');

// 2. Suppliers (Resource magic generates suppliers.index)
Route::resource('suppliers', SupplierController::class);

// 3. Discounts (Resource magic generates discounts.index)
Route::resource('discounts', DiscountController::class);

// 4. VAT (Custom routes)
Route::get('/vat', [VatController::class, 'index'])->name('vat.index');
Route::put('/vat/{vat}', [VatController::class, 'update'])->name('vat.update');

// POS / Sales Routes
Route::post('/sales', [App\Http\Controllers\SalesController::class, 'store'])->name('sales.store');




Route::prefix('operations')->group(function () {
    Route::get('/held-orders', [OperationController::class, 'heldOrders'])->name('operations.held');
    Route::get('/kot', [OperationController::class, 'kitchenTickets'])->name('operations.kot');
    Route::put('/kot/{id}', [OperationController::class, 'updateKotStatus'])->name('operations.kot.update');
    Route::get('/bills', [OperationController::class, 'bills'])->name('operations.bills');
    Route::post('/bills/{id}/pay', [OperationController::class, 'checkoutBill'])->name('operations.pay');
});



Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order.store');
});
