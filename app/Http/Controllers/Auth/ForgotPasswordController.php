<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

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
        ], [
            'email.exists' => 'We could not find a user with that email address.',
            'captcha.captcha' => 'Invalid captcha code.',
        ]);

        // Check if user role is 'user' (restrict to user role only)
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->role !== 'user') {
            // Log attempt
            ActivityLog::create([
                'user_id' => null,
                'ticket_id' => null,
                'action' => 'password_reset_failed',
                'description' => 'Password reset attempt for non-user role: ' . $request->email . ' (Role: ' . $user->role . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => ['Password reset is only available for regular users. Admin/Staff please contact administrator.'],
            ]);
        }

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Log successful send
            ActivityLog::create([
                'user_id' => $user->id ?? null,
                'ticket_id' => null,
                'action' => 'password_reset_requested',
                'description' => 'Password reset link sent to: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('status', 'We have emailed your password reset link! Please check your inbox.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
