<?php

namespace App\Models\Sales;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'price',
        'cost_price',
        'qty',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount'   => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'qty'        => 'integer',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            // Auto hitung subtotal
            $item->subtotal = ($item->price - $item->discount) * $item->qty;
        });

        static::saved(function (self $item) {
            // Trigger recalculate parent sale
            $item->sale?->recalculate();
        });

        static::deleted(function (self $item) {
            // Trigger recalculate parent sale
            $item->sale?->recalculate();
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Hitung profit item ini
     */
    public function getProfit(): float
    {
        return (float) $this->subtotal - ($this->cost_price * $this->qty);
    }

    /**
     * Hitung margin profit (%)
     */
    public function getProfitMargin(): float
    {
        if ($this->subtotal <= 0) {
            return 0;
        }

        return ($this->getProfit() / $this->subtotal) * 100;
    }
}
