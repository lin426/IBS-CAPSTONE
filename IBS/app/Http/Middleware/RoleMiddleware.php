<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role ?? 'client';

        // If role doesn't match, redirect the user to the correct home
        if ($userRole !== $role) {
            return redirect()
                ->route($userRole === 'admin' ? 'dashboard' : 'client.home')
                ->with('warning', 'You do not have access to that page.');
        }

        return $next($request);
    }
}
