@extends('layouts.app')

@section('title', 'Inventory Management')
@section('header_title', 'File Maintenance')

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
        
        <!-- Add Product Form -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-900">
            <h2 class="text-lg font-bold mb-4 text-black">Add New Product</h2>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="product_name">Product Name</label>
                    <input type="text" name="product_name" id="product_name" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="price">Price (₱)</label>
                    <input type="number" step="0.01" name="price" id="price" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="stock_quantity">Initial Stock</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="status">Status</label>
                    <select name="status" id="status" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-900">
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Save Product
                </button>
            </form>
        </div>

        <!-- Inventory List Table -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 text-black">Current Products</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Product Name</th>
                            <th class="py-2 px-4 border-b text-left">Price</th>
                            <th class="py-2 px-4 border-b text-left">Stock</th>
                            <th class="py-2 px-4 border-b text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2 px-4 border-b">{{ $product->product_id }}</td>
                                <td class="py-2 px-4 border-b">{{ $product->product_name }}</td>
                                <td class="py-2 px-4 border-b">₱{{ number_format($product->price, 2) }}</td>
                                <td class="py-2 px-4 border-b font-bold {{ $product->stock_quantity < 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $product->stock_quantity }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $product->status === 'Available' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $product->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">No products found. Start by adding one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection