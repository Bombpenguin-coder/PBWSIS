<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Ingredient;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Get the total number of distinct products on the menu
        $activeProductsCount = Product::count();

        // 2. Find ingredients where current quantity is less than or equal to the reorder_level
        $lowStockCount = Ingredient::whereColumn('quantity', '<=', 'reorder_level')->count();

        // 3. Pass this real data straight into the Blade view
        return view('dashboard', [
            'activeProductsCount' => $activeProductsCount,
            'lowStockCount' => $lowStockCount
        ]);
    }
}