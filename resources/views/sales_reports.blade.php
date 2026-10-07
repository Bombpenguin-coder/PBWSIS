@extends('layouts.app')

@section('header_title', 'Monthly Reports')

@section('content')
<style>
/* Override Global POS Receipt Print Styles specifically for Reports */
@media print {
    /* Hide all layout elements (Sidebars, Topbars, Nav, Buttons) */
    aside, nav, header, footer, .no-print, button, form {
        display: none !important;
    }

    /* Reset background and text color for clear printing */
    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: Arial, sans-serif !important;
    }

    /* Target the reports container and make it full width */
    body * {
        visibility: visible !important;
    }

    .max-w-7xl {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Clean up table formatting for print */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 20px !important;
    }

    th, td {
        border: 1px solid #d1d5db !important;
        padding: 8px 12px !important;
        color: #000000 !important;
        background: transparent !important;
    }

    /* Cards layout adjustment for printing */
    .grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    .bg-\[\#18191c\], .bg-brand-orange {
        background: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
        box-shadow: none !important;
    }

    .text-white, .text-zinc-400, .text-zinc-300, .text-brand-orange {
        color: #000000 !important;
    }

    @page {
        size: A4 portrait;
        margin: 15mm;
    }
}
</style>

<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Filter Bar -->
    <div class="bg-[#18191c] p-6 rounded-2xl shadow-sm border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            
            <div>
                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Select Month</label>
                <select name="month" id="monthSelect" class="bg-[#202226] border border-zinc-700 rounded-lg px-3 py-2 text-sm font-semibold text-white focus:ring-2 focus:ring-brand-orange focus:outline-none">
                    @foreach(range(1, 12) as $m)
                        @php
                            $isFuture = ($selectedYear == date('Y') && $m > date('n'));
                        @endphp
                        <option value="{{ $m }}" 
                                {{ $selectedMonth == $m ? 'selected' : '' }}
                                {{ $isFuture ? 'disabled hidden' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Select Year</label>
                <select name="year" id="yearSelect" onchange="updateMonthOptions()" class="bg-[#202226] border border-zinc-700 rounded-lg px-3 py-2 text-sm font-semibold text-white focus:ring-2 focus:ring-brand-orange focus:outline-none">
                    @php
                        $startYear = 2018; // Establishment Year
                        $currentYear = (int) date('Y');
                    @endphp
                    @for ($y = $currentYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="bg-brand-orange hover:bg-orange-600 text-white font-bold px-5 py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
                Filter Report
            </button>
        </form>

        <button onclick="window.print()" class="bg-[#202226] hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold px-4 py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
            Print Report
        </button>
    </div>

    <!-- Navigation Tabs (Payment Methods Removed) -->
    <div class="border-b border-zinc-800 no-print">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('reports.index', ['tab' => 'summary', 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
               class="pb-4 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeTab === 'summary' ? 'border-brand-orange text-brand-orange' : 'border-transparent text-zinc-400 hover:text-zinc-200' }}">
               Sales Summary
            </a>
            <a href="{{ route('reports.index', ['tab' => 'transactions', 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
               class="pb-4 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeTab === 'transactions' ? 'border-brand-orange text-brand-orange' : 'border-transparent text-zinc-400 hover:text-zinc-200' }}">
               Monthly Orders
            </a>
            <a href="{{ route('reports.index', ['tab' => 'bestsellers', 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
               class="pb-4 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeTab === 'bestsellers' ? 'border-brand-orange text-brand-orange' : 'border-transparent text-zinc-400 hover:text-zinc-200' }}">
               Best Sellers
            </a>
        </nav>
    </div>

    <!-- Printable Report Header (Visible only when printed) -->
    <div class="hidden print:block mb-6 border-b pb-4">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-black uppercase tracking-wide">Prince Buffalo Wings</h1>
                <p class="text-sm text-gray-600 font-semibold">Sales & Performance Summary Report</p>
            </div>
            <div class="text-right text-xs text-gray-500">
                <p><span class="font-bold text-black">Period:</span> {{ $reportDateTitle }}</p>
                <p><span class="font-bold text-black">Generated on:</span> {{ date('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
    
    <!-- TAB 1: Sales Summary Cards -->
    @if($activeTab === 'summary')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-brand-orange text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-orange-100 uppercase tracking-wider">Total Monthly Income</p>
            <p class="text-3xl font-black mt-3">₱{{ number_format($totalSales, 2) }}</p>
        </div>
        
        <div class="bg-[#18191c] p-6 rounded-2xl shadow-sm border border-zinc-800 flex flex-col justify-between">
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Total Completed Orders</p>
            <p class="text-3xl font-black text-white mt-3">{{ $totalOrders }} <span class="text-sm font-bold text-zinc-400">Orders</span></p>
        </div>

        <div class="bg-[#18191c] p-6 rounded-2xl shadow-sm border border-zinc-800 flex flex-col justify-between">
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">VAT Collected</p>
            <p class="text-3xl font-black text-white mt-3">₱{{ number_format($totalVat, 2) }}</p>
        </div>

        <div class="bg-[#18191c] p-6 rounded-2xl shadow-sm border border-zinc-800 flex flex-col justify-between">
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Total Wastage Cost</p>
            <p class="text-3xl font-black text-white mt-3">₱{{ number_format($totalWastageCost, 2) }}</p>
        </div>
    </div>
    @endif

    <!-- TAB 2: All Transactions -->
    @if($activeTab === 'transactions')
    <div class="bg-[#18191c] rounded-2xl shadow-sm border border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
            <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">All Transactions for {{ strtoupper($reportDateTitle) }}</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#202226] border-b border-zinc-800 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Order ID</th>
                        <th class="py-4 px-6">Date & Time</th>
                        <th class="py-4 px-6">Channel</th>
                        <th class="py-4 px-6">Items Included</th>
                        <th class="py-4 px-6 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($monthlySalesList as $sale)
                    <tr class="hover:bg-[#202226]/50 transition">
                        <td class="py-4 px-6 font-bold text-brand-orange">{{ $sale->order_number }}</td>
                        <td class="py-4 px-6 text-zinc-400 font-medium">{{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y — h:i A') }}</td>
                        <td class="py-4 px-6">
                            <span class="bg-[#202226] text-zinc-300 border border-zinc-700 text-[11px] font-bold px-2.5 py-1 rounded uppercase tracking-wider">
                                {{ $sale->order_channel ?? 'WALK-IN' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-zinc-300 font-medium space-y-0.5">
                            @if($sale->details && $sale->details->count() > 0)
                                @foreach($sale->details as $detail)
                                    <div><span class="font-bold text-white">{{ $detail->quantity }}x</span> {{ $detail->product->product_name ?? 'Product' }}</div>
                                @endforeach
                            @else
                                <span class="text-zinc-500">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right font-black text-white">₱{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-zinc-500 font-medium">No transactions recorded for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 3: Best Sellers -->
    @if($activeTab === 'bestsellers')
    <div class="bg-[#18191c] rounded-2xl shadow-sm border border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
            <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Top Performing Products</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#202226] border-b border-zinc-800 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Product Name</th>
                        <th class="py-4 px-6">Quantity Sold</th>
                        <th class="py-4 px-6 text-right">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($bestSellers as $item)
                    <tr class="hover:bg-[#202226]/50 transition">
                        <td class="py-4 px-6 font-bold text-white">{{ $item->product_name }}</td>
                        <td class="py-4 px-6 text-zinc-300 font-bold">{{ $item->total_qty }} <span class="text-xs text-zinc-400 font-normal">units</span></td>
                        <td class="py-4 px-6 text-right font-black text-brand-orange">₱{{ number_format($item->total_revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-zinc-500 font-medium">No product sales recorded for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
function updateMonthOptions() {
    const yearSelect = document.getElementById('yearSelect');
    const monthSelect = document.getElementById('monthSelect');
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;

    const selectedYear = parseInt(yearSelect.value);

    Array.from(monthSelect.options).forEach(option => {
        const monthVal = parseInt(option.value);
        if (selectedYear === currentYear && monthVal > currentMonth) {
            option.disabled = true;
            option.hidden = true;
            if (option.selected) {
                monthSelect.value = currentMonth;
            }
        } else {
            option.disabled = false;
            option.hidden = false;
        }
    });
}
</script>
@endsection