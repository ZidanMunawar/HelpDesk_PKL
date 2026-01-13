<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketApproval extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'manager_approved',
        'manager_approved_by',
        'manager_approved_at',
        'gm_approved',
        'gm_approved_by',
        'gm_approved_at',
        'om_approved',
        'om_approved_by',
        'om_approved_at',
        'admin_check',
        'admin_checked_by',
        'admin_checked_at',
        'rejection_reason',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'manager_approved' => 'boolean',
        'manager_approved_at' => 'datetime',
        'gm_approved' => 'boolean',
        'gm_approved_at' => 'datetime',
        'om_approved' => 'boolean',
        'om_approved_at' => 'datetime',
        'admin_check' => 'boolean',
        'admin_checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Ticket that this approval belongs to
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Manager who approved
     */
    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    /**
     * GM who approved
     */
    public function gmApprover()
    {
        return $this->belongsTo(User::class, 'gm_approved_by');
    }

    /**
     * OM who approved
     */
    public function omApprover()
    {
        return $this->belongsTo(User::class, 'om_approved_by');
    }

    /**
     * Admin who checked
     */
    public function adminChecker()
    {
        return $this->belongsTo(User::class, 'admin_checked_by');
    }

    // ==================== SCOPES ====================

    /**
     * Scope for pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if all approvals are complete
     */
    public function isFullyApproved(): bool
    {
        return $this->manager_approved
            && $this->gm_approved
            && $this->om_approved
            && $this->admin_check;
    }

    /**
     * Check if any approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get approval progress percentage
     */
    public function getProgressPercentage(): int
    {
        $total = 4; // 4 approval levels
        $approved = 0;

        if ($this->manager_approved)
            $approved++;
        if ($this->gm_approved)
            $approved++;
        if ($this->om_approved)
            $approved++;
        if ($this->admin_check)
            $approved++;

        return round(($approved / $total) * 100);
    }

    /**
     * Approve by Manager
     */
    public function approveByManager($userId)
    {
        $this->update([
            'manager_approved' => true,
            'manager_approved_by' => $userId,
            'manager_approved_at' => now(),
        ]);

        $this->checkAndUpdateStatus();
    }

    /**
     * Approve by GM
     */
    public function approveByGM($userId)
    {
        $this->update([
            'gm_approved' => true,
            'gm_approved_by' => $userId,
            'gm_approved_at' => now(),
        ]);

        $this->checkAndUpdateStatus();
    }

    /**
     * Approve by OM
     */
    public function approveByOM($userId)
    {
        $this->update([
            'om_approved' => true,
            'om_approved_by' => $userId,
            'om_approved_at' => now(),
        ]);

        $this->checkAndUpdateStatus();
    }

    /**
     * Admin check
     */
    public function checkByAdmin($userId)
    {
        $this->update([
            'admin_check' => true,
            'admin_checked_by' => $userId,
            'admin_checked_at' => now(),
        ]);

        $this->checkAndUpdateStatus();
    }

    /**
     * Approve all at once (Admin shortcut)
     */
    public function approveAll($userId)
    {
        $this->update([
            'manager_approved' => true,
            'manager_approved_by' => $userId,
            'manager_approved_at' => now(),
            'gm_approved' => true,
            'gm_approved_by' => $userId,
            'gm_approved_at' => now(),
            'om_approved' => true,
            'om_approved_by' => $userId,
            'om_approved_at' => now(),
            'admin_check' => true,
            'admin_checked_by' => $userId,
            'admin_checked_at' => now(),
            'status' => 'approved',
        ]);

        // Update ticket approval status
        $this->ticket->update(['approval_status' => 'approved']);
    }

    /**
     * Reject approval
     */
    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Update ticket approval status
        $this->ticket->update(['approval_status' => 'rejected']);
    }

    /**
     * Check if all approved and update status
     */
    protected function checkAndUpdateStatus()
    {
        if ($this->isFullyApproved()) {
            $this->update(['status' => 'approved']);

            // Update ticket approval status
            $this->ticket->update(['approval_status' => 'approved']);
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
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
}
