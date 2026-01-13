<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'voucher_request_id',
        'item_name',
        'qty',
        'unit_price',
        'vendor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Voucher request that this item belongs to
     */
    public function voucherRequest()
    {
        return $this->belongsTo(VoucherRequest::class);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get subtotal (qty * unit_price)
     */
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->unit_price;
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Boot method - recalculate VR total when item saved/deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            $item->voucherRequest->calculateTotal();
        });

        static::deleted(function ($item) {
            $item->voucherRequest->calculateTotal();
        });
    }
}
