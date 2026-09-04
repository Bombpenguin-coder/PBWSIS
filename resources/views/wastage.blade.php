@extends('layouts.app')

@section('title', 'Wastage Logs')
@section('header_title', 'Wastage & Spoilage Tracking')

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
                <h2 class="text-lg font-bold text-white">Wastage History</h2>
                <p class="text-xs text-zinc-400">Track raw material loss, shrinkage, and spoilage logs</p>
            </div>
            
            <button type="button" onclick="openAddModal()" class="bg-rose-700 hover:bg-rose-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Record Spoilage
            </button>
        </div>

        <!-- Wastage History Table -->
        <div class="overflow-x-auto rounded-lg border border-zinc-800">
            <table class="min-w-full divide-y divide-zinc-800">
                <thead class="bg-[#202226] text-zinc-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Date</th>
                        <th class="py-3 px-4 text-left font-semibold">Ingredient</th>
                        <th class="py-3 px-4 text-left font-semibold">Qty Lost</th>
                        <th class="py-3 px-4 text-left font-semibold">Reason</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm bg-[#18191c]">
                    @forelse($wastages as $log)
                        <tr class="hover:bg-[#202226]/60 transition duration-150">
                            <td class="py-3 px-4 text-zinc-400 font-mono text-xs">
                                {{ \Carbon\Carbon::parse($log->wastage_date ?? $log->created_at)->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-white">
                                {{ $log->ingredient->ingredient_name ?? 'Unknown Ingredient' }}
                            </td>
                            <td class="py-3 px-4 text-rose-400 font-bold">
                                -{{ $log->quantity_wasted }} <span class="text-xs font-normal text-zinc-400">{{ $log->ingredient->unit ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-zinc-300 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700 font-medium">
                                    {{ $log->reason }}
                                </span>
                            </td>

                            <!-- Actions Column -->
                            <td class="py-3 px-4 text-center space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                    onclick="openEditModal('/wastage/{{ $log->wastage_id ?? $log->id }}', 'Edit Wastage Record', [
                                        { label: 'Quantity Wasted', name: 'quantity_wasted', type: 'number', value: '{{ $log->quantity_wasted }}', required: true },
                                        { label: 'Reason', name: 'reason', value: '{{ addslashes($log->reason) }}', required: true },
                                        { label: 'Remarks', name: 'remarks', type: 'textarea', value: '{{ addslashes($log->remarks ?? '') }}' }
                                    ])" 
                                    class="text-xs font-semibold text-sky-400 bg-sky-500/10 hover:bg-sky-500/20 px-3 py-1 rounded-md transition duration-150">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('wastage.destroy', $log->wastage_id ?? $log->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this wastage record?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 px-3 py-1 rounded-md transition duration-150">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-4 text-center text-zinc-500">
                                No wastage logs recorded yet.
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
                    Record Spoilage
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('wastage.store') }}" method="POST" class="overflow-y-auto pr-1 space-y-3">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_ingredient_id">Ingredient</label>
                    <select name="ingredient_id" id="modal_ingredient_id" required class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                        <option value="" disabled selected class="text-zinc-500">Select an ingredient...</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->ingredient_id }}" {{ old('ingredient_id') == $ingredient->ingredient_id ? 'selected' : '' }}>
                                {{ $ingredient->ingredient_name }} (Current: {{ $ingredient->quantity }} {{ $ingredient->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('ingredient_id')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_quantity_wasted">Quantity Wasted</label>
                    <input type="number" step="0.01" name="quantity_wasted" id="modal_quantity_wasted" value="{{ old('quantity_wasted') }}" required placeholder="0.00" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('quantity_wasted')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_wastage_date">Date Wasted</label>
                    <input type="date" name="wastage_date" id="modal_wastage_date" value="{{ old('wastage_date', date('Y-m-d')) }}" required 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('wastage_date')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_reason">Reason</label>
                    <input type="text" name="reason" id="modal_reason" value="{{ old('reason') }}" required placeholder="e.g., Expired, Spilled, Damaged" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">
                    @error('reason')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_remarks">Remarks (Optional)</label>
                    <textarea name="remarks" id="modal_remarks" rows="2" placeholder="Additional details..." 
                              class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8c00]">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800 mt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-[#202226] hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition border border-zinc-700">
                        Cancel
                    </button>
                    <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
                        + Save Spoilage
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

        @if ($errors->any())
            openAddModal();
        @endif
    </script>
@endsection