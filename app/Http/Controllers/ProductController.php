<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);

        return view('inventory', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name'   => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status'         => 'required|in:Available,Unavailable',
        ]);

        Product::create([
            'product_name'   => $request->product_name,
            'price'          => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'status'         => $request->status,
        ]);

        return redirect()->back()->with('success', 'Product created successfully!');
    }
}