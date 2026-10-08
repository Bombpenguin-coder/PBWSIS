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
            return redirect()->route('setup.register');
        }

        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('login'); 
    }

    // 2. First-Time Setup Registration View
    public function showRegister()
    {
        // If an Owner already exists, lock setup and send to login
        if (User::where('role', 'Owner')->exists()) {
            return redirect()->route('login');
        }

        // Return your setup view file (e.g., resources/views/register.blade.php or setup.blade.php)
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
]       );

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
     * Helper Method: Redirect user based on role
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'Owner') {
            return redirect()->route('dashboard')->with('success', 'Welcome back, Owner!');
        } 
        
        if ($user->role === 'Cashier') {
            return redirect('/pos')->with('success', 'POS Terminal ready.');
        } 
        
        // Staff / Kitchen
        return redirect('/ingredients')->with('success', 'Kitchen inventory access granted.');
    }
}