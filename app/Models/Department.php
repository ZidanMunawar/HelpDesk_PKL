<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
     * Count active users in department
     */
    public function activeUsersCount()
    {
        return $this->users()->where('status', 'active')->count();
    }
}
