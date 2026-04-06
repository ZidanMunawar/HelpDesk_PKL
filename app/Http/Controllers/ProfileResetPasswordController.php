<?php
// app/Http/Controllers/ProfileResetPasswordController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileResetPasswordController extends Controller
{
    /**
     * Send password reset link via email (dari profile)
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email address not found in our records.'
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate token
        $token = Str::random(64);

        // Delete old token if exists
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Send email
        try {
            $this->sendResetEmail($user, $token, 'profile');

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'profile_password_reset_requested',
                'description' => 'Password reset link sent from profile to: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link. Please try again later.'
            ], 500);
        }
    }

    /**
     * Send reset email
     */
    private function sendResetEmail($user, $token, $source = 'profile')
    {
        $resetLink = route('profile.password.reset', ['token' => $token, 'email' => $user->email]);

        Mail::send('emails.password-reset', [ // Gunakan template yang sama
            'user' => $user,
            'resetLink' => $resetLink,
            'expiry' => Carbon::now()->addHours(1),
            'source' => $source // Tandai source
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Reset Your Password - ' . config('app.name'));
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Show reset password form
     */
    public function showResetForm($token, Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Invalid password reset link.');
        }

        // Verify token
        $resetData = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$resetData) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired password reset link.');
        }

        // Check if token matches
        if (!Hash::check($token, $resetData->token)) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired password reset link.');
        }

        // Check expiry (1 hour)
        $createdAt = Carbon::parse($resetData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            // Delete expired token
            DB::table('password_resets')->where('email', $email)->delete();

            return redirect()->route('login')
                ->with('error', 'Password reset link has expired. Please request a new one.');
        }

        return view('auth.profile-reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify token
        $resetData = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset link.'
            ], 422);
        }

        // Check if token matches
        if (!Hash::check($request->token, $resetData->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ], 422);
        }

        // Check expiry (1 hour)
        $createdAt = Carbon::parse($resetData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            // Delete expired token
            DB::table('password_resets')->where('email', $request->email)->delete();

            return response()->json([
                'success' => false,
                'message' => 'Reset link has expired. Please request a new one.'
            ], 422);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_password_reset_completed',
            'description' => 'Password reset completed via profile for: ' . $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully! You can now login with your new password.'
        ]);
    }

    /**
     * Check if reset link is valid (AJAX)
     */
    public function checkToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);

        $resetData = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetData) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid reset link'
            ]);
        }

        if (!Hash::check($request->token, $resetData->token)) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token'
            ]);
        }

        $createdAt = Carbon::parse($resetData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            // Delete expired token
            DB::table('password_resets')->where('email', $request->email)->delete();

            return response()->json([
                'valid' => false,
                'message' => 'Token expired'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Token is valid'
        ]);
    }
}
