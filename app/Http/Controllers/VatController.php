<?php

namespace App\Http\Controllers;

use App\Models\VatSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VatController extends Controller
{
    /**
     * Display the VAT settings page.
     */
    public function index(): View
    {
        // Get existing settings or create default initial record
        $vat = VatSetting::first() ?? VatSetting::create([
            'name'         => 'Standard VAT',
            'rate'         => 12.00,
            'is_inclusive' => true,
            'is_active'    => true,
        ]);

        return view('vat', compact('vat'));
    }

    /**
     * Update VAT configuration in database.
     */
    public function update(Request $request, VatSetting $vat): RedirectResponse
    {
        $validated = $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $vat->update([
            'rate'         => $validated['rate'],
            'is_inclusive' => $request->has('is_inclusive'),
            'is_active'    => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'VAT settings updated successfully.');
    }
}