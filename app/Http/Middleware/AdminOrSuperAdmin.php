<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        return $next($request);
    }
}
