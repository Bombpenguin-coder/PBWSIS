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
// Public Guest Routes
// ---------------------------------------------------------
Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// First-time owner setup routes
Route::get('/setup', [AuthController::class, 'showRegister'])->name('setup.register');
Route::post('/setup', [AuthController::class, 'storeOwner'])->name('setup.store');

// ---------------------------------------------------------
// Protected Application Routes (Requires Login)
// ---------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Dashboard & POS
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [SalesController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [SalesController::class, 'store'])->name('pos.checkout');
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order.store');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');

    // Sales History & Reports
    Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
    Route::get('/sales/reports', [SalesController::class, 'reports'])->name('sales.reports');

    // Inventory Management
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');
    Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/inventory/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/inventory/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Ingredients Management
    Route::get('/inventory/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/inventory/ingredients/{id}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    // Categories
    Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Wastage Management
    Route::get('/inventory/wastage', [WastageController::class, 'index'])->name('wastage.index');
    Route::post('/inventory/wastage', [WastageController::class, 'store'])->name('wastage.store');
    Route::put('/wastage/{id}', [WastageController::class, 'update'])->name('wastage.update');
    Route::delete('/inventory/wastage/{id}', [WastageController::class, 'destroy'])->name('wastage.destroy');

    // File Maintenance (Suppliers, Discounts, VAT)
    Route::resource('suppliers', SupplierController::class);
    Route::resource('discounts', DiscountController::class);
    Route::get('/discounts/active', function () {
        try {
            return response()->json(\App\Models\Discount::all());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    });
    Route::get('/vat', [VatController::class, 'index'])->name('vat.index');
    Route::put('/vat/{vat}', [VatController::class, 'update'])->name('vat.update');

    // Operations Routes
    Route::prefix('operations')->group(function () {
        Route::get('/held-orders', [OperationController::class, 'heldOrders'])->name('operations.held');
        Route::get('/kot', [OperationController::class, 'kitchenTickets'])->name('operations.kot');
        Route::put('/kot/{id}', [OperationController::class, 'updateKotStatus'])->name('operations.kot.update');
        Route::get('/bills', [OperationController::class, 'bills'])->name('operations.bills');
        Route::post('/bills/{id}/pay', [OperationController::class, 'checkoutBill'])->name('operations.pay');
    });

    // Administration & User Management
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Protected Administration Routes
    Route::middleware(['auth', 'role:Owner'])->group(function () {
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    
    // We can also add routes for Categories, Suppliers, and Discounts inside this group later!
});

    // Purchases
    Route::view('/purchases', 'layouts.purchases')->name('purchases.index');

    Route::get('/reports', [SalesController::class, 'reports'])->name('reports.index');

    // Reciepts Section
    Route::get('/test-receipt', function () { return view('receipt'); });
});
