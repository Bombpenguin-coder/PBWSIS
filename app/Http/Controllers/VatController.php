<?php

namespace App\Http\Controllers;

use App\Models\VatSetting;
use Illuminate\Http\Request;

class VatController extends Controller
{
    public function index()
    {
        $vat = VatSetting::first() ?? VatSetting::create([
            'name' => 'Standard VAT',
            'rate' => 12.00,
            'is_inclusive' => true,
            'is_active' => true
        ]);

       return view('vat', compact('vat'));
    }

    public function update(Request $request, VatSetting $vat)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $vat->update([
            'rate' => $request->rate,
            'is_inclusive' => $request->has('is_inclusive'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'VAT settings updated successfully.');
    }
}