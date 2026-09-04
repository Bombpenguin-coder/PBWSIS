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
// 1. PUBLIC & AUTHENTICATION ROUTES
// =========================================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// First-time owner setup routes
Route::get('/setup', [AuthController::class, 'showRegister'])->name('setup.register');
Route::post('/setup', [AuthController::class, 'storeOwner'])->name('setup.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'storeOwner']);


// =========================================================
// 2. AUTHENTICATED APPLICATION ROUTES
// =========================================================
Route::middleware(['auth'])->group(function () {

    // ---------------------------------------------------------
    // Dashboard & POS Core
    // ---------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [SalesController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [SalesController::class, 'store'])->name('pos.checkout');
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order.store');

    // ---------------------------------------------------------
    // Sales Management (Grouped + Prefixed)
    // ---------------------------------------------------------
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/history', [SalesController::class, 'history'])->name('history');
        Route::get('/reports', [SalesController::class, 'reports'])->name('reports');
    });

    // Legacy / Unprefixed Sales Aliases
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
    Route::get('/sales/reports', [SalesController::class, 'reports'])->name('sales.reports');

    // ---------------------------------------------------------
    // Inventory Management (Grouped & Prefixed)
    // ---------------------------------------------------------
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

    // ---------------------------------------------------------
    // Unprefixed / Legacy Aliases (Fixes Blade Template Route Errors)
    // ---------------------------------------------------------
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/inventory/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/inventory/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/inventory/ingredients/{id}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');

    Route::get('/wastage', [WastageController::class, 'index'])->name('wastage.index');
    Route::post('/inventory/wastage', [WastageController::class, 'store'])->name('wastage.store');
    Route::put('/wastage/{id}', [WastageController::class, 'update'])->name('wastage.update');
    Route::delete('/inventory/wastage/delete/{id}', [WastageController::class, 'destroy'])->name('wastage.destroy');

    // ---------------------------------------------------------
    // Operations & Kitchen Management
    // ---------------------------------------------------------
    Route::prefix('operations')->name('operations.')->group(function () {
        Route::get('/held-orders', [OperationController::class, 'heldOrders'])->name('held');
        Route::get('/kot', [OperationController::class, 'kitchenTickets'])->name('kot');
        Route::put('/kot/{id}', [OperationController::class, 'updateKotStatus'])->name('kot.update');
        Route::get('/bills', [OperationController::class, 'bills'])->name('bills');
        Route::post('/bills/{id}/pay', [OperationController::class, 'checkoutBill'])->name('pay');
    });

    // ---------------------------------------------------------
    // File Maintenance (Suppliers, Discounts, VAT)
    // ---------------------------------------------------------
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

    // ---------------------------------------------------------
    // Reports & Receipts
    // ---------------------------------------------------------
    Route::get('/reports', [SalesController::class, 'reports'])->name('reports.index');
    Route::view('/purchases', 'layouts.purchases')->name('purchases.index');
    Route::get('/test-receipt', function () { return view('receipt'); });

    Route::get('/receipt/{sale_id}', function ($sale_id) {
        // Fetch the sale, the cashier, the items, and the product details
        $sale = \App\Models\Sale::with(['user', 'details.product'])->findOrFail($sale_id);
        
        return view('receipt', compact('sale'));
    })->name('receipt.show');

    // ---------------------------------------------------------
    // Owner Only Administration
    // ---------------------------------------------------------
    Route::middleware(['role:Owner'])->group(function () {
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        
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