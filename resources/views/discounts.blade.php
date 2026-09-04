@extends('layouts.app')

@section('title', 'Discounts')
@section('header_title', 'Discount Rules')

@section('content')
    <!-- Flash Messages -->
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
                <h2 class="text-lg font-bold text-white">Active & Preset Discounts</h2>
                <p class="text-xs text-zinc-400">Manage promotional and custom price adjustments</p>
            </div>
            
            <button type="button" onclick="openAddDiscountModal()" class="bg-rose-700 hover:bg-rose-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Discount
            </button>
        </div>

        <!-- Discounts Table -->
        <div class="overflow-x-auto rounded-lg border border-zinc-800">
            <table class="min-w-full divide-y divide-zinc-800">
                <thead>
                    <tr class="bg-[#202226] text-zinc-400 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 text-left font-semibold">Discount Name</th>
                        <th class="py-3 px-4 text-left font-semibold">Type</th>
                        <th class="py-3 px-4 text-left font-semibold">Value</th>
                        <th class="py-3 px-4 text-center font-semibold">Status</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm bg-[#18191c]">
                    @forelse($discounts as $discount)
                        <tr class="hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-3 px-4 font-semibold text-white">{{ $discount->name }}</td>
                            <td class="py-3 px-4 text-zinc-400">{{ ucfirst($discount->type) }}</td>
                            <td class="py-3 px-4 font-bold text-white">
                                {{ $discount->type == 'percentage' ? number_format($discount->value, 2) . '%' : '₱' . number_format($discount->value, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $discount->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-800 text-zinc-400 border border-zinc-700' }}">
                                    {{ $discount->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                        data-id="{{ $discount->id }}"
                                        data-name="{{ $discount->name }}"
                                        data-type="{{ $discount->type }}"
                                        data-value="{{ $discount->value }}"
                                        data-active="{{ $discount->is_active ? '1' : '0' }}"
                                        onclick="openEditDiscountModal(this)"
                                        class="text-xs font-semibold text-sky-400 bg-sky-500/10 hover:bg-sky-500/20 px-2.5 py-1 rounded-md transition duration-150">
                                    Edit
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discount?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 px-2.5 py-1 rounded-md transition duration-150">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-zinc-500">
                                <p class="text-sm">No discounts set up yet.</p>
                                <p class="text-xs mt-1">Click the "Add Discount" button above to create one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= ADD DISCOUNT MODAL ================= -->
    <div id="addDiscountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col text-white">
            <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-white">Create Discount Rule</h3>
                <button type="button" onclick="closeAddDiscountModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_name">Discount Name</label>
                    <input type="text" name="name" id="modal_name" value="{{ old('name') }}" required placeholder="e.g., Senior / PWD, Summer Sale" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_type">Discount Type</label>
                        <select name="type" id="modal_type" required 
                                class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₱)</option>
                        </select>
                        @error('type') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="modal_value">Value</label>
                        <input type="number" step="0.01" name="value" id="modal_value" value="{{ old('value') }}" required placeholder="20.00" 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                        @error('value') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label class="flex items-center space-x-2 text-xs text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="rounded border-zinc-700 bg-[#202226] text-rose-600 focus:ring-rose-500">
                        <span>Set status as Active immediately</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeAddDiscountModal()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition">Cancel</button>
                    <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">+ Save Discount</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT DISCOUNT MODAL ================= -->
    <div id="editDiscountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col text-white">
            <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-white">Edit Discount Rule</h3>
                <button type="button" onclick="closeEditDiscountModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form id="editDiscountForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="edit_discount_name">Discount Name</label>
                    <input type="text" name="name" id="edit_discount_name" required 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="edit_discount_type">Discount Type</label>
                        <select name="type" id="edit_discount_type" required 
                                class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₱)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1" for="edit_discount_value">Value</label>
                        <input type="number" step="0.01" name="value" id="edit_discount_value" required 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="flex items-center space-x-2 text-xs text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_discount_is_active" value="1" class="rounded border-zinc-700 bg-[#202226] text-rose-600 focus:ring-rose-500">
                        <span>Set status as Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeEditDiscountModal()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition">Cancel</button>
                    <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openAddDiscountModal() {
            const modal = document.getElementById('addDiscountModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddDiscountModal() {
            const modal = document.getElementById('addDiscountModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEditDiscountModal(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const type = button.dataset.type;
            const value = button.dataset.value;
            const active = button.dataset.active;

            document.getElementById('editDiscountForm').action = '/discounts/' + id;
            document.getElementById('edit_discount_name').value = name;
            document.getElementById('edit_discount_type').value = type;
            document.getElementById('edit_discount_value').value = value;
            document.getElementById('edit_discount_is_active').checked = (active === '1');
            
            const modal = document.getElementById('editDiscountModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditDiscountModal() {
            const modal = document.getElementById('editDiscountModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        @if ($errors->any())
            openAddDiscountModal();
        @endif
    </script>
@endsection