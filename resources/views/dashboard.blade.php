@extends('layouts.app')

@section('title', 'Dashboard Overview')
@section('header_title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <p class="text-gray-600 text-lg">Welcome back. Here is the current status of Prince Buffalo Wings.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Today's Sales Widget -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-lg transition duration-200">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Today's Sales</h3>
            <p class="text-3xl font-bold text-black">₱{{ number_format($todaySales, 2) }}</p>
            <p class="text-sm text-green-600 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Live data from POS
            </p>
        </div>

        <!-- Low Stock Widget -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-900 hover:shadow-lg transition duration-200">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Low Stock Alerts</h3>
            <p class="text-3xl font-bold {{ $totalLowStock > 0 ? 'text-red-900' : 'text-green-600' }}">
                {{ $totalLowStock }} {{ Str::plural('Item', $totalLowStock) }}
            </p>
            <p class="text-sm text-gray-500 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Requires immediate restock
            </p>
        </div>

        <!-- Monthly Revenue Widget -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-lg transition duration-200">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Monthly Revenue</h3>
            <p class="text-3xl font-bold text-black">₱{{ number_format($monthlyRevenue, 2) }}</p>
            <p class="text-sm text-gray-500 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Current month's gross income
            </p>
        </div>
    </div>
@endsection