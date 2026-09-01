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

// =========================================================
// PUBLIC GUEST ROUTES (No Authentication Required)
// =========================================================

// Home & Authentication
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration & Setup
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/setup', [AuthController::class, 'showRegister'])->name('setup.register');
Route::post('/setup', [AuthController::class, 'storeOwner'])->name('setup.store');


// =========================================================
// AUTHENTICATED ROUTES (Requires Login)
// =========================================================

Route::middleware(['auth'])->group(function () {

    // =========================================================
    // Dashboard & POS System
    // =========================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [SalesController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [SalesController::class, 'store'])->name('pos.checkout');
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order.store');

    // =========================================================
    // Sales Management
    // =========================================================
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/history', [SalesController::class, 'history'])->name('history');
        Route::get('/reports', [SalesController::class, 'reports'])->name('reports');
    });

    // =========================================================
    // Inventory Management
    // =========================================================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        
        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::put('{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // Ingredients
        Route::prefix('ingredients')->name('ingredients.')->group(function () {
            Route::get('/', [IngredientController::class, 'index'])->name('index');
            Route::post('/', [IngredientController::class, 'store'])->name('store');
            Route::put('{id}', [IngredientController::class, 'update'])->name('update');
            Route::delete('{id}', [IngredientController::class, 'destroy'])->name('destroy');
        });

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
        });

        // Wastage
        Route::prefix('wastage')->name('wastage.')->group(function () {
            Route::get('/', [WastageController::class, 'index'])->name('index');
            Route::post('/', [WastageController::class, 'store'])->name('store');
            Route::put('{id}', [WastageController::class, 'update'])->name('update');
            Route::delete('{id}', [WastageController::class, 'destroy'])->name('destroy');
        });
    });

    // Backward compatibility routes (old paths still work)
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');

    // =========================================================
    // File Maintenance (Suppliers, Discounts, VAT)
    // =========================================================
    Route::resource('suppliers', SupplierController::class);
    Route::resource('discounts', DiscountController::class);
    
    Route::get('/discounts/active', function () {
        try {
            return response()->json(\App\Models\Discount::all());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    });

    Route::prefix('vat')->name('vat.')->group(function () {
        Route::get('/', [VatController::class, 'index'])->name('index');
        Route::put('{vat}', [VatController::class, 'update'])->name('update');
    });

    // =========================================================
    // Operations & Kitchen Management
    // =========================================================
    Route::prefix('operations')->name('operations.')->group(function () {
        Route::get('/held-orders', [OperationController::class, 'heldOrders'])->name('held');
        Route::get('/kot', [OperationController::class, 'kitchenTickets'])->name('kot');
        Route::put('/kot/{id}', [OperationController::class, 'updateKotStatus'])->name('kot.update');
        Route::get('/bills', [OperationController::class, 'bills'])->name('bills');
        Route::post('/bills/{id}/pay', [OperationController::class, 'checkoutBill'])->name('pay');
    });

    // =========================================================
    // Reports & Receipts
    // =========================================================
    Route::get('/reports', [SalesController::class, 'reports'])->name('reports.index');
    Route::view('/purchases', 'layouts.purchases')->name('purchases.index');
    Route::get('/test-receipt', function () { return view('receipt'); });

    // =========================================================
    // OWNER ONLY - Administration & User Management
    // =========================================================
    Route::middleware(['role:Owner'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserManagementController::class, 'index'])->name('index');
                Route::post('/', [UserManagementController::class, 'store'])->name('store');
                Route::put('{user}', [UserManagementController::class, 'update'])->name('update');
                Route::delete('{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
