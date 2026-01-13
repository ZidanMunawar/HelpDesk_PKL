<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\TicketDetailController;
use App\Http\Controllers\TicketFilterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PriorityController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Captcha reload
Route::get('/reload-captcha', function () {
    return response()->json(['captcha' => captcha_img()]);
})->name('reload.captcha');

// ================================
// AUTHENTICATION ROUTES (Guest Only)
// ================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Reset unverified account
    Route::post('/auth/reset-unverified', [AuthController::class, 'resetUnverified'])->name('auth.reset-unverified');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// PASSWORD RESET ROUTES
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
// ================================
// EMAIL VERIFICATION ROUTES
// ================================

// Email verification notice page (butuh login)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        // Redirect jika sudah verified
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    // Resend verification email (butuh login)
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link has been sent to your email!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// ================================
// VERIFICATION HANDLER (PUBLIC - NO AUTH REQUIRED!)
// ================================
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Find user by ID
    $user = \App\Models\User::findOrFail($id);

    // Verify hash
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    // Check if already verified
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('info', 'Email already verified. Please login.');
    }

    // === VERIFY EMAIL ===
    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    \Log::info('Email verified for user: ' . $user->email);

    // === AUTO LOGIN USER ===
    Auth::login($user);

    // Check user status before redirect
    if (!$user->isActive()) {
        Auth::logout();

        $message = $user->role === 'technician'
            ? 'Email verified! Your technician account is pending approval from admin. You will be notified once approved.'
            : 'Email verified! Your account is not active yet. Please contact administrator.';

        return redirect()->route('login')->with('success', $message);
    }

    // === REDIRECT TO DASHBOARD ===
    return redirect()->route('dashboard')->with('success', 'Email verified successfully! Welcome to Harris Hotel Ticketing System.');

})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');


// ================================
// AUTHENTICATED USER ROUTES
// ================================
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/upload-picture', [ProfileController::class, 'uploadProfilePicture'])->name('upload-picture');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/remove-picture', [ProfileController::class, 'removeProfilePicture'])->name('remove-picture');
    });

    // ================================
    // TICKET ROUTES (All Authenticated Users)
    // ================================
    Route::prefix('tickets')->name('tickets.')->group(function () {

        // List Pages
        Route::get('/', [TicketController::class, 'index'])->name('index'); // All Tickets
        Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('my-tickets'); // My Tickets
        Route::get('/assigned/me', [TicketFilterController::class, 'assignedToMe'])->name('assigned'); // Assigned to Me

        // Create & Store
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');

        // Unassigned (Admin Only)
        Route::middleware('admin')->group(function () {
            Route::get('/unassigned', [TicketFilterController::class, 'unassigned'])->name('unassigned');
            Route::post('/bulk-assign', [TicketFilterController::class, 'bulkAssign'])->name('bulk-assign');
        });

        // Detail & Actions (Must be LAST to avoid conflict)
        Route::get('/{ticket}', [TicketDetailController::class, 'show'])->name('show');
        Route::post('/{ticket}/comment', [TicketDetailController::class, 'addComment'])->name('add-comment');
        Route::post('/{ticket}/status', [TicketDetailController::class, 'updateStatus'])->name('update-status');
        Route::post('/{ticket}/assign', [TicketDetailController::class, 'assignTicket'])->name('assign');
        Route::delete('/{ticket}', [TicketDetailController::class, 'destroy'])->name('destroy');
    });
});

// ================================
// ADMIN ROUTES
// ================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Department Management
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/list', [UserManagementController::class, 'getDepartments'])->name('list'); // For dropdown
    });

    // Location Management
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        Route::post('/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Category Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Priority Management
    Route::prefix('priorities')->name('priorities.')->group(function () {
        Route::get('/', [PriorityController::class, 'index'])->name('index');
        Route::post('/', [PriorityController::class, 'store'])->name('store');
        Route::put('/{priority}', [PriorityController::class, 'update'])->name('update');
        Route::delete('/{priority}', [PriorityController::class, 'destroy'])->name('destroy');
        Route::post('/{priority}/toggle-status', [PriorityController::class, 'toggleStatus'])->name('toggle-status');
    });
});
