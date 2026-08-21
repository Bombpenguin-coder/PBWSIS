@extends('layouts.app')

@section('title', 'User Management')
@section('header_title', 'Manage Staff Accounts')

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Registered Users</h2>
        <button onclick="openModal('addUserModal')" class="bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded shadow transition duration-200">
            + Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md border-t-4 border-red-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-black text-white">
                    <tr>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase">Username</th>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase">Role</th>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase">Contact</th>
                        <th class="py-3 px-4 border-b text-center text-sm uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-3 px-4 font-bold text-gray-800">{{ $user->username }}</td>
                            <td class="py-3 px-4">
                                @if(strtolower($user->role) === 'owner')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Owner</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded-full">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $user->contact_number ?? 'N/A' }}</td>
                            <td class="py-3 px-4 flex justify-center space-x-2">
                                <!-- Edit Button triggers the JS function -->
                                <!-- Safely store user data inside HTML attributes -->
                                    <button type="button" 
                                            data-id="{{ $user->users_id }}"
                                            data-username="{{ $user->username }}"
                                            data-role="{{ $user->role }}"
                                            data-contact="{{ $user->contact_number }}"
                                            onclick="openUserEditModal(this)" 
                                            class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 py-1 px-3 rounded transition">
                                        Edit
                                    </button>
                                <form action="{{ route('users.destroy', $user->users_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to disable this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm bg-red-100 hover:bg-red-200 text-red-800 py-1 px-3 rounded transition">
                                        Disable
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">No staff accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal (Light Transparent Backdrop) -->
    <div id="addUserModal" class="hidden fixed inset-0 bg-gray-900/10 backdrop-blur-sm z-50 flex items-center justify-center transition-all">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border-t-4 border-red-900 relative">
            <button onclick="closeModal('addUserModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="text-lg font-bold mb-4 text-black">Add New Staff</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-red-900">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-red-900">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Role</label>
                    <select name="role" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-red-900">
                        <option value="Cashier">Cashier</option>
                        <option value="Kitchen Staff">Kitchen Staff</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2">Contact Number</label>
                    <input type="text" name="contact_number" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-red-900">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeUserModal('addUserModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-200">Cancel</button>
                    <button type="submit" class="bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-200">Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal (Light Transparent Backdrop) -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-900/10 backdrop-blur-sm z-50 flex items-center justify-center transition-all">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border-t-4 border-blue-900 relative">
            <button onclick="closeModal('editUserModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="text-lg font-bold mb-4 text-black">Edit Staff Details</h2>
            
            <!-- Form Action URL is updated dynamically via JS -->
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Username</label>
                    <input type="text" id="edit_username" name="username" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-900">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Role</label>
                    <select id="edit_role" name="role" required class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-900">
                        <option value="Cashier">Cashier</option>
                        <option value="Kitchen Staff">Kitchen Staff</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2">Contact Number</label>
                    <input type="text" id="edit_contact" name="contact_number" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-900">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeUserModal('editUserModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-200">Cancel</button>
                    <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded transition duration-200">Update Account</button>
                </div>
            </form>
        </div>
    </div>

<!-- Dedicated Scripts for User Management -->
    <script>
        // Use unique names to avoid conflicting with app.blade.php
        function openUserModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeUserModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Renamed to openUserEditModal
        function openUserEditModal(buttonElement) {
            const id = buttonElement.getAttribute('data-id');
            const username = buttonElement.getAttribute('data-username');
            const role = buttonElement.getAttribute('data-role');
            const contact = buttonElement.getAttribute('data-contact');

            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_contact').value = contact || '';
            
            document.getElementById('editUserForm').action = '/admin/users/' + id;
            
            // Call our unique open function
            openUserModal('editUserModal');
        }
    </script>
@endsection