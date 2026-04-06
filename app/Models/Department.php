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
}
