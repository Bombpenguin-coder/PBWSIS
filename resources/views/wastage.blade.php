@extends('layouts.app')

@section('title', 'Wastage Logs')
@section('header_title', 'Wastage & Spoilage Tracking')

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
                <h2 class="text-lg font-bold text-gray-800">Wastage History</h2>
                <p class="text-xs text-gray-500">Track raw material loss and spoilage logs</p>
            </div>
            
            <button type="button" onclick="openAddModal()" class="bg-red-900 hover:bg-red-800 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Record Spoilage
            </button>
        </div>

        <!-- Wastage History Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-gray-100 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-medium">Date</th>
                        <th class="py-3 px-4 text-left font-medium">Ingredient</th>
                        <th class="py-3 px-4 text-left font-medium">Qty Lost</th>
                        <th class="py-3 px-4 text-left font-medium">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm bg-white">
                    @forelse($wastages as $log)
                        <tr class="hover:bg-gray-50/80 transition duration-150">
                            <td class="py-3 px-4 text-gray-500 font-mono text-xs">
                                {{ \Carbon\Carbon::parse($log->wastage_date)->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-gray-800">
                                {{ $log->ingredient->ingredient_name ?? 'Unknown Ingredient' }}
                            </td>
                            <td class="py-3 px-4 text-red-600 font-bold">
                                -{{ $log->quantity_wasted }} <span class="text-xs font-normal text-gray-500">{{ $log->ingredient->unit ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">
                                    {{ $log->reason }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 px-4 text-center text-gray-400">
                                <p class="text-sm">No wastage records found.</p>
                                <p class="text-xs mt-1">Great job minimizing waste! Click "Record Spoilage" above to log an item.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $wastages->links() }}
        </div>
    </div>

    <!-- ================= BLURRED MODAL POPUP FORM ================= -->
    <div id="addProductModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-gray-800">
                    Record Spoilage
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('wastage.store') }}" method="POST" class="overflow-y-auto pr-1">
                @csrf
                
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_ingredient_id">Ingredient</label>
                    <select name="ingredient_id" id="modal_ingredient_id" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent bg-white">
                        <option value="" disabled selected>Select an ingredient...</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->ingredient_id }}" {{ old('ingredient_id') == $ingredient->ingredient_id ? 'selected' : '' }}>
                                {{ $ingredient->ingredient_name }} (Current: {{ $ingredient->quantity }} {{ $ingredient->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('ingredient_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_quantity_wasted">Quantity Wasted</label>
                    <input type="number" step="0.01" name="quantity_wasted" id="modal_quantity_wasted" value="{{ old('quantity_wasted') }}" required placeholder="0.00" 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('quantity_wasted')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_reason">Reason for Wastage</label>
                    <input type="text" name="reason" id="modal_reason" value="{{ old('reason') }}" placeholder="e.g., Expired, Dropped, Burned" required 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('reason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_wastage_date">Date</label>
                    <input type="date" 
                           name="wastage_date" 
                           id="modal_wastage_date" 
                           value="{{ old('wastage_date', date('Y-m-d')) }}" 
                           max="{{ date('Y-m-d') }}" 
                           onkeydown="return false;" 
                           required 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent bg-white cursor-pointer">
                    @error('wastage_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-2 px-3 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
                        Log & Deduct
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