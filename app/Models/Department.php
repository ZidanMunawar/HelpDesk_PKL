<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'manager_id',
        'description',
        'status',
        'has_manager_access', // <-- TAMBAHKAN INI
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'has_manager_access' => 'boolean', // <-- TAMBAHKAN INI (cast ke boolean)
    ];

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive departments
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for departments that have manager access
     */
    public function scopeHasManagerAccess($query)
    {
        return $query->where('has_manager_access', true);
    }

    // Relationships

    /**
     * Manager of this department
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Users in this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Active users in department
     */
    public function activeUsers()
    {
        return $this->users()->where('status', 'active');
    }

    /**
     * Count active users in department
     */
    public function activeUsersCount()
    {
        return $this->activeUsers()->count();
    }

    /**
     * Get profile picture URL for manager
     */
    public function getManagerProfilePictureUrlAttribute()
    {
        if ($this->manager && $this->manager->profile_picture) {
            return asset('storage/' . $this->manager->profile_picture);
        }
        return asset('assets/images/default-avatar.png');
    }

    /**
     * Check if department can be deleted
     */
    public function canBeDeleted()
    {
        return $this->users()->count() === 0;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="badge badge-success">Active</span>',
            'inactive' => '<span class="badge badge-danger">Inactive</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Get manager name or placeholder
     */
    public function getManagerNameAttribute()
    {
        return $this->manager ? $this->manager->name : '-';
    }

    /**
     * Get truncated description
     */
    public function getTruncatedDescriptionAttribute()
    {
        return $this->description ? Str::limit($this->description, 50) : '-';
    }

    /**
     * Check if department has manager access
     */
    public function getHasManagerAccessLabelAttribute()
    {
        return $this->has_manager_access ? 'Yes' : 'No';
    }

    /**
     * Get manager access badge HTML
     */
    public function getManagerAccessBadgeAttribute()
    {
        if ($this->has_manager_access) {
            return '<span class="badge badge-primary"><i class="fas fa-user-tie me-1"></i>Manager Access</span>';
        }
        return '<span class="badge badge-secondary"><i class="fas fa-user me-1"></i>Standard</span>';
    }
}
