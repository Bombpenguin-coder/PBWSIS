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

        // 2. Calculate Today's Food Cost (COGS) using the 'details' relationship
        $todaySalesItems = Sale::whereDate('sale_date', Carbon::today())
            ->with(['details.product.ingredients']) // Loads Sale -> SaleDetail -> Product -> Ingredients
            ->get()
            ->pluck('details')
            ->flatten();

        $todayFoodCost = $todaySalesItems->sum(function ($detail) {
            if (!$detail->product) {
                return 0;
            }

            // Calculate food cost per unit sold
            $unitCost = $detail->product->ingredients->sum(function ($ingredient) {
                $requiredQty = $ingredient->pivot->quantity_required ?? 0;
                $costPerUnit = $ingredient->cost_per_unit ?? 0;
                
                return $requiredQty * $costPerUnit;
            });

            return $unitCost * $detail->quantity;
        });

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
            $dailyTotal = Sale::whereDate('sale_date', $date->toDateString())->sum('total_amount');
            $chartData[] = $dailyTotal;
        }

        // 5. Pass variables to view
        return view('dashboard', compact(
            'todaySales', 
            'todayFoodCost',
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