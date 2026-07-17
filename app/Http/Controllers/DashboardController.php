<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard.
     */
    public function index()
    {
        // 1. Calculate Low Stock Ingredients (50% capacity rule)
        // We use whereRaw to perform the math directly inside the MySQL database for maximum efficiency.
        $lowStockIngredients = Ingredient::whereRaw('quantity <= (max_capacity * 0.50)')->count();

        // 2. Calculate Low Stock Products
        // Since Products don't have a max_capacity in the ERD, we will flag them if stock falls below a static number, like 20.
        $lowStockProducts = Product::where('stock_quantity', '<=', 20)->count();

        // 3. Combine the totals
        $totalLowStock = $lowStockIngredients + $lowStockProducts;

        // Pass the calculated total to the dashboard view
        return view('dashboard', compact('totalLowStock'));
    }
}