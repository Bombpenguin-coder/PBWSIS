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
        // 1. Validate the incoming form data
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4',
            'role' => 'required|string|in:Owner,Cashier,Kitchen Staff',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // 2. Save the new user to the database
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Securely hash the password!
            'role' => $request->role,
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->back()->with('success', 'New staff member added successfully!');
    }

    public function update(Request $request, $id)
    {
        // 1. Validate the changes. We tell the 'unique' rule to ignore the current user's ID so they don't trigger a duplicate error on their own username.
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',users_id',
            'role' => 'required|string|in:Owner,Cashier,Kitchen Staff',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // 2. Find and update the user
        $user = User::findOrFail($id);
        $user->update([
            'username' => $request->username,
            'role' => $request->role,
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->back()->with('success', 'Staff account updated successfully!');
    }

    public function destroy($id)
    {
        // 3. Find the user by their custom users_id and delete them
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User access disabled successfully.');
    }
}