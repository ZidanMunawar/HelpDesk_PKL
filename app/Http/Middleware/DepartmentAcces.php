<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentAcces
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please login.');
        }

        $user = Auth::user();
        $allowedRoles = ['superadmin', 'admin_eng', 'om', 'gm', 'manager'];

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized access to department management.');
        }

        return $next($request);
    }
}
