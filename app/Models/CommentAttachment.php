<?php
// app/Models/CommentAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        // HAPUS 'updated_at' jika tidak ada di database
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Berdasarkan dump SQL, hanya `created_at` yang ada

    // Relationships

    public function comment()
    {
        return $this->belongsTo(TicketComment::class, 'comment_id');
    }

    // Helper Methods

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        $size = $this->file_size;

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return round($size / 1024, 2) . ' KB';
        }

        return $size . ' B';
    }
}
