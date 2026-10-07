<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow access if user role matches any allowed role
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect unauthorized users to their respective home pages
        if ($user->role === 'Cashier') {
            return redirect('/pos')->with('error', 'Access restricted to Owner.');
        }

        return redirect('/inventory/ingredients')->with('error', 'Access restricted to Owner.');
    }
}