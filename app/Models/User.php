<?php

namespace App\Models;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Signature;
use App\Models\Ticket;
use App\Models\TicketApproval;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\VoucherRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_picture',
        'signature_path',
        'has_signature',
        'signature_updated_at',
        'role',
        'department_id',
        'status',
        'email_verified_at',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'has_signature' => 'boolean',
        'signature_updated_at' => 'datetime',
    ];

    // ==================== ROLE CHECKS ====================


    /**
     * Check if user is admin engineering
     */
    public function isAdminEng(): bool
    {
        return $this->role === 'admin_eng';
    }



    /**
     * Check if user is OM (Operational Manager)
     */
    public function isOM(): bool
    {
        return $this->role === 'om';
    }

    /**
     * Check if user is technician
     */
    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user can approve tickets
     */
    public function canApprove(): bool
    {
        return in_array($this->role, ['superadmin', 'admin_eng', 'gm', 'om', 'manager']);
    }

    /**
     * Check if user can manage tickets (assign, delete, etc)
     */
    public function canManageTickets(): bool
    {
        return in_array($this->role, ['superadmin', 'admin_eng']);
    }

    /**
     * Check if user can manage master data (departments, categories, etc)
     */
    public function canManageMasterData(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Check if user can view all tickets
     */
    public function canViewAllTickets(): bool
    {
        return in_array($this->role, ['superadmin', 'admin_eng', 'gm', 'om', 'manager']);
    }

    /**
     * Check if user has department (technician requirement)
     */
    public function hasDepartment(): bool
    {
        return !is_null($this->department_id);
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        // Only superadmin can fully manage users
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can create/delete users
     */
    public function canCreateDeleteUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can edit users (limited)
     */
    public function canEditUsers(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Check if user can assign technicians
     */
    public function canAssignTechnician(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Check if user can receive tickets (admin_eng only)
     */
    public function canReceiveTickets(): bool
    {
        return $this->isAdminEng() || $this->isSuperAdmin();
    }

    /**
     * Check if user can close tickets
     */
    public function canCloseTickets(): bool
    {
        return $this->isAdminEng() || $this->isSuperAdmin();
    }

    /**
     * Check if user can approve OM stage
     */
    public function canApproveOM(): bool
    {
        return $this->isOM() || $this->isSuperAdmin();
    }

    /**
     * Check if user can approve GM stage
     */
    public function canApproveGM(): bool
    {
        return $this->isGM() || $this->isSuperAdmin();
    }

    /**
     * Check if user can check completed work (user check)
     */
    public function canCheckWork(): bool
    {
        return $this->isUser() || $this->isSuperAdmin();
    }

    // ==================== STATUS CHECKS ====================


    /**
     * Check if user is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if user has signature uploaded
     */
    public function hasSignature(): bool
    {
        return $this->has_signature && !empty($this->signature_path);
    }

    // ==================== SCOPES ====================

    /**
     * Scope for superadmin users
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'superadmin');
    }

    /**
     * Scope for admin_eng users
     */
    public function scopeAdminEngs($query)
    {
        return $query->where('role', 'admin_eng');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending users
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for GM users
     */
    public function scopeGMs($query)
    {
        return $query->where('role', 'gm');
    }

    /**
     * Scope for OM users
     */
    public function scopeOMs($query)
    {
        return $query->where('role', 'om');
    }

    /**
     * Scope for technicians
     */
    public function scopeTechnicians($query)
    {
        return $query->where('role', 'technician');
    }

    /**
     * Scope for regular users
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for managers
     */
    public function scopeManagers($query)
    {
        return $query->where('role', 'manager');
    }

    /**
     * Scope for users with department
     */
    public function scopeWithDepartment($query)
    {
        return $query->whereNotNull('department_id');
    }

    /**
     * Scope for users by specific role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for users by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for users with signature
     */
    public function scopeWithSignature($query)
    {
        return $query->where('has_signature', true)->whereNotNull('signature_path');
    }

    /**
     * Scope for users who can sign (OM, GM, Admin Eng)
     */
    public function scopeCanSign($query)
    {
        return $query->whereIn('role', ['om', 'gm', 'admin_eng']);
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Tickets created by user
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Tickets assigned to user (for technician/admin)
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Active assigned tickets (in_progress, pending_vr, completed)
     */
    public function activeAssignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to')
            ->whereIn('status', ['in_progress', 'pending_vr', 'completed']);
    }

    /**
     * Comments made by user
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Notifications for user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Unread notifications
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    /**
     * Activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Ticket events created by user
     */
    public function ticketEvents()
    {
        return $this->hasMany(TicketEvent::class, 'created_by');
    }

    /**
     * Department of this user
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Department managed by this user (if manager)
     */
    public function managedDepartment()
    {
        return $this->hasOne(Department::class, 'manager_id');
    }

    /**
     * Signatures created by user
     */
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    /**
     * Voucher requests created by user
     */
    public function voucherRequests()
    {
        return $this->hasMany(VoucherRequest::class, 'created_by');
    }

    /**
     * Voucher requests approved by user as admin
     */
    public function adminApprovedVouchers()
    {
        return $this->hasMany(VoucherRequest::class, 'admin_approved_by');
    }

    /**
     * Voucher requests approved by user as OM
     */
    public function omApprovedVouchers()
    {
        return $this->hasMany(VoucherRequest::class, 'om_approved_by');
    }

    /**
     * Voucher requests approved by user as GM
     */
    public function gmApprovedVouchers()
    {
        return $this->hasMany(VoucherRequest::class, 'gm_approved_by');
    }

    /**
     * Ticket approvals where user is admin_eng_received_by
     */
    public function receivedTicketApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'admin_eng_received_by');
    }

    /**
     * Ticket approvals where user is om_approved_by
     */
    public function omApprovedTicketApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'om_approved_by');
    }

    /**
     * Ticket approvals where user is user_checked_by
     */
    public function userCheckedTicketApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'user_checked_by');
    }

    /**
     * Ticket approvals where user is gm_approved_by
     */
    public function gmApprovedTicketApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'gm_approved_by');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture && file_exists(storage_path('app/public/' . $this->profile_picture))) {
            return asset('storage/' . $this->profile_picture);
        }

        // Default avatar using UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get signature URL
     */
    public function getSignatureUrlAttribute()
    {
        if ($this->signature_path && file_exists(storage_path('app/public/' . $this->signature_path))) {
            return asset('storage/' . $this->signature_path);
        }

        return null;
    }

    /**
     * Get user's full role name
     */
    public function getRoleNameAttribute()
    {
        $roleNames = [
            'superadmin' => 'Super Administrator',
            'admin_eng' => 'Admin Engineering',
            'gm' => 'General Manager',
            'om' => 'Operational Manager',
            'technician' => 'Technician',
            'user' => 'User',
            'manager' => 'Department Manager',
        ];

        return $roleNames[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        $colors = [
            'superadmin' => 'dark',
            'admin_eng' => 'danger',
            'gm' => 'info',
            'om' => 'primary',
            'technician' => 'success',
            'user' => 'secondary',
            'manager' => 'warning',
        ];

        return $colors[$this->role] ?? 'secondary';
    }

    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->role_badge_color . '">' . $this->role_name . '</span>';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'pending' => 'warning',
            'inactive' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_badge_color . '">' . ucfirst($this->status) . '</span>';
    }

    /**
     * Get unread notification count
     */
    public function getUnreadNotificationCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get active assigned tickets count (for technician)
     */
    public function getActiveAssignedTicketsCountAttribute()
    {
        return $this->activeAssignedTickets()->count();
    }

    /**
     * Get user initials (for avatar)
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get avatar color from name
     */
    public function getAvatarColorAttribute()
    {
        $colors = [
            '#003366', // Harris Blue
            '#ff6600', // Harris Orange
            '#28a745', // Green
            '#6c757d', // Gray
            '#6610f2', // Purple
            '#dc3545', // Red
            '#fd7e14', // Orange
            '#20c997', // Teal
            '#17a2b8', // Cyan
            '#e83e8c', // Pink
        ];

        // Use name to consistently get same color
        $index = crc32($this->name) % count($colors);
        return $colors[$index];
    }

    /**
     * Check if user can edit ticket
     */
    public function canEditTicket($ticket): bool
    {
        // Superadmin & Admin Eng can edit any ticket
        if ($this->isSuperAdmin() || $this->isAdminEng()) {
            return true;
        }

        // User can edit their own ticket if status is open
        if ($ticket->user_id === $this->id && $ticket->status === 'open') {
            return true;
        }

        // Technician can edit assigned ticket if in progress
        if (
            $this->isTechnician() && $ticket->assigned_to === $this->id &&
            in_array($ticket->status, ['in_progress', 'pending_vr'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can delete ticket
     */
    public function canDeleteTicket(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Check if user can create voucher request
     */
    public function canCreateVoucherRequest(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng() || $this->isTechnician();
    }

    /**
     * Check if user can approve voucher request
     */
    public function canApproveVoucherRequest($stage = null): bool
    {
        if ($stage === 'admin') {
            return $this->isSuperAdmin() || $this->isAdminEng();
        } elseif ($stage === 'om') {
            return $this->isOM() || $this->isSuperAdmin();
        } elseif ($stage === 'gm') {
            return $this->isGM() || $this->isSuperAdmin();
        }

        return in_array($this->role, ['superadmin', 'admin_eng', 'gm', 'om']);
    }

    /**
     * Check if user needs to sign (OM, GM, Admin Eng)
     */
    public function needsToSign(): bool
    {
        return in_array($this->role, ['om', 'gm', 'admin_eng']) && !$this->hasSignature();
    }

    /**
     * Get dashboard route based on role
     */
    public function getDashboardRouteAttribute()
    {
        return route('dashboard');
    }

    /**
     * Get total tickets created by user
     */
    public function getTotalTicketsCreatedAttribute()
    {
        return $this->tickets()->count();
    }

    /**
     * Get total tickets resolved (for technician)
     */
    public function getTotalTicketsResolvedAttribute()
    {
        return $this->assignedTickets()
            ->where('status', 'closed')
            ->count();
    }

    /**
     * Get average resolution time (for technician) in hours
     */
    public function getAverageResolutionTimeAttribute()
    {
        $tickets = $this->assignedTickets()
            ->where('status', 'closed')
            ->whereNotNull('closed_at')
            ->get();

        if ($tickets->count() === 0) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($tickets as $ticket) {
            if ($ticket->created_at && $ticket->closed_at) {
                $totalMinutes += $ticket->created_at->diffInMinutes($ticket->closed_at);
            }
        }

        return round($totalMinutes / $tickets->count() / 60, 2); // Convert to hours
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports(): bool
    {
        return in_array($this->role, ['superadmin', 'admin_eng', 'gm', 'om', 'manager']);
    }

    /**
     * Check if user can view activity logs
     */
    public function canViewActivityLogs(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminEng();
    }

    /**
     * Get user's permissions array
     */
    public function getPermissionsAttribute(): array
    {
        return [
            'can_manage_tickets' => $this->canManageTickets(),
            'can_assign_technician' => $this->canAssignTechnician(),
            'can_receive_tickets' => $this->canReceiveTickets(),
            'can_close_tickets' => $this->canCloseTickets(),
            'can_approve_om' => $this->canApproveOM(),
            'can_approve_gm' => $this->canApproveGM(),
            'can_check_work' => $this->canCheckWork(),
            'can_create_vr' => $this->canCreateVoucherRequest(),
            'can_approve_vr' => $this->canApproveVoucherRequest(),
            'can_view_all_tickets' => $this->canViewAllTickets(),
            'can_view_reports' => $this->canViewReports(),
            'can_view_activity_logs' => $this->canViewActivityLogs(),
            'can_access_admin_panel' => $this->canAccessAdminPanel(),
            'has_signature' => $this->hasSignature(),
        ];
    }

    /**
     * Format phone number
     */
    public function getFormattedPhoneAttribute()
    {
        if (empty($this->phone)) {
            return '-';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        // Format based on length
        if (strlen($phone) === 12 && substr($phone, 0, 2) === '62') {
            // Format: +62 812-3456-7890
            return '+62 ' . substr($phone, 2, 3) . '-' . substr($phone, 5, 4) . '-' . substr($phone, 9, 4);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            // Format: 0812-3456-7890
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8, 3);
        }

        return $this->phone;
    }

    /**
     * Get email verification status
     */
    public function getEmailVerifiedStatusAttribute()
    {
        if ($this->email_verified_at) {
            return '<span class="badge bg-success">Verified</span>';
        } else {
            return '<span class="badge bg-warning">Pending</span>';
        }
    }

    /**
     * Get human readable created at
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y H:i') : '-';
    }

    /**
     * Get human readable updated at
     */
    public function getUpdatedAtHumanAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d M Y H:i') : '-';
    }

    /**
     * Get full user info for display
     */
    public function getDisplayInfoAttribute()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->formatted_phone,
            'role' => $this->role_name,
            'status' => ucfirst($this->status),
            'department' => $this->department ? $this->department->name : 'No Department',
            'email_verified' => $this->email_verified_at ? 'Yes' : 'No',
            'has_signature' => $this->hasSignature() ? 'Yes' : 'No',
            'created_at' => $this->created_at_human,
        ];
    }
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is admin (admin_eng)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin_eng';
    }

    /**
     * Check if user is admin or superadmin
     */
    public function isAdminOrSuperAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin_eng']);
    }


    /**
     * Check if user is regular user
     */
    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }




    /**
     * Check if user is General Manager
     */
    public function isGM(): bool
    {
        return $this->role === 'gm';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has signature capability
     */
    public function canSign(): bool
    {
        return in_array($this->role, ['admin_eng', 'om', 'gm', 'technician']);
    }

    // app/Models/User.php - Tambahkan method ini di bagian HELPER METHODS

    /**
     * Check if user can manage signature
     * Roles: admin_eng, om, gm, manager
     */
    public function canManageSignature(): bool
    {
        return in_array($this->role, ['admin_eng', 'om', 'gm', 'manager']);
    }

    /**
     * Get signature badge status
     */
    public function getSignatureStatusAttribute()
    {
        if ($this->has_signature) {
            return '<span class="badge bg-success">Uploaded</span>';
        }
        return '<span class="badge bg-warning">Not Uploaded</span>';
    }

    /**
     * Update signature with validation
     */
    public function updateSignature($path)
    {
        $this->signature_path = $path;
        $this->has_signature = true;
        $this->signature_updated_at = now();
        $this->save();

        return $this;
    }

    /**
     * Remove signature
     */
    public function removeSignature()
    {
        if ($this->signature_path && Storage::disk('public')->exists($this->signature_path)) {
            Storage::disk('public')->delete($this->signature_path);
        }

        $this->signature_path = null;
        $this->has_signature = false;
        $this->signature_updated_at = null;
        $this->save();

        return $this;
    }

    // app/Models/User.php - Perbaiki method sendPasswordResetNotification

    /**
     * Send password reset notification
     * Override untuk menggunakan tabel password_resets dan email template yang konsisten
     */
    public function sendPasswordResetNotification($token)
    {
        $resetLink = route('password.reset', ['token' => $token, 'email' => $this->email]);

        // Gunakan email template yang sama untuk konsistensi
        \Illuminate\Support\Facades\Mail::send('emails.password-reset', [
            'user' => $this,
            'resetLink' => $resetLink,
            'expiry' => \Carbon\Carbon::now()->addHours(1),
            'source' => 'system' // Tambahkan source untuk identifikasi
        ], function ($message) {
            $message->to($this->email, $this->name)
                ->subject('Reset Password Notification - ' . config('app.name'));
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

}

