<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherItem extends Model
{
    use HasFactory;

    protected $table = 'voucher_items';

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
        'description',
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
     * Get item summary for display
     */
    public function getSummaryAttribute()
    {
        return $this->item_name . ' (' . $this->qty . ' x ' . $this->formatted_unit_price . ')';
    }

    /**
     * Boot method - recalculate VR total when item saved/deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            if ($item->voucherRequest) {
                $item->voucherRequest->calculateTotal();
            }
        });

        static::deleted(function ($item) {
            if ($item->voucherRequest) {
                $item->voucherRequest->calculateTotal();
            }
        });
    }
}
