<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ], [
            'captcha.captcha' => 'Invalid captcha code.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Check soft deleted
        $softDeletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->first();

        if ($softDeletedUser) {
            ActivityLog::create([
                'user_id' => null,
                'ticket_id' => null,
                'action' => 'login_failed',
                'description' => 'Login attempt with deactivated account: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return back()->withErrors([
                'email' => 'This account has been deactivated. Please contact administrator or register with a new account.',
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            $request->session()->regenerate();

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => null,
                'action' => 'login',
                'description' => $user->name . ' (' . $user->role . ') logged in successfully',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            // Email verification check
            if (is_null($user->email_verified_at)) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Please verify your email address before continuing. Check your inbox or click resend.');
            }

            // Check user status based on database
            if ($user->status === 'pending') {
                Auth::logout();

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'action' => 'login_failed',
                    'description' => $user->name . ' (' . $user->role . ') login failed - Account pending admin approval',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);

                return redirect()->route('login')
                    ->with('error', 'Your account is pending approval from admin. Please wait for approval email.');
            }

            if ($user->status === 'inactive') {
                Auth::logout();

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'action' => 'login_failed',
                    'description' => $user->name . ' (' . $user->role . ') login failed - Account inactive',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);

                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact administrator.');
            }

            // Login success
            return redirect()->intended('dashboard');
        }

        ActivityLog::create([
            'user_id' => null,
            'ticket_id' => null,
            'action' => 'login_failed',
            'description' => 'Failed login attempt for email: ' . $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        // Hapus departemen dari form registrasi
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // CHECK EXISTING USER (including soft deleted)
        $existingUser = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if ($existingUser) {
            // Force delete soft deleted or unverified users
            if ($existingUser->trashed() || is_null($existingUser->email_verified_at)) {
                ActivityLog::create([
                    'user_id' => null,
                    'ticket_id' => null,
                    'action' => 'user_deleted',
                    'description' => 'Deleted account during registration: ' . $existingUser->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $existingUser->forceDelete();
            } else {
                ActivityLog::create([
                    'user_id' => null,
                    'ticket_id' => null,
                    'action' => 'user_registration_failed',
                    'description' => 'Registration failed - email already exists (active): ' . $request->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return redirect()->back()
                    ->withErrors(['email' => 'This email is already registered and active.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }
        }

        // VALIDATION
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'captcha' => 'required|captcha',
        ];

        $messages = [
            'captcha.captcha' => 'Invalid captcha code.',
            'email.unique' => 'This email is already registered.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // CREATE NEW USER - Default role 'user', status 'pending'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user', // SEMUA YANG REGISTER DEFAULT ROLE 'user'
            'department_id' => null,
            'status' => 'pending', // SEMUA USER BARU STATUS 'pending' (menunggu approval admin)
            'email_verified_at' => null,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'ticket_id' => null,
            'action' => 'user_registered',
            'description' => 'New user registered: ' . $user->name . ' (Role: ' . $user->role . ', Status: ' . $user->status . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Trigger email verification
        event(new Registered($user));

        \Log::info('New user registered: ' . $user->email . ' (Role: ' . $user->role . ', Status: ' . $user->status . ')');

        // Success message - semua user menunggu approval admin
        return redirect()->route('login')
            ->with('success', 'Registration successful! Please check your email and click the verification link. After verification, wait for admin approval to activate your account.');
    }
    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout activity
        ActivityLog::create([
            'user_id' => $user->id,
            'ticket_id' => null,
            'action' => 'logout',
            'description' => $user->name . ' (' . $user->role . ') logged out',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Reset unverified user account
     */
    public function resetUnverified(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Find unverified user
        $user = User::where('email', $request->email)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return back()->with('error', 'No unverified account found with this email.');
        }

        // Log reset activity
        ActivityLog::create([
            'user_id' => null,
            'ticket_id' => null,
            'action' => 'user_reset',
            'description' => 'Manual reset unverified account: ' . $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Force delete unverified account
        $email = $user->email;
        $user->forceDelete();

        \Log::info("Manual reset unverified account: " . $email);

        return redirect()->route('register')
            ->with('success', 'Previous unverified account has been removed. You can now register with ' . $email)
            ->withInput(['email' => $email]); // Pre-fill email di form register
    }
}
