<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {
        // Check if the user is logged in and if their role matches the requirement
        if (!$request->user() || strtolower($request->user()->role) !== strtolower($requiredRole)) {
            // If they are unauthorized, redirect them to the POS with an error message
            return redirect()->route('dashboard')->withErrors('You do not have permission to access that page.');
        }

        // If they pass the check, allow the request to proceed
        return $next($request);
    }
}