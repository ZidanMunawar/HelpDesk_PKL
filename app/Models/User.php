<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'password',
        'phone',
        'profile_picture',
        'role',
        'department_id',
        'status',
        'email_verified_at',
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
    ];

    // ==================== ROLE CHECKS ====================

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is GM (General Manager)
     */
    public function isGM(): bool
    {
        return $this->role === 'gm';
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
     * Check if user can approve tickets
     */
    public function canApprove(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'gm', 'om']);
    }

    /**
     * Check if user can manage tickets (assign, delete, etc)
     */
    public function canManageTickets(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can manage master data
     */
    public function canManageMasterData(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can view all tickets
     */
    public function canViewAllTickets(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'gm', 'om']);
    }

    /**
     * Check if user has department (technician requirement)
     */
    public function hasDepartment(): bool
    {
        return !is_null($this->department_id);
    }

    // ==================== STATUS CHECKS ====================

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

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

    // ==================== SCOPES ====================

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
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for manager users (Manager, GM, OM)
     */
    public function scopeManagers($query)
    {
        return $query->whereIn('role', ['manager', 'gm', 'om']);
    }



    /**
     * Scope for regular users
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
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
     * Active assigned tickets (open, in_progress, pending)
     */
    public function activeAssignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to')
            ->whereIn('status', ['open', 'in_progress', 'pending']);
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
     * Ticket approvals (as manager approver)
     */
    public function managerApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'manager_approved_by');
    }

    /**
     * Ticket approvals (as GM approver)
     */
    public function gmApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'gm_approved_by');
    }

    /**
     * Ticket approvals (as OM approver)
     */
    public function omApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'om_approved_by');
    }

    /**
     * Ticket approvals (as admin checker)
     */
    public function adminApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'admin_checked_by');
    }

    /**
     * Voucher requests created by user
     */
    public function voucherRequests()
    {
        return $this->hasMany(VoucherRequest::class, 'created_by');
    }

    /**
     * Voucher requests approved by user
     */
    public function approvedVoucherRequests()
    {
        return $this->hasMany(VoucherRequest::class, 'approved_by');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        // Default avatar using UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get user's full role name
     */
    public function getRoleNameAttribute()
    {
        $roleNames = [
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'gm' => 'General Manager',
            'om' => 'Operational Manager',
            'technician' => 'Technician',
            'user' => 'User',
        ];

        return $roleNames[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        $colors = [
            'admin' => 'danger',
            'manager' => 'warning',
            'gm' => 'info',
            'om' => 'primary',
            'technician' => 'success',
            'user' => 'secondary',
        ];

        return $colors[$this->role] ?? 'secondary';
    }

    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute()
    {
        return '<span class="badge badge-' . $this->role_badge_color . '">' . $this->role_name . '</span>';
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
        return '<span class="badge badge-' . $this->status_badge_color . '">' . ucfirst($this->status) . '</span>';
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
     * Check if user can edit ticket
     */
    public function canEditTicket($ticket): bool
    {
        // Admin can edit any ticket
        if ($this->isAdmin()) {
            return true;
        }

        // User can edit their own ticket if status is pending_approval
        if ($ticket->user_id === $this->id && $ticket->isPendingApproval()) {
            return true;
        }

        // Technician can edit assigned ticket
        if ($this->isTechnician() && $ticket->assigned_to === $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can delete ticket
     */
    public function canDeleteTicket(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can assign technician
     */
    public function canAssignTechnician(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can create voucher request
     */
    public function canCreateVoucherRequest(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can approve voucher request
     */
    public function canApproveVoucherRequest(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'gm', 'om']);
    }

    /**
     * Get dashboard route based on role
     */
    public function getDashboardRouteAttribute()
    {
        return route('dashboard');
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        // Custom email verification logic jika perlu
        parent::sendEmailVerificationNotification();
    }

    // ==================== STATISTICS METHODS ====================

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
            ->where('status', 'resolved')
            ->count();
    }

    /**
     * Get total tickets closed (for technician)
     */
    public function getTotalTicketsClosedAttribute()
    {
        return $this->assignedTickets()
            ->where('status', 'closed')
            ->count();
    }
    /**
     * Scope for technicians
     */
    public function scopeTechnicians($query)
    {
        return $query->where('role', 'technician');
    }



    /**
     * Check if user has completed registration (has department)
     */
    public function hasCompletedRegistration(): bool
    {
        return !is_null($this->department_id);
    }
    /**
     * Get average resolution time (for technician) in hours
     */
    public function getAverageResolutionTimeAttribute()
    {
        $tickets = $this->assignedTickets()
            ->whereNotNull('resolved_at')
            ->get();

        if ($tickets->count() === 0) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($tickets as $ticket) {
            $totalMinutes += $ticket->created_at->diffInMinutes($ticket->resolved_at);
        }

        return round($totalMinutes / $tickets->count() / 60, 2); // Convert to hours
    }

    /**
     * Get performance rating (for technician) - Simple calculation
     */
    public function getPerformanceRatingAttribute()
    {
        $resolved = $this->total_tickets_resolved;
        $avgTime = $this->average_resolution_time;

        if ($resolved === 0) {
            return 0;
        }

        // Simple rating: more resolved tickets + faster time = better
        // Max 5 stars
        $rating = min(5, ($resolved / 10) + (24 / max($avgTime, 1)));

        return round($rating, 1);
    }
}
