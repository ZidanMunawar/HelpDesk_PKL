<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Display forgot password form
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send reset link email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'captcha' => 'required|captcha',
        ]);

        $user = User::where('email', $request->email)->first();

        // Restrict to user role only
        if ($user && $user->role !== 'user') {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'password_reset_failed',
                'description' => 'Non-user role attempted reset: ' . $request->email . ' (Role: ' . $user->role . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => ['Password reset is only available for regular users.'],
            ]);
        }

        // Generate token
        $token = Str::random(64);

        // Delete old token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Send email menggunakan method dari model User
        try {
            $user->sendPasswordResetNotification($token);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'password_reset_requested',
                'description' => 'Reset link sent from login page to: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('status', 'We have emailed your password reset link!');

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => ['Failed to send reset link. Please try again later.'],
            ]);
        }
    }
}
