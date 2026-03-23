<?php

namespace App\Models\Purchases;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'qty',
        'qty_received',
        'cost_price',
        'subtotal',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'qty_received' => 'integer',
        'cost_price'   => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->subtotal = $item->qty * $item->cost_price;
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
