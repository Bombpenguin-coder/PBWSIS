<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Required to encrypt passwords

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user_management', compact('users'));
    }

    public function store(Request $request)
    {
        // Automatically uppercase the username input before validation
        $request->merge([
            'username' => strtoupper(trim($request->username)),
        ]);

        // 1. Validate the incoming form data
        $request->validate([
            'username'       => 'required|string|max:255|unique:users,username',
            'password'       => 'required|string|min:4',
            'role'           => 'required|string|in:Owner,Cashier,Staff',
            'contact_number' => 'nullable|digits:11',
        ], [
            'contact_number.digits' => 'The contact number must be exactly 11 digits.',
        ]);

        // Check if the role is Owner and if one already exists
        if ($request->role === 'Owner') {
            $ownerExists = User::where('role', 'Owner')->exists();
            if ($ownerExists) {
                return back()->withErrors(['role' => 'An Owner account already exists. Only one Owner is permitted.'])->withInput();
            }
        }

        // 2. Save the new user to the database
        User::create([
            'username'       => $request->username,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->back()->with('success', 'New staff member added successfully!');
    }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Automatically uppercase the username input before validation
    $request->merge([
        'username' => strtoupper(trim($request->username)),
    ]);

    // 1. Validate inputs (Specifying users_id as the primary key column)
    $request->validate([
        'username'       => 'required|string|max:255|unique:users,username,' . $id . ',users_id',
        'role'           => 'required|string|in:Owner,Cashier,Staff',
        'contact_number' => 'nullable|digits:11',
        'password'       => 'nullable|string|min:4',
    ], [
        'contact_number.digits' => 'The contact number must be exactly 11 digits.',
    ]);

    // 2. Security Guard: Prevent demoting the last remaining Owner
    if ($user->role === 'Owner' && $request->role !== 'Owner') {
        $ownerCount = User::where('role', 'Owner')->count();
        if ($ownerCount <= 1) {
            return back()->withErrors(['role' => 'Security Constraint: You cannot demote the last active Owner account.'])->withInput();
        }
    }

    // 3. Security Guard: Prevent assigning a second Owner account
    if ($request->role === 'Owner' && $user->role !== 'Owner') {
        if (User::where('role', 'Owner')->exists()) {
            return back()->withErrors(['role' => 'An Owner account already exists. Only one Owner is permitted.'])->withInput();
        }
    }

    $updateData = [
        'username'       => $request->username,
        'role'           => $request->role,
        'contact_number' => $request->contact_number,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    return redirect()->back()->with('success', 'Staff account updated successfully!');
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 1. Security Guard: Prevent self-deletion
        if (auth()->id() == $id) {
            return redirect()->back()->withErrors(['error' => 'Action Prohibited: You cannot delete your currently active session account.']);
        }

        // 2. Security Guard: Prevent deleting the last remaining Owner
        if ($user->role === 'Owner') {
            $ownerCount = User::where('role', 'Owner')->count();
            if ($ownerCount <= 1) {
                return redirect()->back()->withErrors(['error' => 'Action Prohibited: Cannot delete the last remaining Owner account.']);
            }
        }

        $user->delete();

        return redirect()->back()->with('success', 'User access disabled successfully.');
    }
}