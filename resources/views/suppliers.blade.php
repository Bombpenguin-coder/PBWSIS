@extends('layouts.app')

@section('title', 'Suppliers')
@section('header_title', 'Supplier Management')

@section('content')
    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="flex items-center bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 mb-6 rounded shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Container -->
    <div class="bg-[#18191c] p-6 rounded-xl shadow-sm border border-zinc-800">
        <!-- Header & Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h2 class="text-lg font-bold text-white">Suppliers List</h2>
                <p class="text-xs text-zinc-400">Manage raw material vendors and contact details</p>
            </div>
            
            <button type="button" onclick="openAddSupplierModal()" class="bg-rose-700 hover:bg-rose-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-150 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Supplier
            </button>
        </div>

        <!-- Suppliers Table -->
        <div class="overflow-x-auto rounded-lg border border-zinc-800">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#202226] border-b border-zinc-800 text-zinc-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-3">Name</th>
                        <th class="p-3">Contact Person</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm bg-[#18191c]">
                   @forelse($suppliers as $supplier)
                        <tr class="hover:bg-zinc-800/40 transition duration-150">
                            <td class="p-3 font-semibold text-white">{{ $supplier->name }}</td>
                            <td class="p-3 text-zinc-400">{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td class="p-3 text-zinc-400">{{ $supplier->phone ?? 'N/A' }}</td>
                            <td class="p-3 text-zinc-400">{{ $supplier->email ?? 'N/A' }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ strtolower($supplier->status ?? 'active') == 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
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
                                        class="text-xs font-semibold text-sky-400 bg-sky-500/10 hover:bg-sky-500/20 px-2.5 py-1 rounded-md transition duration-150">
                                    Edit
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="inline">
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
                            <td colspan="6" class="p-6 text-center text-zinc-500">
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
    <div id="addSupplierModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col text-white">
            <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-white">Add New Supplier</h3>
                <button type="button" onclick="closeAddSupplierModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Supplier Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Bean Craft Co." 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="e.g., Jane Doe" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0917XXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        @error('phone') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" required class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g., vendor@beancraft.com" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeAddSupplierModal()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition">Cancel</button>
                    <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">+ Add Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT SUPPLIER MODAL ================= -->
    <div id="editSupplierModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl w-full max-w-sm p-5 relative flex flex-col text-white">
            <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4 shrink-0">
                <h3 class="text-base font-bold text-white">Edit Supplier</h3>
                <button type="button" onclick="closeEditSupplierModal()" class="text-zinc-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form id="editSupplierForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Supplier Name</label>
                    <input type="text" name="name" id="edit_supplier_name" required 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="edit_supplier_contact_person" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" name="phone" id="edit_supplier_phone" placeholder="0917XXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                               class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="edit_supplier_status" required 
                                class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" name="email" id="edit_supplier_email" 
                           class="w-full bg-[#202226] border border-zinc-700 text-white p-2 text-xs rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeEditSupplierModal()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold py-2 px-3 rounded-lg transition">Cancel</button>
                    <button type="submit" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition">Save Changes</button>
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