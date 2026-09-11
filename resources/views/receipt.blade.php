@extends('layouts.app')

@section('title', 'Receipt #' . $sale->sale_id)

@section('content')
<style>
    /* Screen View */
    .receipt-container {
        max-width: 380px;
        margin: 0 auto;
        background: #ffffff;
        color: #000000;
        font-family: 'Courier New', Courier, monospace;
    }

    /* Print View Rules */
    @media print {
        body * {
            visibility: hidden;
        }
        .receipt-container, .receipt-container * {
            visibility: visible;
        }
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<!-- Action Buttons -->
<div class="mb-6 flex items-center justify-between max-w-[380px] mx-auto no-print">
    <a href="{{ route('pos') }}" class="px-3 py-1.5 bg-[#202226] hover:bg-zinc-700 text-zinc-300 text-xs font-bold rounded-lg transition border border-zinc-700">
        ← Back to POS
    </a>
    <button onclick="window.print()" class="px-4 py-1.5 bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold rounded-lg transition shadow-sm">
        Print Receipt
    </button>
</div>

<!-- Receipt Content Container -->
<div class="receipt-container p-6 rounded-xl shadow-lg border border-zinc-200">
    <div class="text-center mb-4">
        <h2 class="text-lg font-bold tracking-tight uppercase">Prince Buffalo Wings</h2>
        <p class="text-xs text-gray-600">123 Flavor Street, Taguig City</p>
        <p class="text-xs text-gray-600 mt-1">Order #: ORD-{{ \Carbon\Carbon::parse($sale->sale_date)->format('Ymd') }}-{{ sprintf('%04d', $sale->sale_id) }}</p>
    </div>

    <div class="border-b border-dashed border-gray-400 my-3"></div>

    <!-- Items List -->
    <div class="space-y-1.5 text-xs">
        @foreach($sale->details as $detail)
            <div class="flex justify-between items-start">
                <span>{{ $detail->quantity }}x {{ $detail->product->product_name ?? 'Item' }}</span>
                <span class="font-semibold">₱{{ number_format($detail->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="border-b border-dashed border-gray-400 my-3"></div>

    <!-- Totals Breakdown -->
    <div class="space-y-1 text-xs">
        <div class="flex justify-between text-gray-600">
            <span>Subtotal:</span>
            <span>₱{{ number_format($sale->subtotal ?? $sale->total_amount, 2) }}</span>
        </div>
        
        @if(($sale->discount_amount ?? 0) > 0)
            <div class="flex justify-between text-gray-600">
                <span>Discount:</span>
                <span>-₱{{ number_format($sale->discount_amount, 2) }}</span>
            </div>
        @endif

        <div class="flex justify-between font-bold text-sm text-black pt-1">
            <span>Total Amount:</span>
            <span>₱{{ number_format($sale->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="border-b border-dashed border-gray-400 my-3"></div>

    <!-- Footer -->
    <div class="text-center text-[11px] text-gray-600 space-y-1">
        <p>Cashier: {{ $sale->user->name ?? 'Staff' }}</p>
        <p>Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d h:i A') }}</p>
        <p class="pt-2 font-semibold text-black">Thank you for your order!</p>
    </div>
</div>
@endsection