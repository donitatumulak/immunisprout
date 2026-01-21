<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Normalize the user's role to lowercase
        $userRole = strtolower($user->role ?? optional($user->worker)->wrk_role ?? '');

        // Normalize the allowed roles array to lowercase
        $allowedRoles = array_map('strtolower', $roles);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Restricted Access.');
    }
}