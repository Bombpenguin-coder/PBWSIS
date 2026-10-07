<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
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
        $todaySales = Sale::whereDate('sale_date', Carbon::today())->sum('total_amount') ?? 0;
        $monthlyRevenue = Sale::whereMonth('sale_date', Carbon::now()->month)
                              ->whereYear('sale_date', Carbon::now()->year)
                              ->sum('total_amount') ?? 0;

        // 2. Calculate Cumulative Food Cost with Daily Unabsorbed Rollover
        // Find the earliest recorded purchase to begin rolling daily calculations
        $firstPurchase = Purchase::orderBy('purchase_date', 'asc')->first();

        $carryOverCost = 0;

        if ($firstPurchase) {
            $startDate = Carbon::parse($firstPurchase->purchase_date);
            $today = Carbon::today();

            // Loop day-by-day from the first recorded purchase up to today
            for ($date = $startDate->copy(); $date->lte($today); $date->addDay()) {
                
                $dailyPurchases = Purchase::whereDate('purchase_date', $date)->sum('total_cost') ?? 0;
                $dailySales = Sale::whereDate('sale_date', $date)->sum('total_amount') ?? 0;

                // Total cost to absorb today (Purchases made today + unabsorbed carryover from yesterday)
                $effectiveFoodCost = $dailyPurchases + $carryOverCost;

                if ($date->isToday()) {
                    // This is today's active Food Cost metric
                    $todayFoodCost = $effectiveFoodCost;
                } else {
                    // For past days, subtract sales from effective food cost.
                    // Any remaining unpaid balance rolls over to the next day.
                    $carryOverCost = max(0, $effectiveFoodCost - $dailySales);
                }
            }
        } else {
            $todayFoodCost = 0;
        }

        // Calculate Food Cost Percentage against Today's Sales
        $foodCostPercentage = $todaySales > 0 ? ($todayFoodCost / $todaySales) * 100 : 0;

        // 3. Fetch ALL ingredients, sorting low stock items to the top
        $allIngredients = Ingredient::orderByRaw('(quantity <= (max_capacity * 0.50)) DESC')->get();

        // Calculate count of low-stock ingredients for the KPI summary card
        $totalLowStock = $allIngredients->filter(function ($ingredient) {
            return $ingredient->max_capacity > 0 && ($ingredient->quantity <= ($ingredient->max_capacity * 0.50));
        })->count();

        // 4. Calculate 7-Day Sales Trend for the Chart
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('M d'); 
            $dailyTotal = Sale::whereDate('sale_date', $date->toDateString())->sum('total_amount') ?? 0;
            $chartData[] = $dailyTotal;
        }

        // 5. Pass variables to view
        return view('dashboard', compact(
            'todaySales', 
            'todayFoodCost',
            'foodCostPercentage',
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