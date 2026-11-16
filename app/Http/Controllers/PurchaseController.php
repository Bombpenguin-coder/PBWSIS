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
            'supplier_id'     => 'required',
            'ingredient_name' => 'required|string|max:255',
            'box_qty'         => 'required|integer|min:1',
            'unit_cost'       => 'required|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        $ingredientName = trim($request->ingredient_name);
        $boxQty = (int) $request->box_qty;
        $totalCost = $boxQty * $request->unit_cost;

        $ingredient = Ingredient::where('ingredient_name', $ingredientName)->first();

        if (!$ingredient) {
            // New ingredient: reference batch = purchased boxes, 30% yellow threshold
            $warningLevel = (int) ceil($boxQty * 0.30);

            $ingredient = Ingredient::create([
                'ingredient_name'     => $ingredientName,
                'quantity'            => $boxQty,
                'max_capacity'        => $boxQty,
                'reorder_level'       => $warningLevel,
                'unit'                => 'pcs',
                'pieces_per_box'      => 50,
                'total_pieces'        => 50,
            ]);
        } else {
            // Existing ingredient: add purchased boxes and recalculate 30% warning threshold
            $newTotalBoxes = $ingredient->quantity + $boxQty;
            $newWarningLevel = (int) ceil($newTotalBoxes * 0.30);

            $ingredient->update([
                'quantity'      => $newTotalBoxes,
                'max_capacity'  => $newTotalBoxes,
                'reorder_level' => $newWarningLevel,
            ]);
        }

        Purchase::create([
            'supplier_id'       => $request->supplier_id,
            'ingredient_id'     => $ingredient->ingredient_id,
            'quantity_received' => $boxQty,
            'unit_cost'         => $request->unit_cost,
            'total_cost'        => $totalCost,
            'purchase_date'     => $request->purchase_date,
        ]);

        return redirect()->back()->with('success', 'Purchase recorded and restock thresholds automatically updated.');
    }
}