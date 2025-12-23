<?php
// app/Models/TicketEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'all_day',
        'color',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'all_day' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Methods

    public function getFormattedDateAttribute()
    {
        if ($this->all_day) {
            return $this->start_date->format('d M Y');
        }

        return $this->start_date->format('d M Y H:i') . ' - ' .
            ($this->end_date ? $this->end_date->format('H:i') : '');
    }
}
