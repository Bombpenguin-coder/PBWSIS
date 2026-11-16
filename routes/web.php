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
use App\Http\Controllers\PurchaseController;

// ESC/POS Thermal Printer Library Imports
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

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
    // STRICTLY OWNER ONLY (Dashboard, User Management, Audit Trail)
    // ---------------------------------------------------------
    Route::middleware(['role:Owner'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserManagementController::class, 'index'])->name('index');
                Route::post('/', [UserManagementController::class, 'store'])->name('store');
                Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            });
            Route::get('/audit-trail', [DashboardController::class, 'auditTrail'])->name('audit-trail');
        });
    });

    // ---------------------------------------------------------
    // POS Core
    // ---------------------------------------------------------
    Route::get('/pos', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('pos')) {
            return redirect()->route('sales.history')->with('error', 'Access Restricted: You do not have POS permissions.');
        }
        return app(SalesController::class)->index($request);
    })->name('pos');

    Route::post('/pos/checkout', [SalesController::class, 'store'])->name('pos.checkout');
    Route::post('/pos/print/{sale_id}', [SalesController::class, 'printThermalReceipt'])->name('pos.print');

    // ---------------------------------------------------------
    // Sales Management & Reports
    // ---------------------------------------------------------
    Route::get('/reports', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('reports')) {
            return redirect()->route('sales.history')->with('error', 'Access Restricted: Reports permission required.');
        }
        return app(SalesController::class)->reports($request);
    })->name('reports.index');

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', function (Illuminate\Http\Request $request) {
            if (!auth()->user()->hasPermission('pos') && !auth()->user()->hasPermission('sales_history')) {
                return redirect('/login')->with('error', 'Access Restricted.');
            }
            return app(SalesController::class)->index($request);
        })->name('index');

        Route::post('/', [SalesController::class, 'store'])->name('store');

        Route::get('/history', function (Illuminate\Http\Request $request) {
            if (!auth()->user()->hasPermission('sales_history')) {
                return redirect()->route('pos')->with('error', 'Access Restricted: Sales History permission required.');
            }
            return app(SalesController::class)->history($request);
        })->name('history');

        Route::get('/reports', function (Illuminate\Http\Request $request) {
            if (!auth()->user()->hasPermission('reports')) {
                return redirect()->route('sales.history')->with('error', 'Access Restricted.');
            }
            return app(SalesController::class)->reports($request);
        })->name('reports');
    });

    // ---------------------------------------------------------
    // Inventory Management (Guarded)
    // ---------------------------------------------------------
    Route::prefix('inventory')->name('inventory.')->group(function () {
        
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', function (Illuminate\Http\Request $request) {
                if (!auth()->user()->hasPermission('inventory')) {
                    return redirect()->route('pos')->with('error', 'Access Restricted.');
                }
                return app(ProductController::class)->index($request);
            })->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::put('{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('ingredients')->name('ingredients.')->group(function () {
            Route::get('/', function (Illuminate\Http\Request $request) {
                if (!auth()->user()->hasPermission('inventory')) {
                    return redirect()->route('pos')->with('error', 'Access Restricted.');
                }
                return app(IngredientController::class)->index($request);
            })->name('index');
            Route::post('/', [IngredientController::class, 'store'])->name('store');
            Route::put('{id}', [IngredientController::class, 'update'])->name('update');
            Route::delete('{id}', [IngredientController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', function (Illuminate\Http\Request $request) {
                if (!auth()->user()->hasPermission('inventory')) {
                    return redirect()->route('pos')->with('error', 'Access Restricted.');
                }
                return app(CategoryController::class)->index($request);
            })->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::put('{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('wastage')->name('wastage.')->group(function () {
            Route::get('/', function (Illuminate\Http\Request $request) {
                if (!auth()->user()->hasPermission('wastage')) {
                    return redirect()->route('pos')->with('error', 'Access Restricted: Wastage permission required.');
                }
                return app(WastageController::class)->index($request);
            })->name('index');
            Route::post('/', [WastageController::class, 'store'])->name('store');
            Route::put('{id}', [WastageController::class, 'update'])->name('update');
            Route::delete('{id}', [WastageController::class, 'destroy'])->name('destroy');
        });
    });

    // ---------------------------------------------------------
    // Blade Compatibility Route Aliases
    // ---------------------------------------------------------
    Route::get('/inventory', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('inventory')) {
            return redirect()->route('pos')->with('error', 'Access Restricted.');
        }
        return app(ProductController::class)->index($request);
    })->name('inventory');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    Route::get('/ingredients', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('inventory')) {
            return redirect()->route('pos')->with('error', 'Access Restricted.');
        }
        return app(IngredientController::class)->index($request);
    })->name('ingredients.index');

    Route::post('/inventory/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/inventory/ingredients/{id}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/wastage', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('wastage')) {
            return redirect()->route('pos')->with('error', 'Access Restricted.');
        }
        return app(WastageController::class)->index($request);
    })->name('wastage.index');
    
    Route::post('/wastage', [WastageController::class, 'store'])->name('wastage.store');
    Route::put('/wastage/{id}', [WastageController::class, 'update'])->name('wastage.update');
    Route::delete('/wastage/{id}', [WastageController::class, 'destroy'])->name('wastage.destroy');

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
    // File Maintenance & Purchases
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

    Route::get('/purchases', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->hasPermission('purchases')) {
            return redirect()->route('pos')->with('error', 'Access Restricted: Purchases permission required.');
        }
        return app(PurchaseController::class)->index($request);
    })->name('purchases.index');

    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store'); 

    Route::get('/receipt/{sale_id}', function ($sale_id) {
        $sale = \App\Models\Sale::with(['details.product'])->findOrFail($sale_id);
        return view('receipt', compact('sale'));
    })->name('receipt.show');

}); // End of Authenticated Routes


// =========================================================
// 3. THERMAL PRINTER TEST ROUTE (POS58)
// =========================================================
Route::get('/test-pos58', function () {
    try {
        $connector = new WindowsPrintConnector("POS58");
        $printer = new Printer($connector);

        // Header
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("PBWSIS POS\n");
        $printer->setEmphasis(false);
        $printer->text("Official Receipt Preview\n");
        $printer->text(now()->format('d/m/Y, H:i:s') . "\n");
        $printer->text("--------------------------------\n");

        // Body (Left Aligned)
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $formatRow = function ($name, $price) {
            $maxWidth = 32;
            $priceStr = " " . $price;
            $maxNameWidth = $maxWidth - strlen($priceStr);

            $wrappedLines = explode("\n", wordwrap($name, $maxNameWidth, "\n", true));
            
            $firstLine = $wrappedLines[0];
            $spaces = $maxWidth - strlen($firstLine) - strlen($price);
            $output = $firstLine . str_repeat(" ", max(1, $spaces)) . $price . "\n";

            for ($i = 1; $i < count($wrappedLines); $i++) {
                $output .= $wrappedLines[$i] . "\n";
            }

            return $output;
        };

        // Sample Items
        $printer->text($formatRow("1x sweet and sour chicken", "P150.00"));
        $printer->text($formatRow("1x chicken tender", "P69.00"));
        $printer->text("--------------------------------\n");

        // Totals
        $printer->text($formatRow("Subtotal:", "P219.00"));
        $printer->text($formatRow("Discount:", "-P0.00"));
        $printer->text($formatRow("VAT (12% Incl.):", "P23.46"));
        $printer->text("--------------------------------\n");

        // Grand Total
        $printer->setEmphasis(true);
        $printer->text($formatRow("TOTAL:", "P219.00"));
        $printer->setEmphasis(false);
        $printer->text("--------------------------------\n");

        // Footer
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Thank you for your purchase!\n");

        $printer->feed(4);
        $printer->close();

        return "SUCCESS! Check your POS58 thermal printer for the receipt.";

    } catch (\Exception $e) {
        return "PRINTER ERROR: " . $e->getMessage();
    }
});