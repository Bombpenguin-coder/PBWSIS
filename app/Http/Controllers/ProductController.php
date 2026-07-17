<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the products in the inventory view.
     */
    public function index()
    {
        // Fetch all products from the database. 
        // We use paginate() instead of get() to handle the 51-100 daily transactions efficiently as your list grows.
        $products = Product::paginate(10); 

        // Send the data to the inventory.blade.php view
        return view('inventory', compact('products'));
    }

    /**
     * Store a newly created product in the database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|string'
        ]);

        try {
            Product::create($validatedData);
            return redirect()->route('inventory')->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create product: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while saving the product.');
        }
    }
}