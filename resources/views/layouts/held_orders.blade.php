@extends('layouts.app')

@section('title', 'Held Orders')
@section('header_title', 'Held Orders')

@section('content')
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Pending & Held Orders</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($heldOrders as $order)
                <div class="border rounded-xl p-4 bg-gray-50 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-red-900 text-xs">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">HELD</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-800">Table: {{ $order->table_number ?? 'N/A' }}</p>
                        <p class="text-[11px] text-gray-500 mb-2">Customer: {{ $order->customer_name ?? 'Guest' }}</p>

                        <div class="border-t border-b py-2 my-2 space-y-1">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-xs">
                                    <span>{{ $item->quantity }}x {{ $item->product->product_name ?? 'Item' }}</span>
                                    <span>₱{{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
                        <a href="{{ route('operations.bills') }}" class="bg-red-900 text-white text-xs px-3 py-1.5 rounded-lg">Recall to Bill</a>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center py-8 text-gray-400 text-xs">No held orders available right now.</p>
            @endforelse
        </div>
    </div>
@endsection