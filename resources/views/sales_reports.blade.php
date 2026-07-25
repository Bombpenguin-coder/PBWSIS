@extends('layouts.app')

@section('title', 'Monthly Revenue Report')
@section('header_title', 'Monthly Reports')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Revenue Report — {{ \Carbon\Carbon::now()->format('F Y') }}</h2>
            <p class="text-sm text-gray-500">Gross sales performance and monthly transaction summary.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg transition">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Summary Banner Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-black text-white p-6 rounded-xl shadow-md">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Monthly Income</p>
            <p class="text-3xl font-black text-white">₱{{ number_format($totalMonthlyAmount, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Completed Orders</p>
            <p class="text-3xl font-black text-gray-900">{{ $totalTransactions }} {{ Str::plural('Order', $totalTransactions) }}</p>
        </div>
    </div>

    <!-- Monthly Transactions Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">All Monthly Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Date & Time</th>
                        <th class="p-4">Channel</th>
                        <th class="p-4">Items Included</th>
                        <th class="p-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($monthlySalesList as $sale)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4 font-bold text-gray-900">#{{ $sale->sale_id }}</td>
                            <td class="p-4 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y — h:i A') }}
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-md font-semibold uppercase">
                                    {{ $sale->order_channel ?? 'Walk-In' }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-700">
                                <ul class="space-y-1">
                                    @foreach($sale->details as $detail)
                                        <li class="text-xs">
                                            <span class="font-bold text-black">{{ $detail->quantity }}x</span> 
                                            {{ $detail->product->product_name ?? 'Item' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 font-black text-gray-900 text-right">
                                ₱{{ number_format($sale->total_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 text-sm">
                                No sales recorded for this month yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection