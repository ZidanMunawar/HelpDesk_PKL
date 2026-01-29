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
        'hotel',
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
     * Scope for hotel filter
     */
    public function scopeOfHotel($query, $hotel)
    {
        return $query->where('hotel', $hotel);
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
            'area' => 'secondary',
            'floor' => 'info',
            'room' => 'success',
            'facility' => 'warning',
            'department' => 'primary',
        ];

        return $colors[$this->location_type] ?? 'dark';
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

    /**
     * Get floor display name
     */
    public function getFloorDisplayAttribute()
    {
        if (!$this->floor_number) {
            return '-';
        }

        $floorNames = [
            'GF' => 'Ground Floor',
            'M' => 'Mezzanine',
            '3A' => '3A Floor',
            '4' => '4th Floor',
            '5' => '5th Floor',
            '6' => '6th Floor',
            '7' => '7th Floor',
            '8' => '8th Floor',
            '9' => '9th Floor',
        ];

        return $floorNames[$this->floor_number] ?? 'Floor ' . $this->floor_number;
    }

    /**
     * Get hotel display name
     */
    public function getHotelDisplayAttribute()
    {
        return $this->hotel === 'harris' ? 'Harris Hotel' : 'Pop Hotel';
    }

    /**
     * Get hotel badge color
     */
    public function getHotelBadgeColorAttribute()
    {
        return $this->hotel === 'harris' ? 'primary' : 'success';
    }
}
