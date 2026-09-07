<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Fetch the authenticated user's role
        $userRole = Auth::user()->role;

        // 3. Allow request to proceed if user has an authorized role
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Redirect unauthorized users to dashboard with an error notice
        return redirect('/dashboard')->with('error', "You don't have permission to access this section.");
    }
}