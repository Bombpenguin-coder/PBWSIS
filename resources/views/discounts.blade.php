@extends('layouts.app')

@section('title', 'Discounts')
@section('header_title', 'Discount Rules')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase">
                <th class="p-3">Discount Name</th>
                <th class="p-3">Type</th>
                <th class="p-3">Value</th>
                <th class="p-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            @forelse($discounts as $discount)
                <tr>
                    <td class="p-3 font-medium">{{ $discount->name }}</td>
                    <td class="p-3">{{ ucfirst($discount->type) }}</td>
                    <td class="p-3">{{ $discount->type == 'percentage' ? $discount->value . '%' : '₱' . number_format($discount->value, 2) }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $discount->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $discount->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No discounts set up yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection