<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Ingredient;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dynamic dashboard overview.
     */
    public function index()
    {
        // 1. Calculate Today's Total Sales
        $todaySales = Sale::whereDate('sale_date', Carbon::today())->sum('total_amount');

        // 2. Calculate Low Stock Alerts (Ingredients below 50% max capacity)
        $lowStockIngredients = Ingredient::whereRaw('quantity <= (max_capacity * 0.50)')->count();
        
        // Products below a flat threshold of 10 items
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->count(); 
        
        $totalLowStock = $lowStockIngredients + $lowStockProducts;

        // 3. Calculate Monthly Revenue
        $monthlyRevenue = Sale::whereMonth('sale_date', Carbon::now()->month)
                              ->whereYear('sale_date', Carbon::now()->year)
                              ->sum('total_amount');

        return view('dashboard', compact('todaySales', 'totalLowStock', 'monthlyRevenue'));
    }
}