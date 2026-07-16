<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 

class ProductController extends Controller
{
    public function index()
    {
        // Grab all products from the database
        $products = Product::all();
        
        // Pass them to a new 'inventory' view
        return view('inventory', ['products' => $products]);
    }

    public function store(Request $request) 
    {
        // 1. Validate the incoming form data
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        // 2. Create the new product in the database
        Product::create([
            'product_name' => $request->product_name,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'status' => 'Available', // Automatically sets default status
        ]);

        // 3. Refresh the page so the user sees the new item in the table
        return redirect('/inventory');
    }
}


