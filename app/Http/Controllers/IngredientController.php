<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IngredientController extends Controller
{
    /**
     * Display a listing of the ingredients.
     */
    public function index()
    {
        $ingredients = Ingredient::paginate(10); 
        return view('ingredients', compact('ingredients'));
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return redirect()->back()->with('success', 'Ingredient deleted successfully!');
    }

    /**
     * Store a newly created ingredient in the database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ingredient_name' => 'required|string|max:255',
            'quantity'        => 'required|numeric|min:0', // Total Sealed Boxes
            'pieces_per_box'  => 'required|numeric|min:1', // Capacity per box
            'unit'            => 'required|string|max:50',
            'max_capacity'    => 'required|numeric|min:1',
            'reorder_level'   => 'required|numeric|min:0',
        ]);

        try {
            $ppb = $validatedData['pieces_per_box'];

            Ingredient::create([
                'ingredient_name' => $validatedData['ingredient_name'],
                'quantity'        => $validatedData['quantity'],
                'pieces_per_box'  => $ppb,
                'total_pieces'    => $ppb, // Opens 1 active box by default
                'unit'            => $validatedData['unit'],
                'max_capacity'    => $validatedData['max_capacity'],
                'reorder_level'   => $validatedData['reorder_level'],
            ]);
            
            return back()->with('success', 'Ingredient added successfully!');
            
        } catch (\Exception $e) {
            Log::error('Failed to create ingredient: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified ingredient in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            $validated = $request->validate([
                'ingredient_name' => 'required|string|max:255',
                'quantity'        => 'required|numeric|min:0',
                'pieces_per_box'  => 'required|numeric|min:1',
                'unit'            => 'required|string|max:50',
            ]);

            $newPpb = $validated['pieces_per_box'];
            
            // Adjust current loose pieces if it exceeds the new box capacity
            $activePieces = min($ingredient->total_pieces ?? $newPpb, $newPpb);

            $ingredient->update([
                'ingredient_name' => $validated['ingredient_name'],
                'quantity'        => $validated['quantity'],
                'pieces_per_box'  => $newPpb,
                'total_pieces'    => $activePieces,
                'unit'            => $validated['unit'],
            ]);

            return redirect()->back()->with('success', 'Ingredient updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed. Check your input values.');
        } catch (\Exception $e) {
            Log::error('Failed to update ingredient: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }

    /**
     * Deduct stock when an ingredient is used (e.g. in POS order execution).
     */
    public function deductStock($ingredientId, $amountUsed)
    {
        $ingredient = Ingredient::findOrFail($ingredientId);

        // Deduct the requested usage from active loose pieces
        $ingredient->total_pieces -= $amountUsed;

        // Loop to open sealed boxes as long as total_pieces is <= 0 and sealed boxes exist
        while ($ingredient->total_pieces <= 0 && $ingredient->quantity > 0) {
            $leftoverNeeded = abs($ingredient->total_pieces);
            
            $ingredient->quantity -= 1; // Open 1 sealed box
            $ingredient->total_pieces = $ingredient->pieces_per_box - $leftoverNeeded;
        }

        // Clamp at 0 if inventory is completely exhausted
        if ($ingredient->quantity <= 0 && $ingredient->total_pieces < 0) {
            $ingredient->total_pieces = 0;
        }

        $ingredient->save();

        return $ingredient;
    }
}