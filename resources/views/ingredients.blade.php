@extends('layouts.app')

@section('title', 'Ingredient Management')
@section('header_title', 'Ingredient Maintenance')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="flex items-center bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded shadow-sm text-xs font-semibold">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 mb-6 rounded shadow-sm text-xs font-semibold">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Container -->
    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h2 class="text-lg font-bold text-white">Current Raw Materials</h2>
                <p class="text-xs text-zinc-400">Live inventory levels and health status</p>
            </div>
          <button type="button" onclick="openAddModal()" class="bg-[#800000] hover:bg-[#660000] text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    Add Ingredient
</button>
        </div>

        <!-- Ingredient List Table -->
        <div class="overflow-x-auto rounded-lg border border-zinc-800">
            <table class="min-w-full divide-y divide-zinc-800">
                <thead class="bg-[#202226] text-zinc-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Ingredient</th>
                        <th class="py-3 px-4 text-left font-semibold">Current Stock</th>
                        <th class="py-3 px-4 text-left font-semibold">Capacity Bar</th>
                        <th class="py-3 px-4 text-center font-semibold">Status</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm bg-[#18191c]">
                    @forelse($ingredients as $ingredient)
                        @php
                            $percent = $ingredient->max_capacity > 0 
                                ? min(100, round(($ingredient->quantity / $ingredient->max_capacity) * 100)) 
                                : 0;
                            $isLow = $ingredient->quantity <= ($ingredient->reorder_threshold ?? ($ingredient->max_capacity * 0.15));
                        @endphp
                        <tr class="hover:bg-[#202226]/60 transition duration-150">
                            <td class="py-3 px-4 font-semibold text-white">{{ $ingredient->ingredient_name }}</td>
                            <td class="py-3 px-4 font-bold {{ $isLow ? 'text-rose-400' : 'text-emerald-400' }}">
                                {{ number_format($ingredient->quantity, 2) }} <span class="text-xs font-normal text-zinc-400">{{ $ingredient->unit }}</span>
                            </td>
                            
                            <!-- Stock Progress Bar -->
                            <td class="py-3 px-4 w-32">
                                <div class="w-full bg-zinc-800 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $isLow ? 'bg-rose-500' : 'bg-emerald-500' }} h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-mono">{{ $percent }}% of {{ $ingredient->max_capacity }} {{ $ingredient->unit }}</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-4 text-center">
                                @if($isLow)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 border border-rose-500/20 text-rose-400">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                                        Optimal
                                    </span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                          <!-- Actions Column -->
<td class="py-3 px-4 text-center">
    <div class="flex items-center justify-center gap-2">
        <!-- Edit Button -->
     <button type="button" 
    onclick="openEditModal('{{ route('inventory.ingredients.update', $ingredient->ingredient_id) }}', 'Edit Ingredient', [
        { label: 'Ingredient Name', name: 'ingredient_name', value: '{{ addslashes($ingredient->ingredient_name) }}', required: true },
        { label: 'Stock Quantity', name: 'quantity', type: 'number', value: '{{ (float)$ingredient->quantity }}', required: true },
        { label: 'Unit (e.g. g, ml, pcs)', name: 'unit', value: '{{ $ingredient->unit }}', required: true }
    ])" 
    class="text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 px-3 py-1.5 rounded-md transition duration-150">
    Edit
</button>

        <!-- Delete Button Form -->
    <form id="delete-ingredient-form-{{ $ingredient->ingredient_id }}" action="{{ route('ingredients.destroy', $ingredient->ingredient_id) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="button" 
            onclick="triggerDelete('delete-ingredient-form-{{ $ingredient->ingredient_id }}', 'Are you sure you want to delete {{ addslashes($ingredient->ingredient_name) }}?')" 
            class="text-xs font-semibold text-red-500 hover:text-white bg-red-500/10 hover:bg-red-600 px-3 py-1.5 rounded-md transition duration-150">
        Delete
    </button>
</form>
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-4 text-center text-zinc-500">
                                No raw ingredients found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= DARK MODAL POPUP FORM ================= -->
    <div id="addProductModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-white">
                    Add Raw Ingredient
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <!-- Modal Body Form -->
          <form action="{{ route('ingredients.store') }}" method="POST">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_ingredient_name">Ingredient Name</label>
                    <input type="text" name="ingredient_name" id="modal_ingredient_name" value="{{ old('ingredient_name') }}" required placeholder="e.g., Espresso Beans" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('ingredient_name')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_quantity">Initial Qty</label>
                        <input type="number" step="0.01" name="quantity" id="modal_quantity" value="{{ old('quantity') }}" required placeholder="0.00" 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                        @error('quantity')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_unit">Unit</label>
                        <input type="text" name="unit" id="modal_unit" value="{{ old('unit') }}" required placeholder="kg, L, pcs" 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                        @error('unit')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_max_capacity">Max Storage Capacity</label>
                    <input type="number" step="0.01" name="max_capacity" id="modal_max_capacity" value="{{ old('max_capacity') }}" required placeholder="100.00" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('max_capacity')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_reorder_level">Reorder Threshold</label>
                    <input type="number" step="0.01" name="reorder_level" id="modal_reorder_level" value="{{ old('reorder_level') }}" required placeholder="10.00" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('reorder_level')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800 mt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-[#202226] hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition border border-zinc-700">
                        Cancel
                    </button>
                  <button type="submit" class="bg-[#800000] hover:bg-[#660000] text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
    + Add Ingredient
</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dark Custom Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all flex flex-col">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4">
            <h3 class="text-base font-bold text-white">Confirm Deletion</h3>
            <button type="button" onclick="closeDeleteModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
        </div>
        <p class="text-xs text-zinc-300 mb-6" id="deleteModalMessage">Are you sure you want to perform this action?</p>
        <div class="flex justify-end space-x-2 border-t border-zinc-800 pt-3">
            <button type="button" onclick="closeDeleteModal()" class="bg-[#202226] hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition border border-zinc-700">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBtn" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
                Delete
            </button>
        </div>
    </div>
</div>

    <script>
        function openAddModal() {
            document.getElementById('addProductModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addProductModal').classList.add('hidden');
        }

        @if ($errors->any())
            openAddModal();
        @endif
        
        let targetFormId = null;

function triggerDelete(formId, message = 'Are you sure you want to delete this item?') {
    targetFormId = formId;
    document.getElementById('deleteModalMessage').textContent = message;
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
}

function closeDeleteModal() {
    targetFormId = null;
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (targetFormId) {
        document.getElementById(targetFormId).submit();
    }
});
    </script>
@endsection