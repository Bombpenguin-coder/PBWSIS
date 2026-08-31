@extends('layouts.app')

@section('title', 'Held Orders')
@section('header_title', 'Held Orders')

@section('content')
    @if(session('success'))
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded-r text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <h2 class="text-lg font-bold text-white mb-4">Pending & Held Orders</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($heldOrders as $order)
                <div class="border border-zinc-800 rounded-xl p-4 bg-[#202226] flex flex-col justify-between hover:border-zinc-700 transition-colors">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-[#ff8c00] text-xs tracking-wide">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 bg-amber-500/20 border border-amber-500/30 text-amber-400 rounded-full text-[10px] font-bold">HELD</span>
                        </div>
                        <p class="text-xs font-semibold text-zinc-200">Table: {{ $order->table_number ?? 'N/A' }}</p>
                        <p class="text-[11px] text-zinc-400 mb-2">Customer: {{ $order->customer_name ?? 'Guest' }}</p>

                        <div class="border-t border-b border-zinc-800/80 py-2 my-2 space-y-1">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-300">{{ $item->quantity }}x {{ $item->product->product_name ?? 'Item' }}</span>
                                    <span class="text-zinc-400">₱{{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-white">₱{{ number_format($order->total_amount, 2) }}</span>
                        <a href="{{ route('operations.bills') }}" class="bg-[#ff8c00] hover:bg-[#e07b00] text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                            Recall to Bill
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-zinc-500 text-xs">No held orders available right now.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection