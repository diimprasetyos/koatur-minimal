<?php

namespace App\Models\Sales;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
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

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Hitung profit item ini (subtotal - (cost_price * qty))
     */
    public function getProfit(): float
    {
        return (float) $this->subtotal - ($this->cost_price * $this->qty);
    }

    // ─── Relations ───────────────────────────────────────────────

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
