<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'voucher_requests';

    protected $fillable = [
        'vr_number',
        'ticket_id',
        'status',
        'notes',
        'rejection_reason',
        'created_by',
        'admin_approved',
        'admin_approved_by',
        'admin_approved_at',
        'om_approved',
        'om_approved_by',
        'om_approved_at',
        'gm_approved',
        'gm_approved_by',
        'gm_approved_at',
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'om_approved' => 'boolean',
        'gm_approved' => 'boolean',
        'admin_approved_at' => 'datetime',
        'om_approved_at' => 'datetime',
        'gm_approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the ticket associated with this VR
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who created this VR
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin who approved this VR
     */
    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Get the OM who approved this VR
     */
    public function omApprover()
    {
        return $this->belongsTo(User::class, 'om_approved_by');
    }

    /**
     * Get the GM who approved this VR
     */
    public function gmApprover()
    {
        return $this->belongsTo(User::class, 'gm_approved_by');
    }

    /**
     * Get all attachments (photos) for this VR
     */
    public function attachments()
    {
        return $this->hasMany(VoucherAttachment::class, 'voucher_request_id');
    }

    /**
     * Get all items in this VR (DEPRECATED - will be removed)
     * @deprecated VR now uses photos instead of items
     */
    public function items()
    {
        return $this->hasMany(VoucherItem::class, 'voucher_request_id');
    }

    /**
     * Get signatures related to this VR (through ticket, filtered by stage)
     */
    public function signatures()
    {
        return $this->hasManyThrough(
            Signature::class,
            Ticket::class,
            'id', // Foreign key on tickets table
            'ticket_id', // Foreign key on signatures table
            'ticket_id', // Local key on voucher_requests table
            'id' // Local key on tickets table
        )->whereIn('stage', [6, 7, 8]); // VR stages only
    }

    // ==================== HELPER METHODS ====================

    /**
     * Calculate total amount from items (DEPRECATED - returns 0)
     * @deprecated VR now uses photos, not items
     */
    public function calculateTotal()
    {
        // VR sekarang berbasis foto, tidak perlu total_amount
        return 0;
    }

    /**
     * Get formatted total (DEPRECATED)
     * @deprecated VR now uses photos
     */
    public function getFormattedTotal()
    {
        return 'N/A (Photo-based VR)';
    }

    /**
     * Get next approval stage
     */
    public function getNextApprovalStage()
    {
        if (!$this->admin_approved) {
            return 'admin';
        } elseif (!$this->om_approved) {
            return 'om';
        } elseif (!$this->gm_approved) {
            return 'gm';
        } else {
            return 'completed';
        }
    }

    /**
     * Check if VR can be edited
     */
    public function canEdit()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Check if VR can be deleted
     */
    public function canDelete()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Generate VR number automatically
     * Format: VR-YYYYMMDD-XXXX
     */
    public static function generateVrNumber()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $prefix = 'VR-' . $year . $month . $day;

        $lastVR = self::where('vr_number', 'like', $prefix . '-%')
            ->orderBy('vr_number', 'desc')
            ->first();

        if ($lastVR) {
            $lastNumber = intval(substr($lastVR->vr_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . '-' . $nextNumber;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => 'warning',
            'admin_approved' => 'info',
            'om_approved' => 'primary',
            'gm_approved' => 'success',
            'paid' => 'success',
            'rejected' => 'danger',
        ];

        $color = $badges[$this->status] ?? 'secondary';
        $label = str_replace('_', ' ', ucfirst($this->status));

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get photo count
     */
    public function getPhotoCountAttribute()
    {
        return $this->attachments()->count();
    }

    /**
     * Get first photo for thumbnail
     */
    public function getThumbnailAttribute()
    {
        $firstPhoto = $this->attachments()->first();
        return $firstPhoto ? $firstPhoto->url : null;
    }

    /**
     * Scope for pending approval based on user role
     */
    public function scopePendingApproval($query, $userRole)
    {
        switch ($userRole) {
            case 'admin_eng':
                return $query->where('status', 'pending');
            case 'om':
                return $query->where('status', 'admin_approved');
            case 'gm':
                return $query->where('status', 'om_approved');
            default:
                return $query;
        }
    }

    /**
     * Get approval timeline array
     */
    public function getApprovalTimeline()
    {
        $timeline = [];

        // Admin Eng approval
        if ($this->admin_approved_at) {
            $timeline[] = [
                'stage' => 'Admin Engineering',
                'status' => 'approved',
                'by' => $this->adminApprover->name ?? 'Unknown',
                'at' => $this->admin_approved_at->format('d M Y, H:i'),
            ];
        } else {
            $timeline[] = [
                'stage' => 'Admin Engineering',
                'status' => 'pending',
                'by' => null,
                'at' => null,
            ];
        }

        // OM approval
        if ($this->om_approved_at) {
            $timeline[] = [
                'stage' => 'Operation Manager',
                'status' => 'approved',
                'by' => $this->omApprover->name ?? 'Unknown',
                'at' => $this->om_approved_at->format('d M Y, H:i'),
            ];
        } else {
            $timeline[] = [
                'stage' => 'Operation Manager',
                'status' => 'pending',
                'by' => null,
                'at' => null,
            ];
        }

        // GM approval
        if ($this->gm_approved_at) {
            $timeline[] = [
                'stage' => 'General Manager',
                'status' => 'approved',
                'by' => $this->gmApprover->name ?? 'Unknown',
                'at' => $this->gm_approved_at->format('d M Y, H:i'),
            ];
        } else {
            $timeline[] = [
                'stage' => 'General Manager',
                'status' => 'pending',
                'by' => null,
                'at' => null,
            ];
        }

        return $timeline;
    }

    /**
     * Check if VR is fully approved
     */
    public function isFullyApproved()
    {
        return $this->admin_approved && $this->om_approved && $this->gm_approved;
    }

    /**
     * Check if VR is pending payment
     */
    public function isPendingPayment()
    {
        return $this->status === 'gm_approved';
    }

    /**
     * Check if VR is completed (paid)
     */
    public function isCompleted()
    {
        return $this->status === 'paid';
    }

    /**
     * Get current approver role
     */
    public function getCurrentApproverRole()
    {
        if (!$this->admin_approved) {
            return 'admin_eng';
        } elseif (!$this->om_approved) {
            return 'om';
        } elseif (!$this->gm_approved) {
            return 'gm';
        }
        return null;
    }

    /**
     * Check if user can approve this VR
     */
    public function canApprove($user)
    {
        $role = $user->role;

        if ($role === 'admin_eng' && $this->status === 'pending') {
            return true;
        }
        if ($role === 'om' && $this->status === 'admin_approved') {
            return true;
        }
        if ($role === 'gm' && $this->status === 'om_approved') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can reject this VR
     */
    public function canReject($user)
    {
        $role = $user->role;

        if ($role === 'admin_eng' && $this->status === 'pending') {
            return true;
        }
        if ($role === 'om' && $this->status === 'admin_approved') {
            return true;
        }
        if ($role === 'gm' && $this->status === 'om_approved') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can mark as paid
     */
    public function canMarkPaid($user)
    {
        return in_array($user->role, ['admin_eng', 'superadmin']) && $this->status === 'gm_approved';
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate VR number if not set
        static::creating(function ($vr) {
            if (empty($vr->vr_number)) {
                $vr->vr_number = self::generateVrNumber();
            }
        });
    }
}
