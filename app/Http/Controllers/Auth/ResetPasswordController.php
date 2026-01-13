<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /**
     * Display reset password form
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset user password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Log password reset
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'action' => 'password_reset',
                    'description' => 'Password reset successfully for: ' . $user->name . ' (' . $user->email . ')',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Your password has been reset successfully! You can now login with your new password.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
