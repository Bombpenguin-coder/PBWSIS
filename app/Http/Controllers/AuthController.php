<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Handle the login form submission
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->onlyInput('username');
    }

    // Show the account registration view
    public function showRegister()
    {
        return view('register');
    }

    // Handle creating a new account
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => ['required', 'unique:users,username'],
            'password' => ['required', 'min:4'],
            'role'     => ['required'],
            'contact_number' => ['nullable'],
        ]);

        User::create([
            'username'       => $fields['username'],
            'password'       => Hash::make($fields['password']),
            'role'           => $fields['role'],
            'contact_number' => $fields['contact_number'] ?? null,
        ]);

        return redirect('/')->with('success', 'Account created! You can now log in.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}