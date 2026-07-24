@extends('layouts.app')

@section('title', 'Ingredient Management')
@section('header_title', 'Ingredient Maintenance')

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
        
        <!-- Add Ingredient Form -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900">
            <h2 class="text-lg font-bold mb-4 text-black">Add Raw Ingredient</h2>
            <form action="{{ route('ingredients.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="ingredient_name">Ingredient Name</label>
                    <input type="text" name="ingredient_name" id="ingredient_name" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-2" for="quantity">Initial Qty</label>
                        <input type="number" step="0.01" name="quantity" id="quantity" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2" for="unit">Unit (e.g., kg, pcs)</label>
                        <input type="text" name="unit" id="unit" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="max_capacity">Max Capacity</label>
                    <input type="number" step="0.01" name="max_capacity" id="max_capacity" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                    <p class="text-xs text-gray-500 mt-1">Used to calculate the 50% low-stock alert.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="reorder_level">Reorder Level</label>
                    <input type="number" step="0.01" name="reorder_level" id="reorder_level" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Save Ingredient
                </button>
            </form>
        </div>

        <!-- Ingredient List Table -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 text-black">Current Raw Materials</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Ingredient Name</th>
                            <th class="py-2 px-4 border-b text-left">Current Qty</th>
                            <th class="py-2 px-4 border-b text-left">Unit</th>
                            <th class="py-2 px-4 border-b text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredients as $ingredient)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2 px-4 border-b">{{ $ingredient->ingredient_id }}</td>
                                <td class="py-2 px-4 border-b">{{ $ingredient->ingredient_name }}</td>
                                <td class="py-2 px-4 border-b font-bold {{ $ingredient->quantity <= ($ingredient->max_capacity * 0.50) ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $ingredient->quantity }}
                                </td>
                                <td class="py-2 px-4 border-b">{{ $ingredient->unit }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if($ingredient->quantity <= ($ingredient->max_capacity * 0.50))
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800">Low Stock</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">Good</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">No ingredients found. Start by adding one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $ingredients->links() }}
            </div>
        </div>
    </div>
@endsection