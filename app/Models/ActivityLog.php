<?php
// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Helper Methods

    public function getActionBadgeAttribute()
    {
        $badges = [
            'created' => '<span class="badge badge-success">Created</span>',
            'updated' => '<span class="badge badge-info">Updated</span>',
            'deleted' => '<span class="badge badge-danger">Deleted</span>',
            'status_changed' => '<span class="badge badge-warning">Status Changed</span>',
            'assigned' => '<span class="badge badge-primary">Assigned</span>',
            'commented' => '<span class="badge badge-secondary">Commented</span>',
        ];

        return $badges[$this->action] ?? '<span class="badge badge-secondary">' . ucfirst($this->action) . '</span>';
    }
}
