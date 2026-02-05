<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vr_number',
        'ticket_id',
        'total_amount',
        'status',
        'notes',
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
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'om_approved' => 'boolean',
        'gm_approved' => 'boolean',
        'total_amount' => 'decimal:2',
        'admin_approved_at' => 'datetime',
        'om_approved_at' => 'datetime',
        'gm_approved_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }



    // ============================================
    // NEW METHODS - TAMBAHKAN INI
    // ============================================

    /**
     * Calculate total amount from items
     */
    public function calculateTotal()
    {
        $total = $this->items->sum(function ($item) {
            return $item->qty * $item->unit_price;
        });

        // Update the total amount in the database
        $this->update(['total_amount' => $total]);

        return $total;
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
     */
    public static function generateVrNumber()
    {
        $prefix = 'VR-' . date('Ymd');

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
     * Get approval status badge
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

        return '<span class="badge bg-' . $color . '">' . str_replace('_', ' ', $this->status) . '</span>';
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotal()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Scope for pending approval
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
     * Get approval timeline
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
    // App\Models\VoucherRequest
    public function omApprover()
    {
        return $this->belongsTo(User::class, 'om_approved_by');
    }

    public function gmApprover()
    {
        return $this->belongsTo(User::class, 'gm_approved_by');
    }



    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }
}
