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

        // ==== CHECK IF USER SOFT DELETED ====
        $softDeletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->first();

        if ($softDeletedUser) {
            // Log attempt to login with deactivated account
            ActivityLog::create([
                'user_id' => null,
                'ticket_id' => null,
                'action' => 'login_failed',
                'description' => 'Login attempt with deactivated account: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'email' => 'This account has been deactivated. Please contact administrator or register with a new account.',
            ])->onlyInput('email');
        }

        // ==== NORMAL LOGIN ATTEMPT ====
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            $request->session()->regenerate();

            // Log successful login
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => null,
                'action' => 'login',
                'description' => $user->name . ' (' . $user->role . ') logged in successfully',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Check email verification
            if (is_null($user->email_verified_at)) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Please verify your email address before continuing. Check your inbox or click resend.');
            }

            // Check user status
            if (!$user->isActive()) {
                Auth::logout();

                // Log failed login attempt due to inactive status
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'action' => 'login_failed',
                    'description' => $user->name . ' (' . $user->role . ') login failed - Account inactive/pending',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $message = $user->role === 'technician'
                    ? 'Your technician account is pending approval from admin. Please wait for approval email.'
                    : 'Your account is not active. Please contact administrator.';

                return redirect()->route('login')
                    ->with('error', $message);
            }

            // Login success
            return redirect()->intended('dashboard');
        }

        // Log failed login attempt
        ActivityLog::create([
            'user_id' => null,
            'ticket_id' => null,
            'action' => 'login_failed',
            'description' => 'Failed login attempt for email: ' . $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
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
        // Get active departments for technician dropdown
        $departments = Department::active()->orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // ==== CHECK EXISTING USER (TERMASUK SOFT DELETED) ====
        $existingUser = User::withTrashed() // ← Include soft deleted users
            ->where('email', $request->email)
            ->first();

        // Jika ada user dengan email yang sama
        if ($existingUser) {
            // Case 1: Soft deleted user → Force delete (permanent) untuk free up email
            if ($existingUser->trashed()) {
                \Log::info("Force deleting soft-deleted account: " . $existingUser->email);

                // Log activity
                ActivityLog::create([
                    'user_id' => null,
                    'ticket_id' => null,
                    'action' => 'user_deleted',
                    'description' => 'Force deleted soft-deleted account during registration: ' . $existingUser->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $existingUser->forceDelete(); // Permanent delete
            }
            // Case 2: Unverified active user → Delete untuk free up email
            elseif (is_null($existingUser->email_verified_at)) {
                \Log::info("Deleting unverified account: " . $existingUser->email);

                // Log activity
                ActivityLog::create([
                    'user_id' => null,
                    'ticket_id' => null,
                    'action' => 'user_deleted',
                    'description' => 'Deleted unverified account during registration: ' . $existingUser->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $existingUser->forceDelete(); // Permanent delete
            }
            // Case 3: Verified active user → Email conflict, return error
            else {
                // Log registration attempt with existing email
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

        // ==== VALIDATION ====
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Normal unique check
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'captcha' => 'required|captcha',
            'is_technician' => 'nullable|boolean',
            'department_id' => 'required_if:is_technician,1|nullable|exists:departments,id',
        ];

        $messages = [
            'captcha.captcha' => 'Invalid captcha code.',
            'department_id.required_if' => 'Please select a department if you are a technician.',
            'email.unique' => 'This email is already registered.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // ==== CREATE NEW USER ====
        $isTechnician = $request->has('is_technician') && $request->is_technician == 1;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $isTechnician ? 'technician' : 'user',
            'department_id' => $isTechnician ? $request->department_id : null,
            'status' => $isTechnician ? 'pending' : 'active',
            'email_verified_at' => null,
        ]);

        // Log successful registration
        ActivityLog::create([
            'user_id' => $user->id,
            'ticket_id' => null,
            'action' => 'user_registered',
            'description' => 'New user registered: ' . $user->name . ' (' . $user->role . ', Status: ' . $user->status . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Trigger email verification
        event(new Registered($user));

        \Log::info('New user registered: ' . $user->email . ' (Role: ' . $user->role . ', Status: ' . $user->status . ')');

        // Success message
        if ($isTechnician) {
            return redirect()->route('login')
                ->with('success', 'Registration successful! Please check your email and click the verification link. After verification, wait for admin approval.');
        } else {
            return redirect()->route('login')
                ->with('success', 'Registration successful! Please check your email and click the verification link to activate your account.');
        }
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
