@extends('layouts.app')

@section('title', 'Inventory Management')
@section('header_title', 'Product Maintenance')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="flex items-center bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Container -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Current Products</h2>
                <p class="text-xs text-gray-500">Live product catalog and stock levels</p>
            </div>
            
            <button type="button" onclick="openAddModal()" class="bg-red-900 hover:bg-red-800 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </button>
        </div>

        <!-- Product List Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-gray-100 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-medium">ID</th>
                        <th class="py-3 px-4 text-left font-medium">Product Name</th>
                        <th class="py-3 px-4 text-left font-medium">Price</th>
                        <th class="py-3 px-4 text-left font-medium">Stock Level</th>
                        <th class="py-3 px-4 text-center font-medium">Status</th>
                        <th class="py-3 px-4 text-center font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm bg-white">
                    @forelse($products as $product)
                        @php
                            $isLow = $product->stock_quantity < 10;
                            $isAvailable = $product->status === 'Available';
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition duration-150">
                            <td class="py-3 px-4 text-gray-500 font-mono text-xs">#{{ $product->product_id }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ $product->product_name }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-700">₱{{ number_format($product->price, 2) }}</td>
                            
                            <!-- Stock Level Indicator -->
                            <td class="py-3 px-4 font-bold {{ $isLow ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $product->stock_quantity }} <span class="text-xs font-normal text-gray-500">pcs</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-4 text-center">
                                @if($isAvailable)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Available
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Unavailable
                                    </span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="py-3 px-4 text-center space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                    onclick="openEditModal('/inventory/{{ $product->product_id }}', 'Edit Product', [
                                        { label: 'Product Name', name: 'product_name', value: '{{ addslashes($product->product_name) }}', required: true },
                                        { label: 'Price (₱)', name: 'price', type: 'number', value: '{{ $product->price }}', required: true },
                                        { label: 'Stock Quantity', name: 'stock_quantity', type: 'number', value: '{{ $product->stock_quantity }}', required: true }
                                    ])" 
                                    class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 px-3 py-1 rounded-md transition duration-150">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline"> 
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-800 px-3 py-1 rounded-md transition duration-150">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-400">
                                No products found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= BLURRED MODAL POPUP FORM ================= -->
    <div id="addProductModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-gray-800">
                    Add New Product
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('products.store') }}" method="POST" class="overflow-y-auto pr-1">
                @csrf
                
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_product_name">Product Name</label>
                    <input type="text" name="product_name" id="modal_product_name" value="{{ old('product_name') }}" required placeholder="e.g., Iced Caramel Macchiato" 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('product_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_price">Price (₱)</label>
                        <input type="number" step="0.01" name="price" id="modal_price" value="{{ old('price') }}" required placeholder="0.00" 
                               class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_stock_quantity">Initial Stock</label>
                        <input type="number" name="stock_quantity" id="modal_stock_quantity" value="{{ old('stock_quantity') }}" required placeholder="0" 
                               class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                        @error('stock_quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_status">Status</label>
                    <select name="status" id="modal_status" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent bg-white">
                        <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Unavailable" {{ old('status') == 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-2 px-3 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
                        + Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts to Toggle Modal -->
    <script>
        function openAddModal() {
            document.getElementById('addProductModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addProductModal').classList.add('hidden');
        }

        // Auto open modal if validation errors exist on submit
        @if ($errors->any())
            openAddModal();
        @endif
    </script>
@endsection