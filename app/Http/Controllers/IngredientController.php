<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IngredientController extends Controller
{
    /**
     * Store a newly created ingredient in the database.
     */
    public function store(Request $request)
    {
        // Validate the specific fields defined in your ERD
        $validatedData = $request->validate([
            'ingredient_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50', // e.g., kg, liters, pieces
            'max_capacity' => 'required|numeric|min:1',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        try {
            Ingredient::create($validatedData);
            
            // Assuming you will create an 'ingredients.index' route and view later
            return back()->with('success', 'Ingredient added successfully!');
            
        } catch (\Exception $e) {
            Log::error('Failed to create ingredient: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while saving the ingredient.');
        }
    }
}