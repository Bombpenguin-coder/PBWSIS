@extends('layouts.app')

@section('title', 'Suppliers')
@section('header_title', 'Supplier Management')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">Suppliers List</h3>
        <!-- Add Modal trigger or simple form here -->
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase">
                <th class="p-3">Name</th>
                <th class="p-3">Contact Person</th>
                <th class="p-3">Phone</th>
                <th class="p-3">Email</th>
                <th class="p-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="p-3 font-medium">{{ $supplier->name }}</td>
                    <td class="p-3">{{ $supplier->contact_person ?? 'N/A' }}</td>
                    <td class="p-3">{{ $supplier->phone ?? 'N/A' }}</td>
                    <td class="p-3">{{ $supplier->email ?? 'N/A' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $supplier->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No suppliers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection