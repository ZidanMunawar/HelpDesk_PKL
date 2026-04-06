<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VoucherAttachment extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Kita hanya pakai created_at, updated_at tidak perlu
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'voucher_request_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'uploaded_by',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'url',
        'formatted_size',
        'is_image',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the voucher request that owns this attachment.
     */
    public function voucherRequest(): BelongsTo
    {
        return $this->belongsTo(VoucherRequest::class);
    }

    /**
     * Get the user who uploaded this attachment.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the full URL of the attachment.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size (e.g., "1.5 MB").
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return $this->file_type && strpos($this->file_type, 'image/') === 0;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }

        return false;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set created_at when creating
        static::creating(function ($attachment) {
            $attachment->created_at = $attachment->created_at ?? now();
        });

        // Delete file when model is deleted
        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}
