@extends('layouts.app')

@section('title', 'Suppliers')
@section('header_title', 'Supplier Management')

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
                <h2 class="text-lg font-bold text-gray-800">Suppliers List</h2>
                <p class="text-xs text-gray-500">Manage raw material vendors and contact details</p>
            </div>
            
            <button type="button" onclick="openAddModal()" class="bg-red-900 hover:bg-red-800 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Supplier
            </button>
        </div>

        <!-- Suppliers Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-900 text-gray-100 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-3">Name</th>
                        <th class="p-3">Contact Person</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm bg-white">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50/80 transition duration-150">
                            <td class="p-3 font-semibold text-gray-800">{{ $supplier->name }}</td>
                            <td class="p-3 text-gray-600">{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td class="p-3 text-gray-600">{{ $supplier->phone ?? 'N/A' }}</td>
                            <td class="p-3 text-gray-600">{{ $supplier->email ?? 'N/A' }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $supplier->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($supplier->status ?? 'active') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">
                                <p class="text-sm">No suppliers found.</p>
                                <p class="text-xs mt-1">Click the "Add Supplier" button above to add your first vendor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= ADD SUPPLIER MODAL ================= -->
    <div id="addSupplierModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative transform transition-all flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-gray-800">
                    Add New Supplier
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('suppliers.store') }}" method="POST" class="overflow-y-auto pr-1">
                @csrf
                
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_supplier_name">Supplier Name</label>
                    <input type="text" name="name" id="modal_supplier_name" value="{{ old('name') }}" required placeholder="e.g., Bean Craft Co." 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_contact_person">Contact Person</label>
                    <input type="text" name="contact_person" id="modal_contact_person" value="{{ old('contact_person') }}" placeholder="e.g., Jane Doe" 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('contact_person')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_phone">Phone</label>
                        <input type="text" name="phone" id="modal_phone" value="{{ old('phone') }}" placeholder="e.g., 0917XXXXXXX" 
                               class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_status">Status</label>
                        <select name="status" id="modal_status" required 
                                class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1" for="modal_email">Email Address</label>
                    <input type="email" name="email" id="modal_email" value="{{ old('email') }}" placeholder="e.g., vendor@beancraft.com" 
                           class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-2 px-3 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">
                        + Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Modal Scripts -->
    <script>
        function openAddModal() {
            document.getElementById('addSupplierModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addSupplierModal').classList.add('hidden');
        }

        // Keep modal open if validation errors exist on submit
        @if ($errors->any())
            openAddModal();
        @endif
    </script>
@endsection