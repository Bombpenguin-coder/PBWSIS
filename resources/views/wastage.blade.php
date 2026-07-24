@extends('layouts.app')

@section('title', 'Wastage Logs')
@section('header_title', 'Wastage & Spoilage Tracking')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Log Wastage Form -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900">
            <h2 class="text-lg font-bold mb-4 text-black">Record Spoilage</h2>
            <form action="{{ route('wastage.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="ingredient_id">Ingredient</label>
                    <select name="ingredient_id" id="ingredient_id" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900 bg-white">
                        <option value="" disabled selected>Select an ingredient...</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->ingredient_id }}">{{ $ingredient->ingredient_name }} (Current: {{ $ingredient->quantity }} {{ $ingredient->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="quantity_wasted">Quantity Wasted</label>
                    <input type="number" step="0.01" name="quantity_wasted" id="quantity_wasted" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="reason">Reason for Wastage</label>
                    <input type="text" name="reason" id="reason" placeholder="e.g., Expired, Dropped, Burned" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="wastage_date">Date</label>
                    <input type="date" name="wastage_date" id="wastage_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Log & Deduct Inventory
                </button>
            </form>
        </div>

        <!-- Wastage History Table -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 text-black">Wastage History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Date</th>
                            <th class="py-2 px-4 border-b text-left">Ingredient</th>
                            <th class="py-2 px-4 border-b text-left">Qty Lost</th>
                            <th class="py-2 px-4 border-b text-left">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wastages as $log)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2 px-4 border-b text-gray-600">{{ \Carbon\Carbon::parse($log->wastage_date)->format('M d, Y') }}</td>
                                <td class="py-2 px-4 border-b font-semibold">{{ $log->ingredient->ingredient_name ?? 'Unknown' }}</td>
                                <td class="py-2 px-4 border-b text-red-600 font-bold">
                                    -{{ $log->quantity_wasted }} {{ $log->ingredient->unit ?? '' }}
                                </td>
                                <td class="py-2 px-4 border-b">{{ $log->reason }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">No wastage records found. Great job minimizing waste!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $wastages->links() }}
            </div>
        </div>
    </div>
@endsection