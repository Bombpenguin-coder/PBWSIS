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
        // 1. Calculate Today's Sales & Monthly Revenue
        $todaySales = Sale::whereDate('sale_date', Carbon::today())->sum('total_amount');
        $monthlyRevenue = Sale::whereMonth('sale_date', Carbon::now()->month)
                              ->whereYear('sale_date', Carbon::now()->year)
                              ->sum('total_amount');

        // 2. Fetch Low Stock Collections (This fixes the error!)
        // Use ->get() instead of ->count() so the Blade file has the actual item data for the modal
        $lowStockIngredients = Ingredient::whereRaw('quantity <= (max_capacity * 0.50)')->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get(); 
        
        // Calculate the total integer for the top widget
        $totalLowStock = $lowStockIngredients->count() + $lowStockProducts->count();

        // 3. Calculate 7-Day Sales Trend for the Chart
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('M d'); 
            $dailyTotal = Sale::whereDate('sale_date', $date->toDateString())->sum('total_amount');
            $chartData[] = $dailyTotal;
        }

        // 4. Pass ALL variables to the view, including the ones your groupmates' code needs
        return view('dashboard', compact(
            'todaySales', 
            'totalLowStock', 
            'monthlyRevenue',
            'chartLabels',
            'chartData',
            'lowStockIngredients', // Added for the modal
            'lowStockProducts'     // Added for the modal
        ));
    }
}