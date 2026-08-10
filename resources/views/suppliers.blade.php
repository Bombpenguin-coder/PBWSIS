@extends('layouts.app')

@section('title', 'Suppliers')
@section('header_title', 'Supplier Management')

@section('content')
    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="flex items-center bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Container -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <!-- Header & Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Suppliers List</h2>
                <p class="text-xs text-gray-500">Manage raw material vendors and contact details</p>
            </div>
            
            <button type="button" onclick="openAddSupplierModal()" class="bg-red-900 hover:bg-red-800 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
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
                        <th class="p-3 text-center">Actions</th>
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
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ strtolower($supplier->status ?? 'active') == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($supplier->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="p-3 text-center space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                        data-id="{{ $supplier->id }}"
                                        data-name="{{ $supplier->name }}"
                                        data-contact_person="{{ $supplier->contact_person }}"
                                        data-phone="{{ $supplier->phone }}"
                                        data-email="{{ $supplier->email }}"
                                        data-status="{{ strtolower($supplier->status ?? 'active') }}"
                                        onclick="openEditSupplierModal(this)"
                                        class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 px-2.5 py-1 rounded-md transition duration-150">
                                    Edit
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-800 px-2.5 py-1 rounded-md transition duration-150">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">
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
    <div id="addSupplierModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Add New Supplier</h3>
                <button type="button" onclick="closeAddSupplierModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Supplier Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Bean Craft Co." class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="e.g., Jane Doe" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0917XXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g., vendor@beancraft.com" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeAddSupplierModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-2 px-3 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold py-2 px-3 rounded-lg">+ Add Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT SUPPLIER MODAL ================= -->
    <div id="editSupplierModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Edit Supplier</h3>
                <button type="button" onclick="closeEditSupplierModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            <form id="editSupplierForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Supplier Name</label>
                    <input type="text" name="name" id="edit_supplier_name" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="edit_supplier_contact_person" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phone</label>
                        <input type="text" name="phone" id="edit_supplier_phone" placeholder="0917XXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" id="edit_supplier_status" required class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" id="edit_supplier_email" class="w-full border border-gray-300 p-2 text-xs rounded-lg focus:ring-2 focus:ring-red-900 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeEditSupplierModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-2 px-3 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white text-xs font-bold py-2 px-3 rounded-lg">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openAddSupplierModal() {
            const modal = document.getElementById('addSupplierModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddSupplierModal() {
            const modal = document.getElementById('addSupplierModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEditSupplierModal(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const contactPerson = button.dataset.contact_person;
            const phone = button.dataset.phone;
            const email = button.dataset.email;
            const status = button.dataset.status;

            document.getElementById('editSupplierForm').action = '/suppliers/' + id;
            document.getElementById('edit_supplier_name').value = name || '';
            document.getElementById('edit_supplier_contact_person').value = contactPerson || '';
            document.getElementById('edit_supplier_phone').value = phone || '';
            document.getElementById('edit_supplier_email').value = email || '';
            document.getElementById('edit_supplier_status').value = status || 'active';

            const modal = document.getElementById('editSupplierModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditSupplierModal() {
            const modal = document.getElementById('editSupplierModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        @if ($errors->any())
            openAddSupplierModal();
        @endif
    </script>
@endsection