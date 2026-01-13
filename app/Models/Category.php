<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'status',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Relationships

    /**
     * Tickets in this category
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Department relationship
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Count active tickets
     */
    public function activeTicketsCount()
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled'])->count();
    }
}
