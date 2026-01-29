<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
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
    return view('home');
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
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

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
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('info', 'Email already verified. Please login.');
    }

    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    \Log::info('Email verified for user: ' . $user->email);

    Auth::login($user);

    if (!$user->isActive()) {
        Auth::logout();

        $message = $user->role === 'technician'
            ? 'Email verified! Your technician account is pending approval from admin. You will be notified once approved.'
            : 'Email verified! Your account is not active yet. Please contact administrator.';

        return redirect()->route('login')->with('success', $message);
    }

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

        // Main Index (All Tickets - semua role bisa lihat)
        Route::get('/', [TicketController::class, 'index'])->name('index');

        // Create & Store - HANYA admin_eng dan user
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');

        // Check access untuk modal (API endpoint) - DITAMBAHKAN KEMBALI
        Route::get('/{ticket}/check-access', [TicketController::class, 'checkAccess'])->name('check-access');

        // Detail & Actions (Menggunakan TicketDetailController)
        Route::get('/{ticket}', [TicketDetailController::class, 'show'])->name('show');

        // Comment - HANYA user, admin_eng, technician
        Route::post('/{ticket}/comment', [TicketDetailController::class, 'addComment'])->name('add-comment');

        // === ALUR BARU ===

        // Admin Engineering Actions
        Route::post('/{ticket}/receive', [TicketDetailController::class, 'receiveTicket'])->name('receive');
        Route::post('/{ticket}/assign', [TicketDetailController::class, 'assignTicket'])->name('assign');
        Route::post('/{ticket}/close-admin', [TicketDetailController::class, 'closeTicket'])->name('close-admin');

        // OM Actions
        Route::post('/{ticket}/om-action', [TicketDetailController::class, 'omAction'])->name('om-action');

        // Technician Actions
        Route::post('/{ticket}/complete', [TicketDetailController::class, 'technicianComplete'])->name('complete');
        Route::post('/{ticket}/request-vr', [TicketDetailController::class, 'requestVR'])->name('request-vr');

        // User Actions
        Route::post('/{ticket}/user-check', [TicketDetailController::class, 'userCheck'])->name('user-check');

        // GM Actions
        Route::post('/{ticket}/gm-action', [TicketDetailController::class, 'gmAction'])->name('gm-action');

        // Common Actions
        Route::post('/{ticket}/cancel', [TicketDetailController::class, 'cancelTicket'])->name('cancel');

        // Delete - HANYA superadmin
        Route::delete('/{ticket}', [TicketDetailController::class, 'destroy'])->name('destroy');

        // Quick Approve Route
        Route::post('/{ticket}/quick-approve', [TicketDetailController::class, 'quickApprove'])->name('quick-approve');
        // Di routes/web.php
        Route::post('/{ticket}/continue-to-om', [TicketDetailController::class, 'continueToOM'])->name('continue-to-om');
        // Verify Password for New Signature
        Route::post('/verify-password', [TicketDetailController::class, 'verifyPassword'])->name('verify-password');
        // VR Routes (akan dibuat controller terpisah)

    });
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        // Main VR Listing
        Route::get('/', [VoucherController::class, 'index'])->name('index');

        // Create VR via Modal
        Route::get('/create-modal/{ticket_id}', [VoucherController::class, 'createModal'])->name('create-modal');

        // Store VR (via AJAX)
        Route::post('/{ticket_id}/store', [VoucherController::class, 'store'])->name('store');

        // View VR Details Modal
        Route::get('/{vr}/show-modal', [VoucherController::class, 'showModal'])->name('show-modal');

        // Approve/Reject VR
        Route::post('/{vr}/approve', [VoucherController::class, 'approve'])->name('approve');

        // Mark as Paid
        Route::post('/{vr}/mark-as-paid', [VoucherController::class, 'markAsPaid'])->name('mark-as-paid');

        // Delete VR
        Route::delete('/{vr}', [VoucherController::class, 'destroy'])->name('destroy');
    });

    // API routes untuk VR support
    Route::middleware(['auth'])->group(function () {
        Route::get('/api/tickets/find-by-number/{ticketNumber}', function ($ticketNumber) {
            $ticket = \App\Models\Ticket::where('ticket_number', $ticketNumber)->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number
            ]);
        });

        Route::get('/api/tickets/my-recent-tickets', function () {
            $user = auth()->user();

            $tickets = \App\Models\Ticket::where('user_id', $user->id)
                ->whereHas('approval', function ($q) {
                    $q->where('needs_vr', true);
                })
                ->where('status', 'pending_vr')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'ticket_number', 'title']);

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);
        });

        Route::get('/api/tickets/pending-vr-tickets', function () {
            $tickets = \App\Models\Ticket::whereHas('approval', function ($q) {
                $q->where('needs_vr', true);
            })
                ->where('status', 'pending_vr')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'ticket_number', 'title', 'user_id']);

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);
        });
    });
});

// ================================
// ADMIN ROUTES (For SuperAdmin & Admin)
// ================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_or_superadmin'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management (All admin roles can access, but permissions differ)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/details', [UserManagementController::class, 'getUserDetails'])->name('details');

        // Store - Only SuperAdmin
        Route::middleware('superadmin')->group(function () {
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
        });

        // Update - SuperAdmin & Admin (with restrictions)
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');

        // Destroy - Only SuperAdmin
        Route::middleware('superadmin')->group(function () {
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        });

        // Toggle Status - SuperAdmin & Admin (with restrictions)
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
