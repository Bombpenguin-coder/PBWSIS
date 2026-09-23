<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Ingredient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

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

        // 2. Fetch ALL ingredients, sorting low stock items to the top
        $allIngredients = Ingredient::orderByRaw('(quantity <= (max_capacity * 0.50)) DESC')->get();

        // Calculate count of low-stock ingredients for the KPI summary card
        $totalLowStock = $allIngredients->filter(function ($ingredient) {
            return $ingredient->max_capacity > 0 && ($ingredient->quantity <= ($ingredient->max_capacity * 0.50));
        })->count();

        // 3. Calculate 7-Day Sales Trend for the Chart
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('M d'); 
            $dailyTotal = Sale::whereDate('sale_date', $date->toDateString())->sum('total_amount');
            $chartData[] = $dailyTotal;
        }

        // 4. Pass variables to view
        return view('dashboard', compact(
            'todaySales', 
            'totalLowStock', 
            'monthlyRevenue',
            'chartLabels',
            'chartData',
            'allIngredients'
        ));
    }

    public function auditTrail()
    {
        $audits = Audit::with('user')->latest()->paginate(20);
        
        return view('audit_trail', compact('audits'));
    }
}