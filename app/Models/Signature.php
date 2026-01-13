<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'signature_type',
        'signature_path',
        'signed_at',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Ticket that this signature belongs to
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User who signed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope for reporter signatures
     */
    public function scopeReporter($query)
    {
        return $query->where('signature_type', 'reporter');
    }

    /**
     * Scope for technician signatures
     */
    public function scopeTechnician($query)
    {
        return $query->where('signature_type', 'technician');
    }

    /**
     * Scope for approver signatures
     */
    public function scopeApprover($query)
    {
        return $query->where('signature_type', 'approver');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if signature is from reporter
     */
    public function isReporter(): bool
    {
        return $this->signature_type === 'reporter';
    }

    /**
     * Check if signature is from technician
     */
    public function isTechnician(): bool
    {
        return $this->signature_type === 'technician';
    }

    /**
     * Check if signature is from approver
     */
    public function isApprover(): bool
    {
        return $this->signature_type === 'approver';
    }

    /**
     * Get signature URL
     */
    public function getSignatureUrlAttribute()
    {
        return asset('storage/' . $this->signature_path);
    }

    /**
     * Get full signature path for storage
     */
    public static function getStoragePath($ticketId, $signatureType, $userId)
    {
        return "signatures/ticket_{$ticketId}_{$signatureType}_{$userId}_" . time() . ".png";
    }

    /**
     * Get signature type badge
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'reporter' => '<span class="badge badge-info">Reporter</span>',
            'technician' => '<span class="badge badge-success">Technician</span>',
            'approver' => '<span class="badge badge-warning">Approver</span>',
        ];

        return $badges[$this->signature_type] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Get signature type icon
     */
    public function getTypeIconAttribute()
    {
        $icons = [
            'reporter' => 'fa-user text-info',
            'technician' => 'fa-wrench text-success',
            'approver' => 'fa-check-circle text-warning',
        ];

        return $icons[$this->signature_type] ?? 'fa-file text-secondary';
    }

    /**
     * Save signature from base64 data
     */
    public static function saveFromBase64($ticketId, $userId, $signatureType, $base64Data, $ipAddress = null)
    {
        // Remove data:image/png;base64, prefix
        $imageData = str_replace('data:image/png;base64,', '', $base64Data);
        $imageData = str_replace(' ', '+', $imageData);

        // Generate path
        $path = self::getStoragePath($ticketId, $signatureType, $userId);

        // Save to storage
        \Storage::disk('public')->put($path, base64_decode($imageData));

        // Create signature record
        return self::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'signature_type' => $signatureType,
            'signature_path' => $path,
            'signed_at' => now(),
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Delete signature file from storage
     */
    public function deleteFile()
    {
        if (\Storage::disk('public')->exists($this->signature_path)) {
            \Storage::disk('public')->delete($this->signature_path);
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto delete file when signature record deleted
        static::deleting(function ($signature) {
            $signature->deleteFile();
        });
    }
}
