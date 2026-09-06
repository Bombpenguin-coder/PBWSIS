@extends('layouts.app')

@section('title', 'Bills')
@section('header_title', 'Bills')

@section('content')
    @if(session('success'))
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded-r text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <h2 class="text-lg font-bold text-white mb-4">Billing & Payment Processing</h2>
        <div class="overflow-x-auto rounded-lg border border-zinc-800">
            <table class="min-w-full divide-y divide-zinc-800">
                <thead class="bg-[#202226] text-zinc-300 text-xs uppercase">
                    <tr>
                        <th class="py-3 px-4 text-left">Order #</th>
                        <th class="py-3 px-4 text-left">Customer / Table</th>
                        <th class="py-3 px-4 text-left">Total</th>
                        <th class="py-3 px-4 text-center">Payment Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-[#202226]/50 transition-colors">
                            <td class="py-3 px-4 font-bold text-[#ff8c00] text-xs">{{ $bill->order_number }}</td>
                            <td class="py-3 px-4 text-xs text-zinc-300">{{ $bill->customer_name ?? 'Guest' }} (Table: {{ $bill->table_number ?? 'N/A' }})</td>
                            <td class="py-3 px-4 font-bold text-xs text-white">₱{{ number_format($bill->total_amount, 2) }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($bill->payment_status === 'paid')
                                    <span class="px-2 py-0.5 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-full text-xs font-semibold">Paid ({{ $bill->payment_method }})</span>
                                @else
                                    <span class="px-2 py-0.5 bg-rose-500/20 border border-rose-500/30 text-rose-400 rounded-full text-xs font-semibold">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($bill->payment_status === 'unpaid')
                                    <form action="{{ route('operations.pay', $bill->order_id) }}" method="POST" class="inline-flex gap-2 items-center">
                                        @csrf
                                        <select name="payment_method" required class="text-xs bg-[#202226] text-zinc-200 border border-zinc-700 rounded-lg p-1.5 focus:outline-none focus:border-[#ff8c00]">
                                            <option value="Cash">Cash</option>
                                            <option value="GCash">GCash</option>
                                            <option value="Card">Card</option>
                                        </select>
                                        <button type="submit" class="bg-[#ff8c00] hover:bg-[#e07b00] text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">Pay</button>
                                    </form>
                                @else
                                    <span class="text-xs text-zinc-500">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-zinc-500 text-xs">No bills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection