@extends('layouts.app')

@section('title', 'Today\'s Sales History')
@section('header_title', 'Sales History')

@section('content')
    <!-- Page Header & Action Button -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Today's Transactions</h2>
            <p class="text-sm text-gray-500">Breakdown of orders processed through the POS today.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg transition">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Sales Transactions Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Time</th>
                        <th class="p-4">Channel</th>
                        <th class="p-4">Items Sold</th>
                        <th class="p-4">Discount</th>
                        <th class="p-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($todaySalesList as $sale)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- Order ID -->
                            <td class="p-4 font-bold text-gray-900">
                                #{{ $sale->sale_id }}
                            </td>

                            <!-- Time Formatted -->
                            <td class="p-4 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('h:i A') }}
                            </td>

                            <!-- Order Channel Tag -->
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-md font-semibold uppercase">
                                    {{ $sale->order_channel ?? 'Walk-In' }}
                                </span>
                            </td>

                            <!-- Items List -->
                            <td class="p-4 text-gray-700">
                                <ul class="space-y-1">
                                    @foreach($sale->details as $detail)
                                        <li class="text-xs">
                                            <span class="font-bold text-black">{{ $detail->quantity }}x</span> 
                                            {{ $detail->product->product_name ?? 'Product Item' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <!-- Discount info -->
                            <td class="p-4 text-xs text-gray-500">
                                @if(($sale->discount_amount ?? 0) > 0)
                                    <span class="text-red-700 font-medium">
                                        {{ $sale->discount_type ?? 'Discount' }} (-₱{{ number_format($sale->discount_amount, 2) }})
                                    </span>
                                @else
                                    <span class="text-gray-400">None</span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="p-4 font-black text-gray-900 text-right">
                                ₱{{ number_format($sale->total_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 text-sm">
                                No sales transactions completed today yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection