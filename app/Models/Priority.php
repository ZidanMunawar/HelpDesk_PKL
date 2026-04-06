<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'level',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Level labels mapping
     */
    const LEVEL_LABELS = [
        1 => 'Lowest',
        2 => 'Low',
        3 => 'Medium',
        4 => 'High',
        5 => 'Highest',
    ];

    /**
     * Level colors mapping
     */
    const LEVEL_COLORS = [
        1 => '#28a745', // Green
        2 => '#17a2b8', // Blue
        3 => '#ffc107', // Yellow
        4 => '#fd7e14', // Orange
        5 => '#dc3545', // Red
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

    /**
     * Get level label
     */
    public function getLevelLabelAttribute()
    {
        return self::LEVEL_LABELS[$this->level] ?? 'Unknown';
    }

    /**
     * Get level badge color
     */
    public function getLevelColorAttribute()
    {
        return self::LEVEL_COLORS[$this->level] ?? '#6c757d';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
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
     * Count tickets using this priority
     */
    public function ticketsCount()
    {
        return $this->tickets()->count();
    }

    /**
     * Check if priority can be deleted
     */
    public function canBeDeleted()
    {
        return $this->tickets()->count() === 0;
    }

    /**
     * Get badge HTML
     */
    public function getBadgeHtmlAttribute()
    {
        return '<span class="badge" style="background-color: ' . $this->color . '">' . $this->name . '</span>';
    }

    /**
     * Get level badge HTML
     */
    public function getLevelBadgeHtmlAttribute()
    {
        return '<span class="badge" style="background-color: ' . $this->level_color . '">Level ' . $this->level . ': ' . $this->level_label . '</span>';
    }
}
