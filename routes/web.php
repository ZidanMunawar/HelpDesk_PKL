<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PriorityController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileResetPasswordController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDetailController;
use App\Http\Controllers\TicketReportController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherRequestController;
use App\Mail\TicketNotification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/home', function () {
    return view('home');
})->name('home');

// Calendar untuk semua role
Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [App\Http\Controllers\CalendarController::class, 'getEvents'])->name('calendar.events');
Route::get('/calendar/check-access/{ticket}', [App\Http\Controllers\CalendarController::class, 'checkAccess'])->name('calendar.check-access');
Route::get('/calendar/print', [App\Http\Controllers\CalendarController::class, 'print'])->name('calendar.print');

// Route untuk mengecek user yang sedang login (AJAX)
Route::get('/api/user', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'has_remember_me' => Auth::viaRemember()
            ]
        ]);
    }

    return response()->json([
        'authenticated' => false,
        'user' => null
    ]);
})->name('api.user');

// ================================
// CAPTCHA ROUTES (Public)
// ================================
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
// ================================
// PASSWORD RESET ROUTES (From Login Page - FOR GUEST / REGULAR USERS)
// ================================
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

// ✅ TAMBAHKAN THROTTLE: maksimal 3 request per 10 menit
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware(['throttle:3,10'])
    ->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// ================================
// PROFILE PASSWORD RESET ROUTES (From Profile Page - FOR AUTHENTICATED USERS)
// ================================
Route::prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    // ✅ TAMBAHKAN THROTTLE: maksimal 3 request per 10 menit
    Route::post('/password/email', [ProfileResetPasswordController::class, 'sendResetLink'])
        ->middleware(['throttle:3,10'])
        ->name('password.email');
});

// Public reset password routes (for links from email sent via profile)
Route::get('profile/password/reset/{token}', [ProfileResetPasswordController::class, 'showResetForm'])
    ->name('profile.password.reset');

Route::post('profile/password/reset', [ProfileResetPasswordController::class, 'reset'])
    ->name('profile.password.reset.submit');

// Check token validity (AJAX) - tidak perlu throttle karena hanya pengecekan
Route::post('profile/password/check-token', [ProfileResetPasswordController::class, 'checkToken'])
    ->name('profile.password.check-token');
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
// TEST EMAIL ROUTE - HAPUS SETELAH TEST
Route::get('/test-email', function () {
    // Ambil user pertama
    $user = App\Models\User::first();

    // Ambil ticket pertama
    $ticket = App\Models\Ticket::first();

    // Ambil PR pertama (kalau ada)
    $vr = App\Models\VoucherRequest::first();

    // Cek data
    if (!$user || !$ticket) {
        return response()->json([
            'success' => false,
            'message' => 'No user or ticket found in database!'
        ]);
    }

    // Kirim email
    try {
        Mail::to('danzidann5@gmail.com')->send(new App\Mail\VRNotification(
            $user,
            $ticket,
            'TEST PR NOTIFICATION - ' . date('Y-m-d H:i:s'),
            'This is a test email from the PR Notification System. Your email configuration is working correctly!',
            'info',
            $vr
        ));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully to danzidann5@gmail.com',
            'data' => [
                'user' => $user->name,
                'ticket' => $ticket->ticket_number,
                'vr' => $vr ? $vr->vr_number : 'No PR data'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        ]);
    }
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
        Route::post('/upload-cropped-picture', [ProfileController::class, 'uploadCroppedProfilePicture'])->name('upload-cropped-picture');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/remove-picture', [ProfileController::class, 'removeProfilePicture'])->name('remove-picture');

        // Signature Routes - Untuk role tertentu
        Route::post('/upload-signature', [ProfileController::class, 'uploadSignature'])->name('upload-signature');
        Route::post('/remove-signature', [ProfileController::class, 'removeSignature'])->name('remove-signature');
        Route::get('/signature-info', [ProfileController::class, 'getSignatureInfo'])->name('signature-info');
        Route::get('/signature-info-pr', [ProfileController::class, 'getSignatureInfoForPR'])->name('signature-info-pr');
    });

    // ================================
    // TICKET ROUTES (All Authenticated Users)
    // ================================
    Route::prefix('tickets')->name('tickets.')->group(function () {

        // Main Index (All Tickets - semua role bisa lihat)
        Route::get('/', [TicketController::class, 'index'])->name('index');

        // Di dalam group tickets

        // EXPORT ROUTE - TAMBAHKAN INI!
        Route::get('/export', [TicketController::class, 'export'])->name('export');
        // Get saved signature untuk manager dan admin_eng
        Route::get('/tickets/get-saved-signature', [TicketController::class, 'getSavedSignature'])
            ->name('tickets.get-saved-signature');

        // Create & Store - HANYA admin_eng dan user
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');


        // Check access untuk modal (API endpoint)
        Route::get('/{ticket}/check-access', [TicketController::class, 'checkAccess'])->name('check-access');

        // Detail & Actions (Menggunakan TicketDetailController)
        Route::get('/{ticket}', [TicketDetailController::class, 'show'])->name('show');

        // Comment - HANYA user, admin_eng, technician, manager
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

        // User/Manager Check Actions
        Route::post('/{ticket}/user-check', [TicketDetailController::class, 'userCheck'])->name('user-check');

        // GM Actions
        Route::post('/{ticket}/gm-action', [TicketDetailController::class, 'gmAction'])->name('gm-action');

        // Common Actions
        Route::post('/{ticket}/cancel', [TicketDetailController::class, 'cancelTicket'])->name('cancel');

        // Delete - HANYA superadmin
        Route::delete('/{ticket}', [TicketDetailController::class, 'destroy'])->name('destroy');

        // Quick Approve Route
        Route::post('/{ticket}/quick-approve', [TicketDetailController::class, 'quickApprove'])->name('quick-approve');

        // Continue to OM
        Route::post('/{ticket}/continue-to-om', [TicketDetailController::class, 'continueToOM'])->name('continue-to-om');

        // Verify Password for New Signature
        Route::post('/verify-password', [TicketDetailController::class, 'verifyPassword'])->name('verify-password');

        // ========== FOLLOW-UP ROUTES ==========
        // Add Follow-up Notes (Admin Engineering)
        Route::post('/{ticket}/add-followup', [TicketDetailController::class, 'addFollowUpNotes'])->name('add-followup');
        // Edit Ticket Detail (Admin only - saat status OPEN)
        Route::put('/{ticket}/update-detail', [TicketDetailController::class, 'updateDetail'])->name('update-detail');
        // Report Routes
        Route::get('/{ticket}/report', [TicketReportController::class, 'viewReport'])->name('report.view');
        Route::get('/{ticket}/report/download', [TicketReportController::class, 'generateReport'])->name('report.download');
        Route::post('/{ticket}/report/save', [TicketReportController::class, 'saveReport'])->name('report.save');
        Route::get('/reports/cleanup', [TicketReportController::class, 'cleanupTempFiles'])->name('report.cleanup');
    });

    // Reports Routes
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
        Route::get('/ticket/{ticket}/pdf', [TicketReportController::class, 'generateReport'])->name('ticket.pdf');
        Route::get('/ticket/{ticket}/pdf-with-attachments', [TicketReportController::class, 'generateReportWithAttachments'])->name('ticket.pdf-with-attachments');
        Route::get('/ticket/{ticket}/download', [TicketReportController::class, 'downloadReport'])->name('ticket.download');
    });


    // Di routes/web.php, tambahkan di dalam middleware auth group:

    // Voucher Request Routes
    Route::prefix('voucher-requests')->name('voucher-requests.')->group(function () {
        Route::get('/', [VoucherRequestController::class, 'index'])->name('index');
        Route::get('/list', [VoucherRequestController::class, 'getList'])->name('list');
        Route::get('/stats', [VoucherRequestController::class, 'getStats'])->name('stats');
        Route::get('/generate-number', [VoucherRequestController::class, 'generateNumber'])->name('generate-number');
        Route::post('/store', [VoucherRequestController::class, 'store'])->name('store');
        Route::get('/export', [VoucherRequestController::class, 'export'])->name('export');
        Route::get('/{vr}', [VoucherRequestController::class, 'show'])->name('show');
        Route::post('/{vr}/approve', [VoucherRequestController::class, 'approve'])->name('approve');
        Route::post('/{vr}/reject', [VoucherRequestController::class, 'reject'])->name('reject');
        Route::post('/{vr}/mark-paid', [VoucherRequestController::class, 'markPaid'])->name('mark-paid');
        Route::delete('/{vr}', [VoucherRequestController::class, 'destroy'])->name('destroy');

    });
    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/export', [NotificationController::class, 'export'])->name('export');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        Route::post('/broadcast', [NotificationController::class, 'broadcast'])->name('broadcast');
    });

    // Activity Logs Routes (SuperAdmin only via middleware)
    Route::middleware(['auth'])->group(function () {
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
            Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
            Route::get('/statistics', [ActivityLogController::class, 'getStatistics'])->name('statistics');

        });
    });
});
// Settings Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/refresh-cache', [App\Http\Controllers\SettingsController::class, 'refreshCache'])->name('settings.refresh-cache');
    Route::get('/settings/export-activity-log', [App\Http\Controllers\SettingsController::class, 'exportActivityLog'])->name('settings.export-activity-log');

    // Email Config (SuperAdmin only)
    Route::post('/settings/email-config', [App\Http\Controllers\SettingsController::class, 'saveEmailConfig'])->name('settings.email-config');

    // Backup Routes (SuperAdmin only)
    Route::prefix('settings')->group(function () {
        Route::post('/backup', [App\Http\Controllers\SettingsController::class, 'backupDatabase'])->name('settings.backup');
        Route::get('/backup/download/{filename}', [App\Http\Controllers\SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::delete('/backup/{filename}', [App\Http\Controllers\SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    });
});
// My Department untuk Manager
Route::prefix('my-department')->name('my-department.')->group(function () {
    Route::get('/', [App\Http\Controllers\Manager\MyDepartmentController::class, 'index'])->name('index');
    Route::post('/update-name', [App\Http\Controllers\Manager\MyDepartmentController::class, 'updateName'])->name('update-name');
});
// Technician Performance (untuk role dengan akses)
Route::prefix('technician-performance')->name('technician-performance.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Manager\TechnicianPerformanceController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\Manager\TechnicianPerformanceController::class, 'show'])->name('show');
    Route::get('/list/technicians', [App\Http\Controllers\Manager\TechnicianPerformanceController::class, 'getTechnicians'])->name('list');
});

// ================================
// ADMIN ROUTES (For SuperAdmin & Admin)
// ================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'superadmin'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/export', [UserManagementController::class, 'export'])->name('export');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/details', [UserManagementController::class, 'getUserDetails'])->name('details');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk/update', [UserManagementController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/{user}/activate-with-department', [UserManagementController::class, 'activateWithDepartment'])
            ->name('activate-with-department');
    });

    Route::get('/departments/list', [UserManagementController::class, 'getDepartments'])
        ->middleware('auth')
        ->name('departments.list');

    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/{department}/toggle-status', [App\Http\Controllers\Admin\DepartmentController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{department}/toggle-manager-access', [App\Http\Controllers\Admin\DepartmentController::class, 'toggleManagerAccess'])->name('toggle-manager-access');
    });

    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        Route::post('/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::prefix('priorities')->name('priorities.')->group(function () {
        Route::get('/', [PriorityController::class, 'index'])->name('index');
        Route::post('/', [PriorityController::class, 'store'])->name('store');
        Route::put('/{priority}', [PriorityController::class, 'update'])->name('update');
        Route::delete('/{priority}', [PriorityController::class, 'destroy'])->name('destroy');
        Route::post('/{priority}/toggle-status', [PriorityController::class, 'toggleStatus'])->name('toggle-status');
    });
});
