@extends('layouts.app')

@section('title', 'User Management')
@section('header_title', 'Manage Staff Accounts')

@section('content')
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Registered Users</h2>
        <button class="bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-4 rounded shadow transition duration-200">
            + Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md border-t-4 border-red-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-black text-white">
                    <tr>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase tracking-wider">Username</th>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase tracking-wider">Role</th>
                        <th class="py-3 px-4 border-b text-left text-sm uppercase tracking-wider">Contact Number</th>
                        <th class="py-3 px-4 border-b text-center text-sm uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-3 px-4 text-gray-600">{{ $user->users_id }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">{{ $user->username }}</td>
                            <td class="py-3 px-4">
                                @if(strtolower($user->role) === 'owner' || strtolower($user->role) === 'admin')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Owner</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded-full">Cashier</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $user->contact_number ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <!-- Placeholder action buttons for permissions and disabling -->
                                <button class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 py-1 px-3 rounded transition">Edit Role</button>
                                <button class="text-sm bg-red-100 hover:bg-red-200 text-red-800 py-1 px-3 rounded transition">Disable</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">No staff accounts found in the database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection