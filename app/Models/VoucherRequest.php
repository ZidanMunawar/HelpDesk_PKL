<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vr_number',
        'ticket_id',
        'total_amount',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method for auto-generating VR number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vr) {
            if (!$vr->vr_number) {
                $vr->vr_number = self::generateVRNumber();
            }
        });
    }

    /**
     * Generate unique VR number
     */
    public static function generateVRNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastVR = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastVR ? intval(substr($lastVR->vr_number, -4)) + 1 : 1;

        return 'VR-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Ticket related to this VR
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User who created this VR
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved this VR
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Items in this VR
     */
    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope for pending VRs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved VRs
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected VRs
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for paid VRs
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if VR is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if VR is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if VR is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Calculate total amount from items
     */
    public function calculateTotal()
    {
        $total = $this->items->sum(function ($item) {
            return $item->qty * $item->unit_price;
        });

        $this->update(['total_amount' => $total]);

        return $total;
    }

    /**
     * Approve VR
     */
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject VR
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'paid' => 'success',
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
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}
