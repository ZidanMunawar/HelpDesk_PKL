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
        'ticket_id' => 'integer',
        'created_by' => 'integer',
        'admin_approved_by' => 'integer',
        'om_approved_by' => 'integer',
        'gm_approved_by' => 'integer',


    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the ticket associated with this PR
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who created this PR
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin who approved this PR
     */
    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Get the OM who approved this PR
     */
    public function omApprover()
    {
        return $this->belongsTo(User::class, 'om_approved_by');
    }

    /**
     * Get the GM who approved this PR
     */
    public function gmApprover()
    {
        return $this->belongsTo(User::class, 'gm_approved_by');
    }

    /**
     * Get all attachments (photos) for this PR
     */
    public function attachments()
    {
        return $this->hasMany(VoucherAttachment::class, 'voucher_request_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate PR number automatically
     * Format: PR-YYYYMMDD-XX
     */
    public static function generateVrNumber()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $prefix = 'PR-' . $year . $month . $day;

        $lastPR = self::where('vr_number', 'like', $prefix . '-%')
            ->orderBy('vr_number', 'desc')
            ->first();

        if ($lastPR) {
            // Ubah dari -4 menjadi -2
            $lastNumber = intval(substr($lastPR->vr_number, -2));
            // Ubah parameter str_pad dari 4 menjadi 2
            $nextNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            // Ubah default dari '0001' menjadi '01'
            $nextNumber = '01';
        }

        return $prefix . '-' . $nextNumber;
    }

    /**
     * Get current approver role based on status
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
     * Check if PR is fully approved (all three approvals)
     */
    public function isFullyApproved()
    {
        return $this->admin_approved && $this->om_approved && $this->gm_approved;
    }

    /**
     * Check if PR is pending payment (GM approved but not paid)
     */
    public function isPendingPayment()
    {
        return $this->status === 'gm_approved';
    }

    /**
     * Check if PR is completed (paid)
     */
    public function isCompleted()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if PR can be edited
     */
    public function canEdit()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Check if PR can be deleted
     */
    public function canDelete()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Check if user can approve this PR
     */
    public function canApprove($user)
    {
        $role = $user->role;

        // Admin Engineering can approve pending PR
        if ($role === 'admin_eng' && $this->status === 'pending') {
            return true;
        }

        // OM can approve admin_approved PR
        if ($role === 'om' && $this->status === 'admin_approved') {
            return true;
        }

        // GM can approve om_approved PR
        if ($role === 'gm' && $this->status === 'om_approved') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can reject this PR
     */
    public function canReject($user)
    {
        $role = $user->role;

        // Admin Engineering can reject pending PR
        if ($role === 'admin_eng' && $this->status === 'pending') {
            return true;
        }

        // OM can reject admin_approved PR
        if ($role === 'om' && $this->status === 'admin_approved') {
            return true;
        }

        // GM can reject om_approved PR
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
        return $firstPhoto ? $firstPhoto->file_path : null;
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
     * Get status display name for frontend
     */
    public function getStatusDisplayAttribute()
    {
        $statusMap = [
            'pending' => 'Pending Admin',
            'admin_approved' => 'Admin Approved',
            'om_approved' => 'OM Approved',
            'gm_approved' => 'GM Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
        ];

        return $statusMap[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
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

        // Payment status
        if ($this->status === 'paid') {
            $timeline[] = [
                'stage' => 'Payment',
                'status' => 'completed',
                'by' => null,
                'at' => $this->updated_at->format('d M Y, H:i'),
            ];
        } elseif ($this->status === 'gm_approved') {
            $timeline[] = [
                'stage' => 'Payment',
                'status' => 'pending',
                'by' => null,
                'at' => null,
            ];
        }

        return $timeline;
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
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('vr_number', 'LIKE', "%{$search}%")
                ->orWhere('notes', 'LIKE', "%{$search}%")
                ->orWhereHas('ticket', function ($q2) use ($search) {
                    $q2->where('ticket_number', 'LIKE', "%{$search}%")
                        ->orWhere('title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('creator', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });
        });
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate PR number if not set
        static::creating(function ($pr) {
            if (empty($pr->vr_number)) {
                $pr->vr_number = self::generateVrNumber();
            }
        });
    }
}
