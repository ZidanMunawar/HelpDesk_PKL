<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vr_number',
        'ticket_id',
        'total_amount',
        'status',
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
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'om_approved' => 'boolean',
        'gm_approved' => 'boolean',
        'total_amount' => 'decimal:2',
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
        return $this->hasMany(VoucherItem::class, 'voucher_request_id');
    }

    /**
     * Check if VR can be edited
     */
    public function canEdit()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Get next approval stage
     */
    public function getNextApprovalStage()
    {
        if ($this->status === 'pending')
            return 'admin';
        if ($this->status === 'admin_approved')
            return 'om';
        if ($this->status === 'om_approved')
            return 'gm';
        return null;
    }
}
