<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Count active tickets
     */
    public function activeTicketsCount()
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled'])->count();
    }
}
