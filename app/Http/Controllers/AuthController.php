<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // 1. Display Login Screen
    public function showLogin()
    {
        // Redirect to initial setup if no Owner exists
        if (!User::where('role', 'Owner')->exists()) {
            return redirect('/setup'); // Directly use the URL path to avoid route name mismatch loops
        }

        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('login'); 
    }

    // 2. Registration View (First-time setup view)
    public function showRegister()
    {
        // If an Owner already exists, block public access and send them to login
        if (User::where('role', 'Owner')->exists()) {
            return redirect()->route('login');
        }

        // Point to register.blade.php
        return view('register'); 
    }

    // 3. Register First Owner Account (First-time setup only)
    public function storeOwner(Request $request)
    {
        // Lock endpoint if an Owner already exists
        if (User::where('role', 'Owner')->exists()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'username'       => 'required|string|alpha_dash|max:50|unique:users,username',
            'password'       => 'required|string|min:8|max:64',
            'contact_number' => 'nullable|numeric|digits:11',
        ]);

        User::create([
            'username'       => strtoupper(trim($validated['username'])),
            'password'       => Hash::make($validated['password']),
            'role'           => 'Owner', 
            'contact_number' => $validated['contact_number'] ? trim($validated['contact_number']) : null,
        ]);

        return redirect()->route('login')->with('success', 'Master Owner account created! Please log in.');
    }

    // 4. Authenticate User Login
    public function login(Request $request)
    {
        // Auto-uppercase input username before authentication attempt
        $request->merge([
            'username' => strtoupper(trim($request->username)),
        ]);

        $credentials = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($credentials['username']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            
            // Regenerate session AFTER obtaining user info
            $user = Auth::user();
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
    
    // 5. Logout User
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Force a clean GET redirect back to the login page
        return redirect()->to('/login');
    }

    /**
     * Helper Method: Dynamically redirect user based on Role and ARBAC Permissions
     */
    protected function redirectBasedOnRole($user)
    {
        // 1. Owner always goes to dashboard/admin area
        if (strtolower($user->role) === 'owner') {
            return redirect()->route('dashboard')->with('success', 'Welcome back, Owner!');
        } 
        
        // 2. Fetch their saved permissions array
        $permissions = $user->permissions ?? [];

        // 3. Map permission keys to your actual Laravel Route names from web.php
        $moduleRouteMap = [
            'pos'           => 'pos',
            'inventory'     => 'inventory',
            'wastage'       => 'wastage.index',
            'purchases'     => 'purchases.index',
            'sales_history' => 'sales.history',
            'reports'       => 'reports.index',
        ];

        foreach ($permissions as $perm) {
            if (isset($moduleRouteMap[$perm]) && \Illuminate\Support\Facades\Route::has($moduleRouteMap[$perm])) {
                return redirect()->route($moduleRouteMap[$perm])->with('success', 'Access granted.');
            }
        }

        // 4. Fallback URL paths matching your exact web.php URI structure
        $modulePathMap = [
            'pos'           => '/pos',
            'inventory'     => '/inventory',
            'wastage'       => '/wastage',
            'purchases'     => '/purchases',
            'sales_history' => '/sales/history', // Corrected to match your prefix /sales/history
            'reports'       => '/reports',
        ];

        foreach ($permissions as $perm) {
            if (isset($modulePathMap[$perm])) {
                return redirect($modulePathMap[$perm])->with('success', 'Access granted.');
            }
        }

        // 5. Safeguard if zero permissions match
        Auth::logout();
        return redirect('/login')->withErrors(['username' => 'This account has no module access permissions assigned.']);
    }
}