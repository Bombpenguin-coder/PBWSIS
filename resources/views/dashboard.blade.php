@extends('layouts.app')

@section('title', 'Dashboard Overview')
@section('header_title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <p class="text-gray-600 text-lg">Welcome back. Here is the current status of Prince Buffalo Wings.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- 1. TODAY'S SALES WIDGET (Fixed: Now links to Sales History) -->
        <a href="{{ route('sales.history') }}" 
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Today's Sales</h3>
                <span class="text-xs font-bold text-gray-400 group-hover:text-black transition">View History →</span>
            </div>
            <p class="text-3xl font-bold text-black">₱{{ number_format($todaySales, 2) }}</p>
            <p class="text-sm text-green-600 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Live data from POS
            </p>
        </a>

        <!-- 2. LOW STOCK WIDGET (Opens Modal Popup) -->
        <div onclick="openLowStockModal()" 
             class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-900 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Low Stock Alerts</h3>
                <span class="text-xs font-bold text-red-900/60 group-hover:text-red-900 transition">Quick View →</span>
            </div>
            <p class="text-3xl font-bold {{ $totalLowStock > 0 ? 'text-red-900' : 'text-green-600' }}">
                {{ $totalLowStock }} {{ Str::plural('Item', $totalLowStock) }}
            </p>
            <p class="text-sm text-gray-500 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Requires immediate restock
            </p>
        </div>

        <!-- 3. MONTHLY REVENUE WIDGET (Fixed: Now a clickable link to Sales Reports) -->
        <a href="{{ route('sales.reports') }}" 
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-black hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Monthly Revenue</h3>
                <span class="text-xs font-bold text-gray-400 group-hover:text-black transition">Full Report →</span>
            </div>
            <p class="text-3xl font-bold text-black">₱{{ number_format($monthlyRevenue, 2) }}</p>
            <p class="text-sm text-gray-500 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Current month's gross income
            </p>
        </a>

    </div>

    <!-- NEW: Visual Data Section -->
    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900 mb-8">
        <h2 class="text-lg font-bold mb-4 text-black">7-Day Sales Trend</h2>
        
        <!-- The Canvas with Data Attributes -->
        <div class="relative h-72 w-full">
            <canvas id="salesChart" 
                    data-labels="{{ json_encode($chartLabels) }}" 
                    data-values="{{ json_encode($chartData) }}">
            </canvas>
        </div>
    </div>


    <!-- LOW STOCK QUICK VIEW MODAL -->
    <div id="lowStockModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden border border-gray-100">
            
            <!-- Modal Header -->
            <div class="bg-red-900 text-white p-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="font-bold text-base">Low Stock Breakdown</h3>
                </div>
                <button onclick="closeLowStockModal()" class="text-red-200 hover:text-white text-lg font-bold transition">✕</button>
            </div>

            <!-- Items List -->
            <div class="p-4 max-h-[60vh] overflow-y-auto space-y-3">
                @if($lowStockProducts->count() === 0 && $lowStockIngredients->count() === 0)
                    <div class="text-center py-6 text-emerald-700 font-medium text-sm">
                        All products and raw ingredients are well stocked! 🎉
                    </div>
                @else
                    <!-- Low Stock Finished Products -->
                    @if($lowStockProducts->count() > 0)
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Products (≤ 10 items)</p>
                        @foreach($lowStockProducts as $prod)
                            <div class="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-lg">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $prod->product_name }}</p>
                                    <p class="text-xs text-gray-500">Finished Product</p>
                                </div>
                                <span class="bg-red-900 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                                    {{ $prod->stock_quantity }} left
                                </span>
                            </div>
                        @endforeach
                    @endif

                    <!-- Low Stock Raw Ingredients -->
                    @if($lowStockIngredients->count() > 0)
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-4 mb-1">Raw Ingredients (≤ 50% Capacity)</p>
                        @foreach($lowStockIngredients as $ing)
                            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-100 rounded-lg">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $ing->ingredient_name }}</p>
                                    <p class="text-xs text-gray-500">Max Capacity: {{ $ing->max_capacity }} {{ $ing->unit ?? 'units' }}</p>
                                </div>
                                <span class="bg-amber-700 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                                    {{ $ing->quantity }} {{ $ing->unit ?? '' }} left
                                </span>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>

            <!-- Footer Links -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-xs font-bold">
                <div class="space-x-3">
                    <a href="{{ route('inventory') }}" class="text-red-900 hover:underline">Products →</a>
                    <a href="{{ route('ingredients.index') }}" class="text-amber-800 hover:underline">Ingredients →</a>
                </div>
                <button onclick="closeLowStockModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openLowStockModal() {
            document.getElementById('lowStockModal').classList.remove('hidden');
        }
        function closeLowStockModal() {
            document.getElementById('lowStockModal').classList.add('hidden');
        }
    </script>
@endsection