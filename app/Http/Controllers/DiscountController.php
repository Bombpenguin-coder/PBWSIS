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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        Discount::create($request->all());
        return redirect()->back()->with('success', 'Discount created successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->back()->with('success', 'Discount deleted successfully.');
    }
}