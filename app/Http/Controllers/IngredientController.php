<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IngredientController extends Controller
{
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

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ingredient_name' => 'required|string|max:255',
            'quantity'        => 'required|numeric|min:0',
            'pieces_per_box'  => 'required|numeric|min:1',
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
                'total_pieces'    => $ppb,
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

    public function update(Request $request, $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            $validated = $request->validate([
                'ingredient_name' => 'required|string|max:255',
                'quantity'        => 'required|numeric|min:0',
                'pieces_per_box'  => 'required|numeric|min:1',
                'unit'            => 'required|string|max:50',
                'max_capacity'    => 'required|numeric|min:1',
                'reorder_level'   => 'required|numeric|min:0',
            ]);

            $newPpb = $validated['pieces_per_box'];
            $activePieces = min($ingredient->total_pieces ?? $newPpb, $newPpb);

            $ingredient->update([
                'ingredient_name' => $validated['ingredient_name'],
                'quantity'        => $validated['quantity'],
                'pieces_per_box'  => $newPpb,
                'total_pieces'    => $activePieces,
                'unit'            => $validated['unit'],
                'max_capacity'    => $validated['max_capacity'],
                'reorder_level'   => $validated['reorder_level'],
            ]);

            return redirect()->back()->with('success', 'Ingredient updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            Log::error('Failed to update ingredient: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function deductStock($ingredientId, $amountUsed)
    {
        $ingredient = Ingredient::findOrFail($ingredientId);
        $ingredient->deductPieces($amountUsed);
        return $ingredient;
    }
}