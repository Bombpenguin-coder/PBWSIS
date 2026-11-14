<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user_management', compact('users'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'username' => strtoupper(trim($request->username)),
        ]);

        $request->validate([
            'username'       => 'required|string|max:255|unique:users,username',
            'password'       => 'required|string|min:4',
            'role'           => 'required|string|in:Owner,Cashier,Staff',
            'permissions'    => $request->role === 'Owner' ? 'nullable|array' : 'required|array|min:1',
            'contact_number' => 'nullable|digits:11',
        ], [
            'permissions.required' => 'You must check at least one access permission.',
            'permissions.min'      => 'You must check at least one access permission.',
        ]);

        if ($request->role === 'Owner') {
            if (User::where('role', 'Owner')->exists()) {
                return back()->withErrors(['role' => 'An Owner account already exists. Only one Owner is permitted.'])->withInput();
            }
        }

        User::create([
            'username'       => $request->username,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'permissions'    => $request->role === 'Owner' ? [] : $request->input('permissions', []),
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->back()->with('success', 'New staff member and permissions added successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->merge([
            'username' => strtoupper(trim($request->username)),
        ]);

        // If the user being updated is an Owner, permissions can be empty, otherwise require at least 1
        $isOwner = strtolower($request->role) === 'owner';

        $request->validate([
            'username'       => 'required|string|max:255|unique:users,username,' . $id . ',users_id',
            'role'           => 'required|string|in:Owner,Cashier,Staff',
            'permissions'    => $isOwner ? 'nullable|array' : 'required|array|min:1',
            'contact_number' => 'nullable|digits:11',
            'password'       => 'nullable|string|min:4',
        ], [
            'permissions.required' => 'You must check at least one access permission.',
            'permissions.min'      => 'You must check at least one access permission.',
        ]);

        if ($user->role === 'Owner' && $request->role !== 'Owner') {
            if (User::where('role', 'Owner')->count() <= 1) {
                return back()->withErrors(['role' => 'Security Constraint: You cannot demote the last active Owner account.'])->withInput();
            }
        }

        if ($request->role === 'Owner' && $user->role !== 'Owner') {
            if (User::where('role', 'Owner')->exists()) {
                return back()->withErrors(['role' => 'An Owner account already exists. Only one Owner is permitted.'])->withInput();
            }
        }

        $updateData = [
            'username'       => $request->username,
            'role'           => $request->role,
            'permissions'    => $isOwner ? [] : $request->input('permissions', []),
            'contact_number' => $request->contact_number,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Staff account and permissions updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $id) {
            return redirect()->back()->withErrors(['error' => 'Action Prohibited: You cannot delete your currently active session account.']);
        }

        if ($user->role === 'Owner' && User::where('role', 'Owner')->count() <= 1) {
            return redirect()->back()->withErrors(['error' => 'Action Prohibited: Cannot delete the last remaining Owner account.']);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User access disabled successfully.');
    }
}