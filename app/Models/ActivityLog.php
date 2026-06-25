<?php
// app/Models/ActivityLog.php

namespace App\Models;

use App\Models\Ticket;
use App\Models\User;
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
        // HAPUS 'updated_at' karena tidak ada di database
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; // Masih true karena ada created_at

    /**
     * Get the name of the "created at" column.
     * Ini override untuk hanya menggunakan created_at
     *
     * @return string|null
     */
    public function getCreatedAtColumn()
    {
        return 'created_at';
    }

    /**
     * Get the name of the "updated at" column.
     * Return null karena tidak ada updated_at di database
     *
     * @return string|null
     */
    public function getUpdatedAtColumn()
    {
        return null; // Tidak ada updated_at
    }

    /**
     * Set the value of the "updated at" attribute.
     * Override untuk mencegah setting updated_at
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        // Do nothing - tidak ada updated_at
        return $this;
    }

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
            'login' => '<span class="badge badge-success">Login</span>',
            'logout' => '<span class="badge badge-info">Logout</span>',
            'login_failed' => '<span class="badge badge-succes">Login Failed</span>',
            'user_registered' => '<span class="badge badge-primary">User Registered</span>',
            'user_deleted' => '<span class="badge badge-danger">User Deleted</span>',
            'user_reset' => '<span class="badge badge-warning">User Reset</span>',
            'password_reset_requested' => '<span class="badge badge-info">Password Reset Requested</span>',
            'password_reset' => '<span class="badge badge-success">Password Reset</span>',
            'password_reset_failed' => '<span class="badge badge-danger">Password Reset Failed</span>',
            'broadcast' => '<span class="badge badge-primary">Broadcast</span>',
        ];

        return $badges[$this->action] ?? '<span class="badge badge-secondary">' . ucfirst($this->action) . '</span>';
    }
}
