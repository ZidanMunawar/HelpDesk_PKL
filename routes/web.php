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
Route::get('/calendar/print', [App\Http\Controllers\CalendarController::class, 'print'])->name('calendar.print'); // TAMBAH INI


// // Route untuk mengecek user yang sedang login (AJAX)
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
// PASSWORD RESET ROUTES (Public - From Login Page)
// ================================
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// ================================
// PROFILE PASSWORD RESET ROUTES (From Profile Page)
// ================================
Route::prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    // Send reset link from profile (authenticated) - with captcha
    Route::post('/password/email', [ProfileResetPasswordController::class, 'sendResetLink'])
        ->name('password.email');
});

// Public reset password routes (for links from email)
Route::get('password/reset/{token}', [ProfileResetPasswordController::class, 'showResetForm'])
    ->name('profile.password.reset');

Route::post('password/reset', [ProfileResetPasswordController::class, 'reset'])
    ->name('profile.password.reset.submit');

// Check token validity (AJAX)
Route::post('password/check-token', [ProfileResetPasswordController::class, 'checkToken'])
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

        // Password reset via email
        Route::post('/password/email', [ProfileController::class, 'sendResetLink'])->name('password.email');
    });

    // ================================
// TICKET ROUTES (All Authenticated Users)
// ================================
    Route::prefix('tickets')->name('tickets.')->group(function () {

        // Main Index (All Tickets - semua role bisa lihat)
        Route::get('/', [TicketController::class, 'index'])->name('index');

        // Tambahkan route ini di dalam group tickets
        Route::get('/manager-signature', [TicketController::class, 'getManagerSignature'])->name('manager-signature');

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
    // Test dengan queue (jika pakai queue)
    Route::get('/test-queue-email/{ticketId}', function ($ticketId) {
        try {
            $ticket = Ticket::with(['user', 'priority', 'category', 'department'])->findOrFail($ticketId);
            $user = $ticket->user;

            Log::info('=== TEST QUEUE EMAIL START ===', [
                'ticket_id' => $ticket->id,
                'user_email' => $user->email
            ]);

            // Test dengan queue
            Mail::to($user->email)->queue(new TicketNotification(
                $user,
                $ticket,
                'TEST QUEUE: Your Maintenance Request Has Been Created',
                'This is a test email using QUEUE. Your request #' . $ticket->ticket_number . ' has been submitted.',
                'success'
            ));

            Log::info('Queue email dispatched to: ' . $user->email);

            return response()->json([
                'success' => true,
                'message' => 'Queue email dispatched to ' . $user->email,
                'queue_connection' => config('queue.default'),
                'note' => 'Make sure queue worker is running: php artisan queue:work'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->middleware('auth');
    // TEST EMAIL ROUTE - HAPUS SETELAH TESTING
    Route::get('/test-email', function () {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Please login first'], 401);
            }

            // Test send email
            Mail::raw('Test email from ' . config('app.name'), function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Test Email - ' . config('app.name'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent to ' . $user->email,
                'mail_config' => [
                    'default' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->middleware('auth');
    // Voucher Routes
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');

        Route::get('/create-modal/{ticketId?}', [VoucherController::class, 'createModal'])->name('create-modal');
        Route::post('/search-tickets', [VoucherController::class, 'searchTickets'])->name('search-tickets');
        Route::get('/find-ticket/{ticketNumber}', [VoucherController::class, 'findTicketByNumber'])->name('find-ticket');
        Route::post('/', [VoucherController::class, 'store'])->name('store');

        // Signature page for VR approval
        Route::get('/{vr}/signature', [VoucherController::class, 'signaturePage'])->name('signature');

        // Process signature submission
        Route::post('/{vr}/signature-submit', [VoucherController::class, 'submitSignature'])->name('signature-submit');

        Route::get('/modal/approve', [VoucherController::class, 'approveModal'])->name('modal.approve');
        Route::get('/modal/reject', [VoucherController::class, 'rejectModal'])->name('modal.reject');
        Route::get('/modal/mark-paid', [VoucherController::class, 'markPaidModal'])->name('modal.mark-paid');
        Route::get('/{vr}/show-modal', [VoucherController::class, 'showModal'])->name('show-modal');
        Route::post('/{vr}/approve', [VoucherController::class, 'approve'])->name('approve');
        Route::post('/{vr}/reject', [VoucherController::class, 'reject'])->name('reject');
        Route::post('/{vr}/mark-paid', [VoucherController::class, 'markPaid'])->name('mark-paid');
        Route::delete('/{vr}', [VoucherController::class, 'destroy'])->name('destroy');
        Route::get('/{vr}/print', [VoucherController::class, 'print'])->name('print');
        // Di dalam group vouchers
        Route::get('/list', [VoucherController::class, 'list'])->name('list');
        Route::post('/verify-password', [VoucherController::class, 'verifyPassword'])->name('verify-password');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/filter', [NotificationController::class, 'filter'])->name('filter');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/mark-read', [NotificationController::class, 'ajaxMarkAsRead'])->name('ajax-mark-read');
        Route::get('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        // Di routes/web.php dalam group notifications
        Route::post('/broadcast', [NotificationController::class, 'broadcast'])->name('broadcast')->middleware('superadmin');
    });

    // Activity Logs Routes
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
        Route::post('/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('clear-old');
        Route::delete('/{id}', [ActivityLogController::class, 'destroy'])->name('destroy');
        Route::get('/ticket/{ticketId}/activities', [ActivityLogController::class, 'getTicketActivities'])->name('ticket-activities');
        Route::get('/statistics', [ActivityLogController::class, 'getStatistics'])->name('statistics');
    });
});

// My Department untuk Manager - tanpa middleware, langsung di controller
// My Department untuk Manager (tanpa middleware)
Route::prefix('my-department')->name('my-department.')->group(function () {
    Route::get('/', [App\Http\Controllers\Manager\MyDepartmentController::class, 'index'])->name('index');
    Route::post('/update-name', [App\Http\Controllers\Manager\MyDepartmentController::class, 'updateName'])->name('update-name');
});
// ================================
// ADMIN ROUTES (For SuperAdmin & Admin)
// ================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'superadmin'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/export', [UserManagementController::class, 'export'])->name('export');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/details', [UserManagementController::class, 'getUserDetails'])->name('details');

        // Store - Only SuperAdmin
        Route::post('/', [UserManagementController::class, 'store'])->name('store');

        // Update - SuperAdmin & Admin Eng
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');

        // Destroy - Only SuperAdmin
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');

        // Toggle Status - SuperAdmin & Admin Eng
        Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');

        // Bulk Operations - Only SuperAdmin
        Route::post('/bulk/update', [UserManagementController::class, 'bulkUpdate'])->name('bulk-update');
        // Activate with department
        Route::post('/{user}/activate-with-department', [UserManagementController::class, 'activateWithDepartment'])
            ->name('activate-with-department');
    });

    // Departments list for dropdown
    Route::get('/departments/list', [UserManagementController::class, 'getDepartments'])
        ->middleware('auth')
        ->name('departments.list');

    // Department Management
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{department}/details', [DepartmentController::class, 'getDetails'])->name('details');
        Route::get('/list', [UserManagementController::class, 'getDepartments'])->name('list');
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
