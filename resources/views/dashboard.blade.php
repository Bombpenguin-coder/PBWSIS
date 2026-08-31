@extends('layouts.app')

@section('title', 'Dashboard Overview')
@section('header_title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <p class="text-zinc-400 text-lg">Welcome back. Here is the current status of Prince Buffalo Wings.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- 1. TODAY'S SALES WIDGET -->
        <a href="{{ route('sales.history') }}" 
           class="block bg-[#202226] p-6 rounded-xl shadow-lg border-l-4 border-[#ff8c00] hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-zinc-400 text-sm font-bold uppercase tracking-wider">Today's Sales</h3>
                <span class="text-xs font-bold text-zinc-500 group-hover:text-[#ff8c00] transition">View History →</span>
            </div>
            <p class="text-3xl font-black text-white">₱{{ number_format($todaySales, 2) }}</p>
            <p class="text-sm text-emerald-400 mt-2 flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Live data from POS
            </p>
        </a>

        <!-- 2. LOW STOCK WIDGET -->
        <div onclick="openLowStockModal()" 
             class="bg-[#202226] p-6 rounded-xl shadow-lg border-l-4 border-amber-500 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-zinc-400 text-sm font-bold uppercase tracking-wider">Low Stock Alerts</h3>
                <span class="text-xs font-bold text-amber-500/80 group-hover:text-amber-400 transition">Quick View →</span>
            </div>
            <p class="text-3xl font-black {{ $totalLowStock > 0 ? 'text-amber-500' : 'text-emerald-400' }}">
                {{ $totalLowStock }} {{ Str::plural('Ingredient', $totalLowStock) }}
            </p>
            <p class="text-sm text-zinc-400 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Requires immediate restock
            </p>
        </div>

        <!-- 3. MONTHLY REVENUE WIDGET -->
        <a href="{{ route('sales.reports') }}" 
           class="block bg-[#202226] p-6 rounded-xl shadow-lg border-l-4 border-[#ff8c00] hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-zinc-400 text-sm font-bold uppercase tracking-wider">Monthly Revenue</h3>
                <span class="text-xs font-bold text-zinc-500 group-hover:text-[#ff8c00] transition">Full Report →</span>
            </div>
            <p class="text-3xl font-black text-white">₱{{ number_format($monthlyRevenue, 2) }}</p>
            <p class="text-sm text-zinc-400 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Current month's gross income
            </p>
        </a>

    </div>

    <!-- 7-Day Sales Trend -->
    <div class="bg-[#202226] p-6 rounded-xl shadow-lg border-t-4 border-[#ff8c00] mb-8">
        <h2 class="text-lg font-bold mb-4 text-white">7-Day Sales Trend</h2>
        <div class="relative h-72 w-full">
            <canvas id="salesChart" 
                    data-labels="{{ json_encode($chartLabels) }}" 
                    data-values="{{ json_encode($chartData) }}">
            </canvas>
        </div>
    </div>

    <!-- LOW STOCK QUICK VIEW MODAL -->
    <div id="lowStockModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-[#202226] rounded-xl max-w-lg w-full shadow-2xl overflow-hidden border border-zinc-700">
            
            <!-- Modal Header -->
            <div class="bg-amber-600 text-black p-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="font-extrabold text-base">Low Stock Breakdown</h3>
                </div>
                <button onclick="closeLowStockModal()" class="text-black/70 hover:text-black text-lg font-bold transition">✕</button>
            </div>

            <!-- Items List -->
            <div class="p-4 max-h-[60vh] overflow-y-auto space-y-3">
                @if($lowStockIngredients->count() === 0)
                    <div class="text-center py-6 text-emerald-400 font-medium text-sm">
                        All raw ingredients are well stocked! 🎉
                    </div>
                @else
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Raw Ingredients (≤ 50% Capacity)</p>
                    @foreach($lowStockIngredients as $ing)
                        <div class="flex items-center justify-between p-3 bg-[#18191c] border border-zinc-800 rounded-lg">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $ing->ingredient_name }}</p>
                                <p class="text-xs text-zinc-400">Max Capacity: {{ $ing->max_capacity }} {{ $ing->unit ?? 'units' }}</p>
                            </div>
                            <span class="bg-amber-500/20 border border-amber-500/40 text-amber-400 text-xs font-bold px-2.5 py-1 rounded-full">
                                {{ $ing->current_stock }} {{ $ing->unit ?? '' }} left
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Footer Links -->
            <div class="p-4 bg-[#18191c] border-t border-zinc-800 flex justify-between items-center text-xs font-bold">
                <div>
                    <a href="{{ route('ingredients.index') }}" class="text-[#ff8c00] hover:underline">Ingredients →</a>
                </div>
                <button onclick="closeLowStockModal()" class="px-4 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg transition">Close</button>
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