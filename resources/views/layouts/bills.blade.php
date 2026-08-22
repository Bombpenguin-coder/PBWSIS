@extends('layouts.app')

@section('title', 'Bills')
@section('header_title', 'Bills')

@section('content')
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Billing & Payment Processing</h2>
        <div class="overflow-x-auto rounded-lg border">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-gray-100 text-xs uppercase">
                    <tr>
                        <th class="py-3 px-4 text-left">Order #</th>
                        <th class="py-3 px-4 text-left">Customer / Table</th>
                        <th class="py-3 px-4 text-left">Total</th>
                        <th class="py-3 px-4 text-center">Payment Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($bills as $bill)
                        <tr>
                            <td class="py-3 px-4 font-bold text-red-900 text-xs">{{ $bill->order_number }}</td>
                            <td class="py-3 px-4 text-xs">{{ $bill->customer_name ?? 'Guest' }} (Table: {{ $bill->table_number ?? 'N/A' }})</td>
                            <td class="py-3 px-4 font-bold text-xs">₱{{ number_format($bill->total_amount, 2) }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($bill->payment_status === 'paid')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-xs">Paid ({{ $bill->payment_method }})</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($bill->payment_status === 'unpaid')
                                    <form action="{{ route('operations.pay', $bill->order_id) }}" method="POST" class="inline-flex gap-1">
                                        @csrf
                                        <select name="payment_method" required class="text-xs border rounded p-1">
                                            <option value="Cash">Cash</option>
                                            <option value="GCash">GCash</option>
                                            <option value="Card">Card</option>
                                        </select>
                                        <button type="submit" class="bg-red-900 text-white text-xs px-3 py-1 rounded">Pay</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-xs">No bills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection