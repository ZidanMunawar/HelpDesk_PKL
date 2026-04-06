<?php
// app/Models/Ticket.php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'category_id',
        'department_id',
        'priority_id',
        'location_id',
        'location_manual',
        'user_id',
        'assigned_to',
        'status',
        'current_stage',
        'approval_status',
        'due_date',
        'resolved_at',
        'closed_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    // Di dalam class Ticket (app/Models/Ticket.php)
// Tambahkan relationship ini:

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Tambahkan juga method ini jika belum ada:

    public function getLocationDisplayAttribute()
    {
        if ($this->location_id) {
            $location = $this->location;
            if ($location) {
                $display = $location->name;
                if ($location->location_type) {
                    $display .= ' (' . ucfirst($location->location_type) . ')';
                }
                if ($location->floor_number) {
                    $display .= ' - Floor: ' . $location->floor_number;
                }
                return $display;
            }
        } elseif ($this->location_manual) {
            return $this->location_manual . ' (Manual)';
        }
        return 'N/A';
    }


    /**
     * Boot method for auto-generating ticket number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber()
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTicket ? intval(substr($lastTicket->ticket_number, -4)) + 1 : 1;

        return 'MR-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Scopes

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['closed', 'cancelled']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['closed', 'resolved', 'cancelled']);
    }

    // Relationships

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // public function attachments()
    // {
    //     return $this->hasMany(TicketAttachment::class);
    // }

    // public function comments()
    // {
    //     return $this->hasMany(TicketComment::class);
    // }
    // public function activities()
    // {
    //     return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'asc');
    // }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function events()
    {
        return $this->hasMany(TicketEvent::class);
    }

    // Helper Methods

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['closed', 'resolved', 'cancelled']);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'open' => '<span class="badge badge-primary">Open</span>',
            'in_progress' => '<span class="badge badge-info">In Progress</span>',
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'resolved' => '<span class="badge badge-success">Resolved</span>',
            'closed' => '<span class="badge badge-secondary">Closed</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">' . ucfirst($this->status) . '</span>';
    }
    // ==================== RELATIONSHIPS ====================

    // Tambahkan ini di dalam class Ticket

    /**
     * Signatures for this ticket
     */
    // public function signatures()
    // {
    //     return $this->hasMany(Signature::class);
    // }

    /**
     * Voucher requests for this ticket
     */
    // public function voucherRequests()
    // {
    //     return $this->hasMany(VoucherRequest::class);
    // }

    /**
     * Ticket approvals
     */
    public function ticketApprovals()
    {
        return $this->hasOne(TicketApproval::class);
    }

    /**
     * Activity logs for this ticket
     */
    // public function activities()
    // {
    //     return $this->hasMany(ActivityLog::class);
    // }

    /**
     * Ticket events
     */
    public function ticketEvents()
    {
        return $this->hasMany(TicketEvent::class);
    }
    /**
     * Get the ticket approval record.
     */
    public function approval()
    {
        return $this->hasOne(TicketApproval::class, 'ticket_id');
    }

    /**
     * Get the signatures for the ticket.
     */
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    /**
     * Get the voucher requests for the ticket.
     */
    public function voucherRequests()
    {
        return $this->hasMany(VoucherRequest::class);
    }

    /**
     * Get the activities for the ticket.
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class)->whereNotNull('ticket_id');
    }

    /**
     * Get the comments for the ticket.
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Get the attachments for the ticket.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
    // Di dalam class Ticket, tambahkan method ini:

    /**
     * Get status display name (for user view)
     */
    public function getStatusDisplayAttribute()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            if ($this->status === 'pending_om') {
                return 'in_progress';
            } elseif ($this->status === 'pending_gm') {
                return 'completed';
            }
        }

        return $this->status;
    }

    /**
     * Get status label with proper class
     */
    public function getStatusLabelAttribute()
    {
        $displayStatus = $this->status_display;

        $labels = [
            'open' => '<span class="badge badge-primary">Open</span>',
            'received' => '<span class="badge badge-info">Received</span>',
            'pending_om' => '<span class="badge badge-warning">Pending OM</span>',
            'in_progress' => '<span class="badge badge-info">In Progress</span>',
            'pending_vr' => '<span class="badge badge-warning">Pending VR</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'pending_gm' => '<span class="badge badge-warning">Pending GM</span>',
            'closed' => '<span class="badge badge-secondary">Closed</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
        ];

        return $labels[$displayStatus] ?? '<span class="badge badge-secondary">' . ucfirst($this->status) . '</span>';
    }
}
