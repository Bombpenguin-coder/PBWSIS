@extends('layouts.app')

@section('title', 'KOT / Counter Tickets')
@section('header_title', 'Kitchen Order Tickets')

@section('content')
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Live Kitchen Queue</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tickets as $ticket)
                <div class="border-2 rounded-xl p-4 bg-white flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center border-b pb-2 mb-3">
                            <span class="font-bold text-xs text-gray-800">{{ $ticket->order_number }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-100 text-amber-800">
                                {{ $ticket->kot_status }}
                            </span>
                        </div>
                        <div class="space-y-2 mb-4">
                            @foreach($ticket->items as $item)
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-red-900 bg-red-50 px-2 py-0.5 rounded">{{ $item->quantity }}x</span>
                                    <span class="font-semibold text-gray-800 flex-1 ml-2">{{ $item->product->product_name ?? 'Item' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <form action="{{ route('operations.kot.update', $ticket->order_id) }}" method="POST" class="pt-3 border-t">
                        @csrf
                        @method('PUT')
                        @if($ticket->kot_status === 'pending')
                            <button type="submit" name="kot_status" value="preparing" class="w-full bg-blue-600 text-white text-xs py-1.5 rounded-lg">Start Preparing</button>
                        @elseif($ticket->kot_status === 'preparing')
                            <button type="submit" name="kot_status" value="ready" class="w-full bg-emerald-600 text-white text-xs py-1.5 rounded-lg">Mark as Ready</button>
                        @else
                            <button type="submit" name="kot_status" value="served" class="w-full bg-gray-800 text-white text-xs py-1.5 rounded-lg">Mark as Served</button>
                        @endif
                    </form>
                </div>
            @empty
                <p class="col-span-full text-center py-8 text-gray-400 text-xs">No active kitchen tickets.</p>
            @endforelse
        </div>
    </div>
@endsection