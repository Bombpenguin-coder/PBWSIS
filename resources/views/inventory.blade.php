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
                        <th class="py-3 px-4 text-left font-medium">Product Details</th>
                        <th class="py-3 px-4 text-left font-medium">Price</th>
                        <th class="py-3 px-4 text-left font-medium">Recipe / Ingredients</th>
                        <th class="py-3 px-4 text-center font-medium">Status</th>
                        <th class="py-3 px-4 text-center font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm bg-white">
                    @forelse($products as $product)
                        @php
                            $isAvailable = $product->status === 'Available';
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition duration-150">
                            <td class="py-3 px-4 font-semibold text-gray-800 flex items-center gap-3">
    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" 
             alt="{{ $product->product_name }}" 
             class="w-10 h-10 object-cover rounded-lg border border-gray-200">
    @else
        <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] text-gray-400">
            No Image
        </div>
    @endif
    <span>{{ $product->product_name }}</span>
</td>

                            <td class="py-3 px-4 font-semibold text-gray-700">₱{{ number_format($product->price, 2) }}</td>
                            
                            <!-- Ingredients List -->
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($product->ingredients as $ingredient)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $ingredient->ingredient_name }}: 
                                            <strong class="ml-1 text-gray-900">{{ $ingredient->pivot->quantity_needed }} {{ $ingredient->unit }}</strong>
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No ingredients assigned</span>
                                    @endforelse
                                </div>
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

                            <!-- Actions -->
                            <td class="py-3 px-4 text-center space-x-1">
                                <button type="button" 
    onclick="openEditModalWithData({{ json_encode($product) }})" 
    class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 px-3 py-1 rounded-md transition duration-150">
    Edit
</button>

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
                            <td colspan="5" class="py-8 px-4 text-center text-gray-400">
                                No products found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= MODAL POPUP FORM ================= -->
    <div id="addProductModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-base font-bold text-gray-800">Add New Product</h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto pr-1">
                @csrf

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_product_name">Product Name</label>
                    <input type="text" name="product_name" id="modal_product_name" value="{{ old('product_name') }}" required placeholder="e.g., Iced Caramel Macchiato" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                    @error('product_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Image Input -->
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_image">Product Photo</label>
                    <input type="file" name="image" id="modal_image" accept="image/*" class="w-full border border-gray-300 p-1.5 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 bg-gray-50">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_price">Price (₱)</label>
                    <input type="number" step="0.01" name="price" id="modal_price" value="{{ old('price') }}" required placeholder="0.00" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_status">Status</label>
                    <select name="status" id="modal_status" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                        <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Unavailable" {{ old('status') == 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ingredients Section -->
                <div class="mb-5 border-t border-gray-100 pt-3">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Required Ingredients
                        </label>
                        <button type="button" onclick="addProductIngredientRow()" class="text-xs font-bold text-red-900 hover:text-red-700">
                            + Add Ingredient
                        </button>
                    </div>

                    <div id="productIngredientsWrapper" class="space-y-2">
                        <div class="ingredient-row flex space-x-2 items-center">
                            <select name="ingredients[0][ingredient_id]" required class="w-2/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                                <option value="" disabled selected>Select Raw Ingredient</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->ingredient_id }}">
                                        {{ $ingredient->ingredient_name }} ({{ $ingredient->unit }})
                                    </option>
                                @endforeach
                            </select>

                            <input type="number" step="0.01" name="ingredients[0][quantity]" placeholder="Qty" required class="w-1/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">

                            <button type="button" onclick="removeProductIngredientRow(this)" class="text-gray-400 hover:text-red-600 font-bold text-sm px-1">&times;</button>
                        </div>
                    </div>
                    @error('ingredients')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-900 hover:bg-red-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">+ Save Product</button>
                </div>
            </form>
        </div>
    </div>

  <!-- Scripts -->
<script>
    const rawIngredientsList = @json($ingredients ?? []);
    let editIngredientIndex = 0;

    function openAddModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }

    let productIngredientIndex = 1;

    function addProductIngredientRow() {
        const wrapper = document.getElementById('productIngredientsWrapper');
        if (!wrapper) return;
        
        const row = document.createElement('div');
        row.className = 'ingredient-row flex space-x-2 items-center';
        row.innerHTML = `
            <select name="ingredients[${productIngredientIndex}][ingredient_id]" required class="w-2/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                <option value="" disabled selected>Select Raw Ingredient</option>
                @foreach($ingredients as $ingredient)
                    <option value="{{ $ingredient->ingredient_id }}">
                        {{ addslashes($ingredient->ingredient_name) }} ({{ $ingredient->unit }})
                    </option>
                @endforeach
            </select>

            <input type="number" step="0.01" name="ingredients[${productIngredientIndex}][quantity]" placeholder="Qty" required class="w-1/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">

            <button type="button" onclick="removeProductIngredientRow(this)" class="text-gray-400 hover:text-red-600 font-bold text-sm px-1">&times;</button>
        `;

        wrapper.appendChild(row);
        productIngredientIndex++;
    }

    function removeProductIngredientRow(button) {
        const wrapper = document.getElementById('productIngredientsWrapper');
        if (wrapper && wrapper.children.length > 1) {
            button.parentElement.remove();
        }
    }

    // --- CUSTOM EDIT MODAL LOADER ---
    function openEditModalWithData(product) {
        const fields = [
            { label: 'Product Name', name: 'product_name', value: product.product_name || '', required: true },
            { label: 'Price (₱)', name: 'price', type: 'number', value: product.price || '', required: true },
            { label: 'Product Photo (Optional)', name: 'image', type: 'file' }
        ];

        // Trigger base modal setup
        if (typeof window.openEditModal === 'function') {
            window.openEditModal('/inventory/' + product.product_id, 'Edit Product', fields);
        }

        setTimeout(() => {
            const forms = document.querySelectorAll('form');
            let editForm = null;

            forms.forEach(f => {
                if (f.action.includes('/inventory/')) editForm = f;
            });

            if (!editForm) return;

            editForm.setAttribute('enctype', 'multipart/form-data');

            // --- REPLACE OR INJECT STATUS SELECT DROPDOWN ---
            let statusContainer = editForm.querySelector('#editStatusContainer');
            if (statusContainer) statusContainer.remove();

            statusContainer = document.createElement('div');
            statusContainer.id = 'editStatusContainer';
            statusContainer.className = 'mb-3 text-left';
            statusContainer.innerHTML = `
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                <select name="status" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                    <option value="Available" ${product.status === 'Available' ? 'selected' : ''}>Available</option>
                    <option value="Unavailable" ${product.status === 'Unavailable' ? 'selected' : ''}>Unavailable</option>
                </select>
            `;

            // Insert Status field above Image or right before submit buttons
            const imageInput = editForm.querySelector('[name="image"]');
            if (imageInput && imageInput.parentNode) {
                imageInput.parentNode.parentNode.insertBefore(statusContainer, imageInput.parentNode);
            } else {
                const buttonContainer = editForm.querySelector('.flex.justify-end') || editForm.lastElementChild;
                editForm.insertBefore(statusContainer, buttonContainer);
            }

            // Image Preview Handler
            let oldPreview = editForm.querySelector('#editImagePreview');
            if (oldPreview) oldPreview.remove();

            if (product.image) {
                const imgInput = editForm.querySelector('[name="image"]');
                if (imgInput) {
                    const preview = document.createElement('div');
                    preview.id = 'editImagePreview';
                    preview.className = 'mt-1 mb-2 flex items-center gap-2';
                    preview.innerHTML = `
                        <img src="/storage/${product.image}" class="w-10 h-10 object-cover rounded-md border border-gray-200">
                        <span class="text-[11px] text-gray-500">Current photo</span>
                    `;
                    imgInput.parentNode.appendChild(preview);
                }
            }

            // --- INGREDIENTS SECTION ---
            let existingSection = document.getElementById('editIngredientsSection');
            if (existingSection) existingSection.remove();

            const section = document.createElement('div');
            section.id = 'editIngredientsSection';
            section.className = 'mb-4 border-t border-gray-100 pt-3 text-left';
            section.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Required Ingredients</label>
                    <button type="button" onclick="addEditIngredientRow()" class="text-xs font-bold text-red-900 hover:text-red-700">+ Add Ingredient</button>
                </div>
                <div id="editIngredientsWrapper" class="space-y-2"></div>
            `;

            const buttonContainer = editForm.querySelector('.flex.justify-end') || editForm.lastElementChild;
            editForm.insertBefore(section, buttonContainer);

            const wrapper = document.getElementById('editIngredientsWrapper');
            editIngredientIndex = 0;

            if (product.ingredients && product.ingredients.length > 0) {
                product.ingredients.forEach(ing => {
                    const qty = ing.pivot ? ing.pivot.quantity_needed : (ing.quantity_needed || '');
                    addEditIngredientRow(ing.ingredient_id, qty);
                });
            } else {
                addEditIngredientRow();
            }
        }, 10);
    }

    function addEditIngredientRow(selectedId = '', qty = '') {
        const wrapper = document.getElementById('editIngredientsWrapper');
        if (!wrapper) return;

        let options = '<option value="" disabled ' + (!selectedId ? 'selected' : '') + '>Select Raw Ingredient</option>';
        rawIngredientsList.forEach(ing => {
            const selected = ing.ingredient_id == selectedId ? 'selected' : '';
            options += `<option value="${ing.ingredient_id}" ${selected}>${ing.ingredient_name} (${ing.unit})</option>`;
        });

        const row = document.createElement('div');
        row.className = 'ingredient-row flex space-x-2 items-center';
        row.innerHTML = `
            <select name="ingredients[${editIngredientIndex}][ingredient_id]" required class="w-2/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
                ${options}
            </select>
            <input type="number" step="0.01" name="ingredients[${editIngredientIndex}][quantity]" value="${qty}" placeholder="Qty" required class="w-1/3 border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900">
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-red-600 font-bold text-sm px-1">&times;</button>
        `;

        wrapper.appendChild(row);
        editIngredientIndex++;
    }

    @if ($errors->any())
        openAddModal();
    @endif
</script>
@endsection