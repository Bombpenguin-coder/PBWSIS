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

        // 2. Fetch Low Stock Lists (Ingredients below 50% & Products <= 10)
        $lowStockIngredients = Ingredient::whereRaw('quantity <= (max_capacity * 0.50)')->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get(); 
        
        $totalLowStock = $lowStockIngredients->count() + $lowStockProducts->count();

        // 3. Calculate Monthly Revenue
        $monthlyRevenue = Sale::whereMonth('sale_date', Carbon::now()->month)
                              ->whereYear('sale_date', Carbon::now()->year)
                              ->sum('total_amount');

        return view('dashboard', compact(
            'todaySales', 
            'totalLowStock', 
            'monthlyRevenue', 
            'lowStockIngredients', 
            'lowStockProducts'
        ));
    }
}