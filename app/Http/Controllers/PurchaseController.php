<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'ingredient'])->latest()->get();
        $ingredients = Ingredient::all();
        $suppliers = Supplier::all();

     return view('layouts.purchases', compact('purchases', 'ingredients', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'ingredient_id' => 'required|exists:ingredients,ingredient_id',
            'quantity_received' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $totalCost = $request->quantity_received * $request->unit_cost;

        Purchase::create([
            'supplier_id' => $request->supplier_id,
            'ingredient_id' => $request->ingredient_id,
            'quantity_received' => $request->quantity_received,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $totalCost,
            'purchase_date' => $request->purchase_date,
        ]);

        $ingredient = Ingredient::where('ingredient_id', $request->ingredient_id)->firstOrFail();
        $ingredient->increment('quantity', $request->quantity_received);

        return redirect()->back()->with('success', 'Purchase recorded and stock updated successfully.');
    }
}