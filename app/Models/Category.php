<?php
// app/Models/Category.php

namespace App\Models;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'status', // Hapus 'department_id' dari sini
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
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive categories
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Hapus scope byDepartment karena sudah tidak ada department_id

    // Relationships

    /**
     * Tickets in this category
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Hapus department relationship

    /**
     * Count active tickets
     */
    public function activeTicketsCount()
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled'])->count();
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

    // Hapus getDepartmentNameAttribute

    /**
     * Get truncated description
     */
    public function getTruncatedDescriptionAttribute()
    {
        return $this->description ? Str::limit($this->description, 50) : '-';
    }

    /**
     * Check if category can be deleted
     */
    public function canBeDeleted()
    {
        return $this->tickets()->count() === 0;
    }

    /**
     * Get category usage info
     */
    public function getUsageInfoAttribute()
    {
        $totalTickets = $this->tickets()->count();
        $activeTickets = $this->activeTicketsCount();

        return "Total: {$totalTickets} tickets, Active: {$activeTickets} tickets";
    }
}
