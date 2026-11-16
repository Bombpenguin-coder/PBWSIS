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
            'quantity'        => 'required|numeric|min:1',
            'pieces_per_box'  => 'required|numeric|min:1',
            'unit'            => 'required|string|max:50',
        ]);

        try {
            $qty = (int) $validatedData['quantity'];
            $ppb = $validatedData['pieces_per_box'];
            $autoReorder = (int) ceil($qty * 0.30);

            Ingredient::create([
                'ingredient_name' => $validatedData['ingredient_name'],
                'quantity'        => $qty,
                'pieces_per_box'  => $ppb,
                'total_pieces'    => $pbb,
                'unit'            => $validatedData['unit'],
                'max_capacity'    => $qty,
                'reorder_level'   => $autoReorder,
            ]);
            
            return back()->with('success', 'Ingredient added with automatic restock thresholds!');
            
        } catch (\Exception $e) {
            Log::error('Failed to create ingredient: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            // Only allow editing the amount of goods per box (and unit)
            $validated = $request->validate([
                'pieces_per_box' => 'required|numeric|min:1',
                'unit'           => 'required|string|max:50',
            ]);

            $newPpb = $validated['pieces_per_box'];
            $activePieces = min($ingredient->total_pieces ?? 0, $newPpb);

            $ingredient->update([
                'pieces_per_box' => $newPpb,
                'total_pieces'   => $newPpb,
                'unit'           => $validated['unit'],
            ]);

            return redirect()->back()->with('success', 'Box contents updated successfully.');

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