<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Eager load ingredients for inventory display
        $products = Product::with('ingredients')->latest()->paginate(10);

        return view('inventory', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:Available,Unavailable',
        ]);

        Product::create([
            'product_name' => $request->product_name,
            'price'        => $request->price,
            'status'       => $request->status,
        ]);

        return redirect()->back()->with('success', 'Product created successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'status'       => 'nullable|in:Available,Unavailable',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }
}