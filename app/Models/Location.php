<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'location_type',
        'floor_number',
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
     * Scope for active locations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for location type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('location_type', $type);
    }

    /**
     * Scope for rooms only
     */
    public function scopeRooms($query)
    {
        return $query->where('location_type', 'room');
    }

    /**
     * Scope for floors only
     */
    public function scopeFloors($query)
    {
        return $query->where('location_type', 'floor');
    }

    /**
     * Tickets at this location
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Active tickets count
     */
    public function activeTicketsCount()
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled'])->count();
    }

    /**
     * Get location type badge color
     */
    public function getTypeBadgeColorAttribute()
    {
        $colors = [
            'floor' => 'primary',
            'room' => 'info',
            'area' => 'warning',
            'facility' => 'success',
        ];

        return $colors[$this->location_type] ?? 'secondary';
    }
}
