<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        // Eager load ingredients for inventory display
        $products = Product::with('ingredients')->latest()->paginate(10);
        
        // Fetch all ingredients to pass to the modal view
        $ingredients = Ingredient::orderBy('ingredient_name', 'asc')->get();

        return view('inventory', compact('products', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name'                => 'required|string|max:255',
            'image'                       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price'                       => 'required|numeric|min:0',
            'status'                      => 'required|in:Available,Unavailable',
            'ingredients'                 => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,ingredient_id',
            'ingredients.*.quantity'      => 'required|numeric|min:0.01',
        ]);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'product_name' => $validated['product_name'],
            'image'        => $imagePath,
            'price'        => $validated['price'],
            'status'       => $validated['status'],
        ]);

        // Sync ingredients to the pivot table with required recipe quantities
        foreach ($request->ingredients as $item) {
            $product->ingredients()->attach($item['ingredient_id'], [
                'quantity_needed' => $item['quantity']
            ]);
        }

        return redirect()->back()->with('success', 'Product created successfully!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $validated = $request->validate([
            'product_name'                => 'required|string|max:255',
            'image'                       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price'                       => 'required|numeric|min:0',
            'status'                      => 'nullable|in:Available,Unavailable',
            'ingredients'                 => 'nullable|array',
            'ingredients.*.ingredient_id' => 'required_with:ingredients|exists:ingredients,ingredient_id',
            'ingredients.*.quantity'      => 'required_with:ingredients|numeric|min:0.01',
        ]);

        // Handle Image Replacement
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'product_name' => $validated['product_name'],
            'image'        => $validated['image'] ?? $product->image,
            'price'        => $validated['price'],
            'status'       => $validated['status'] ?? $product->status,
        ]);

        // If updated ingredients are supplied, sync the pivot table
        if ($request->has('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['ingredient_id']] = ['quantity_needed' => $item['quantity']];
            }
            $product->ingredients()->sync($syncData);
        }

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Delete product image from storage if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->ingredients()->detach(); // Clean up pivot table relationships
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}