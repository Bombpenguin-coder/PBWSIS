<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->get();
        return view('discounts', compact('discounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        Discount::create([
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'value'     => $validated['value'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Discount created successfully.');
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        $discount->update([
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'value'     => $validated['value'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->back()->with('success', 'Discount deleted successfully.');
    }
}