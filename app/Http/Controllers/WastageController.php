<?php

namespace App\Http\Controllers;

use App\Models\Wastage;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WastageController extends Controller
{
    public function index()
    {
        // Fetch all ingredients for the dropdown
        $ingredients = Ingredient::where('quantity', '>', 0)->get();
        
        // Fetch wastage logs with their associated ingredient name
        $wastages = Wastage::with('ingredient')->orderBy('wastage_date', 'desc')->paginate(10);
        
        return view('wastage', compact('ingredients', 'wastages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id'   => 'required|exists:ingredients,ingredient_id',
            'quantity_wasted' => 'required|numeric|min:0.01',
            'reason'          => 'required|string|max:255|not_regex:/^[0-9]+$/',
            'wastage_date'    => 'required|date|before_or_equal:today',
        ], [
            'reason.not_regex' => 'Please provide a valid descriptive reason (e.g., Expired, Dropped) instead of just numbers.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Save the Wastage Log
            Wastage::create([
                'ingredient_id'   => $request->ingredient_id,
                'quantity_wasted' => $request->quantity_wasted,
                'reason'          => $request->reason,
                'wastage_date'    => $request->wastage_date,
            ]);

            // 2. Deduct the wasted quantity from the actual Inventory
            Ingredient::where('ingredient_id', $request->ingredient_id)
                      ->decrement('quantity', $request->quantity_wasted);

            DB::commit();
            return redirect()->back()->with('success', 'Wastage logged successfully and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wastage Log Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to log wastage. Please try again.');
        }
    }
}