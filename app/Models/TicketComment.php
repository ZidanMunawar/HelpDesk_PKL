<?php
// app/Models/TicketComment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
        'is_followup', 
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_followup' => 'boolean', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
            'ticket_id' => 'integer',
    'user_id' => 'integer',
    ];

    // Relationships

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(CommentAttachment::class, 'comment_id');
    }

    // Scopes

    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    // Scope untuk follow-up comments
    public function scopeFollowUp($query)
    {
        return $query->where('is_followup', true);
    }
}
