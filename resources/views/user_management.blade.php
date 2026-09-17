@extends('layouts.app')

@section('title', 'User Management')
@section('header_title', 'Manage Staff Accounts')

@section('content')
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-white tracking-wide">Registered Users</h2>
        <button onclick="openUserModal('addUserModal')" class="bg-[#EA580C] hover:bg-orange-600 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition duration-200 text-sm flex items-center space-x-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Add New User</span>
        </button>
    </div>

    <!-- Users Table Container -->
    <div class="bg-[#18191c] rounded-xl shadow-xl border border-zinc-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-zinc-300">
                <thead class="bg-[#202226] text-zinc-400 font-semibold uppercase text-xs border-b border-zinc-800">
                    <tr>
                        <th class="py-3.5 px-5">Username</th>
                        <th class="py-3.5 px-5">Role</th>
                        <th class="py-3.5 px-5">Contact</th>
                        <th class="py-3.5 px-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-5 font-bold text-white">{{ $user->username }}</td>
                            <td class="py-4 px-5">
                                @if(strtolower($user->role) === 'owner')
                                    <span class="px-2.5 py-1 bg-orange-500/10 border border-orange-500/30 text-orange-400 text-xs font-bold rounded-full">Owner</span>
                                @else
                                    <span class="px-2.5 py-1 bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs font-medium rounded-full">
                                        {{ $user->role === 'Kitchen Staff' ? 'Staff' : $user->role }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-zinc-400">{{ $user->contact_number ?? 'N/A' }}</td>
                            <td class="py-4 px-5">
                                <div class="flex justify-center items-center space-x-2">
                                    <button type="button" 
                                            data-id="{{ $user->users_id }}"
                                            data-username="{{ $user->username }}"
                                            data-role="{{ $user->role }}"
                                            data-contact="{{ $user->contact_number }}"
                                            onclick="openUserEditModal(this)" 
                                            class="text-xs font-bold bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 py-1.5 px-3 rounded-lg transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->users_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to disable this account?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 py-1.5 px-3 rounded-lg transition">
                                            Disable
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-zinc-500">No staff accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl max-w-md w-full p-6 text-white relative">
            <div class="flex justify-between items-center mb-5 border-b border-zinc-800 pb-3">
                <h3 class="text-lg font-bold text-white">Add New Staff</h3>
                <button type="button" onclick="closeUserModal('addUserModal')" class="text-zinc-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Role</label>
                    <select name="role" required class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                        <option value="Cashier">Cashier</option>
                        <option value="Staff">Staff</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Contact Number</label>
                    <input type="text" 
                           name="contact_number" 
                           maxlength="11" 
                           pattern="[0-9]{11}" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           placeholder="09123456789" 
                           class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeUserModal('addUserModal')" class="px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 rounded-lg text-sm transition font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#EA580C] hover:bg-orange-600 text-white rounded-lg text-sm font-bold transition">Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all">
        <div class="bg-[#18191c] border border-zinc-800 rounded-xl shadow-2xl max-w-md w-full p-6 text-white relative">
            <div class="flex justify-between items-center mb-5 border-b border-zinc-800 pb-3">
                <h3 class="text-lg font-bold text-white">Edit Staff Details</h3>
                <button type="button" onclick="closeUserModal('editUserModal')" class="text-zinc-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form id="editUserForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Username</label>
                    <input type="text" id="edit_username" name="username" required class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Role</label>
                    <select id="edit_role" name="role" required class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                        <option value="Cashier">Cashier</option>
                        <option value="Staff">Staff</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-zinc-400 mb-1">Contact Number</label>
                    <input type="text" 
                           id="edit_contact" 
                           name="contact_number" 
                           maxlength="11" 
                           pattern="[0-9]{11}" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           placeholder="09123456789" 
                           class="w-full bg-[#202226] border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-[#EA580C]">
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-3 border-t border-zinc-800">
                    <button type="button" onclick="closeUserModal('editUserModal')" class="px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 rounded-lg text-sm transition font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold transition">Update Account</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openUserModal(modalId) {
            document.getElementById(modalId)?.classList.remove('hidden');
        }

        function closeUserModal(modalId) {
            document.getElementById(modalId)?.classList.add('hidden');
        }

        function openUserEditModal(buttonElement) {
            const id = buttonElement.getAttribute('data-id');
            const username = buttonElement.getAttribute('data-username');
            let role = buttonElement.getAttribute('data-role');
            const contact = buttonElement.getAttribute('data-contact');

            if (role === 'Kitchen Staff') {
                role = 'Staff';
            }

            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_contact').value = contact || '';
            
            document.getElementById('editUserForm').action = '/admin/users/' + id;
            
            openUserModal('editUserModal');
        }
    </script>
@endsection