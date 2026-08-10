<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
{
    $suppliers = Supplier::latest()->get();
    return view('suppliers', compact('suppliers')); // Updated from 'file_maintenance.suppliers'
}

    public function store(Request $request)
    {
       $request->validate([
    'name'           => 'required|string|max:255',
    'contact_person' => 'nullable|string|max:255',
    'phone'          => 'nullable|string|regex:/^09\d{9}$/', // Must start with 09 and contain exactly 11 digits
    'email'          => 'nullable|email|max:255',
    'status'         => 'required|in:active,inactive',
]);
        Supplier::create($request->all());
        return redirect()->back()->with('success', 'Supplier created successfully.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->all());
        return redirect()->back()->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier deleted successfully.');
    }
}
