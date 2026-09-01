<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showLogin()
    {
        // DEBUG: Stop the app and show me exactly how many users are in the table
        $userCount = User::count();
        $ownerCount = User::where('role', 'Owner')->count();
        
        // Uncomment the line below to run the diagnostic test
        dd('Total Users: ' . $userCount, 'Total Owners: ' . $ownerCount);

        // If no owner exists in the entire database, force them to setup the system
        if (User::where('role', 'Owner')->doesntExist()) {
            return redirect()->route('setup.register');
        }

        return view('Login'); 
    }

    // 2. Show the registration page (only if no owner exists)
    public function showRegister()
    {
        // Security Check: If an owner already exists, lock this page down!
        if (User::where('role', 'Owner')->exists()) {
            return redirect()->route('login')->withErrors('System already initialized.');
        }

        return view('register');
    }

    // 3. Save the new Owner account
    public function storeOwner(Request $request)
    {
        // Double security check to prevent advanced POST attacks
        if (User::where('role', 'Owner')->exists()) {
            return redirect()->route('login');
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // Create the user and FORCE the role to 'Owner'
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'Owner', 
            'contact_number' => $request->contact_number,
        ]);

        return redirect()->route('login')->with('success', 'Master Owner account created! Please log in.');
    }
    // Process the actual login attempt
    public function login(Request $request)
    {
        // 1. Validate the form inputs
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Attempt to log the user in using Laravel's built-in Auth facade
        if (Auth::attempt($credentials)) {
            // 3. Security best practice: regenerate the session ID to prevent fixation attacks
            $request->session()->regenerate();
            
            // 4. Redirect them to the main POS dashboard
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        // 5. If the password was wrong, send them back to the form with an error
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    // Securely log the user out
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}