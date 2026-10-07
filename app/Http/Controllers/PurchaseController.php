<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Ingredient;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        // Automatically resets table view daily by filtering for today's date only
        $purchases = Purchase::with(['supplier', 'ingredient'])
            ->whereDate('purchase_date', Carbon::today())
            ->latest()
            ->get();

        $ingredients = Ingredient::all();
        $suppliers = Supplier::all();

        return view('layouts.purchases', compact('purchases', 'ingredients', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'ingredient_name' => 'required|string|max:255',
            'box_qty' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $ingredientName = trim($request->ingredient_name);

        // Find ingredient by name or create a new one if it doesn't exist
        $ingredient = Ingredient::where('ingredient_name', $ingredientName)->first();

        if (!$ingredient) {
            $ingredient = Ingredient::create([
                'ingredient_name'     => $ingredientName,
                'quantity'            => 0,
                'max_capacity'        => 100,
                'reorder_level'       => 10,     // Fixed: Required by DB schema
                'unit'                => 'pcs',  // Fulfills non-null unit constraint
                'pieces_per_box'      => 50,     // Default fallback pieces per box
                'active_loose_pieces' => 0,      // Default fallback active pieces
            ]);
        }

        $boxQty = (int) $request->box_qty;
        $totalCost = $boxQty * $request->unit_cost;

        // Record the purchase log
        Purchase::create([
            'supplier_id'       => $request->supplier_id,
            'ingredient_id'     => $ingredient->ingredient_id ?? $ingredient->id,
            'quantity_received' => $boxQty,
            'unit_cost'         => $request->unit_cost,
            'total_cost'        => $totalCost,
            'purchase_date'     => $request->purchase_date,
        ]);

        // Increment the ingredient inventory quantity by box count
        $ingredient->increment('quantity', $boxQty);

        return redirect()->back()->with('success', 'Purchase recorded and stock updated successfully.');
    }
}