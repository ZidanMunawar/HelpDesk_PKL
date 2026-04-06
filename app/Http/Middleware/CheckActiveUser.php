<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Superadmin selalu bisa akses
            if ($user->role === 'superadmin') {
                return $next($request);
            }

            // Cek status berdasarkan database
            if ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account is pending approval from admin.');
            }

            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact administrator.');
            }
        }

        return $next($request);
    }
}
