<?php
// app/Models/Priority.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'level',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active priorities
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordering by level
     */
    public function scopeOrderByLevel($query)
    {
        return $query->orderBy('level', 'asc');
    }

    // Relationships

    /**
     * Tickets with this priority
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get badge HTML
     */
    public function getBadgeHtmlAttribute()
    {
        return '<span class="badge" style="background-color: ' . $this->color . '">' . $this->name . '</span>';
    }
}
