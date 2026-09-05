@extends('layouts.app')

@section('title', 'KOT / Counter Tickets')
@section('header_title', 'Kitchen Order Tickets')

@section('content')
    @if(session('success'))
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded-r text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <h2 class="text-lg font-bold text-white mb-4">Live Kitchen Queue</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tickets as $ticket)
                <div class="border border-zinc-800 rounded-xl p-4 bg-[#202226] flex flex-col justify-between hover:border-zinc-700 transition-colors">
                    <div>
                        <div class="flex justify-between items-center border-b border-zinc-800 pb-2 mb-3">
                            <span class="font-bold text-xs text-[#ff8c00]">{{ $ticket->order_number }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400">
                                {{ $ticket->kot_status }}
                            </span>
                        </div>
                        <div class="space-y-2 mb-4">
                            @foreach($ticket->items as $item)
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-[#ff8c00] bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">{{ $item->quantity }}x</span>
                                    <span class="font-semibold text-zinc-200 flex-1 ml-2">{{ $item->product->product_name ?? 'Item' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <form action="{{ route('operations.kot.update', $ticket->order_id) }}" method="POST" class="pt-3 border-t border-zinc-800">
                        @csrf
                        @method('PUT')
                        @if($ticket->kot_status === 'pending')
                            <button type="submit" name="kot_status" value="preparing" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs py-1.5 rounded-lg transition-colors font-semibold">Start Preparing</button>
                        @elseif($ticket->kot_status === 'preparing')
                            <button type="submit" name="kot_status" value="ready" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs py-1.5 rounded-lg transition-colors font-semibold">Mark as Ready</button>
                        @else
                            <button type="submit" name="kot_status" value="served" class="w-full bg-zinc-700 hover:bg-zinc-600 text-white text-xs py-1.5 rounded-lg transition-colors font-semibold">Mark as Served</button>
                        @endif
                    </form>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-zinc-500 text-xs">No active kitchen tickets.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection