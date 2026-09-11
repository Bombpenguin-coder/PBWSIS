@extends('layouts.app')

@section('title', 'Today\'s Sales History')
@section('header_title', 'Sales History')

@section('content')
    <!-- Page Header & Action Button -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Today's Transactions</h2>
            <p class="text-sm text-zinc-400">Breakdown of orders processed through the POS today.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-[#202226] hover:bg-zinc-700 text-zinc-300 text-xs font-bold rounded-lg transition border border-zinc-700">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Sales Transactions Table -->
    <div class="bg-[#18191c] rounded-xl shadow-sm border border-zinc-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#202226] border-b border-zinc-800 text-xs font-bold text-zinc-400 uppercase tracking-wider">
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Time</th>
                        <th class="p-4">Channel</th>
                        <th class="p-4">Items Sold</th>
                        <th class="p-4">Discount</th>
                        <th class="p-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm bg-[#18191c]">
                    @forelse($todaySalesList as $sale)
                        <tr class="hover:bg-[#202226]/60 transition">
                            <!-- Order ID -->
                            <td class="p-4 font-bold text-white">
                                #{{ $sale->sale_id }}
                            </td>

                            <!-- Time Formatted -->
                            <td class="p-4 text-zinc-400 text-xs">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('h:i A') }}
                            </td>

                            <!-- Order Channel Tag -->
                            <td class="p-4">
                                <span class="bg-[#202226] text-zinc-300 border border-zinc-700 text-xs px-2.5 py-1 rounded-md font-semibold uppercase">
                                    {{ $sale->order_channel ?? 'Walk-In' }}
                                </span>
                            </td>

                            <!-- Items List -->
                            <td class="p-4 text-zinc-300">
                                <ul class="space-y-1">
                                    @foreach($sale->details as $detail)
                                        <li class="text-xs">
                                            <span class="font-bold text-white">{{ $detail->quantity }}x</span> 
                                            {{ $detail->product->product_name ?? 'Product Item' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <!-- Discount info -->
                            <td class="p-4 text-xs text-zinc-400">
                                @if(($sale->discount_amount ?? 0) > 0)
                                    <span class="text-rose-400 font-medium">
                                        {{ $sale->discount_type ?? 'Discount' }} (-₱{{ number_format($sale->discount_amount, 2) }})
                                    </span>
                                @else
                                    <span class="text-zinc-500">None</span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="p-4 font-black text-white text-right">
                                ₱{{ number_format($sale->total_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500 text-sm">
                                No sales transactions completed today yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection