<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display the Point of Sale (POS) terminal.
     */
    public function index()
    {
        // Fetch only products that are marked 'Available' and have stock remaining.
        $products = Product::where('status', 'Available')
                           ->where('stock_quantity', '>', 0)
                           ->get();

        return view('pointofsale', compact('products'));
    }
}