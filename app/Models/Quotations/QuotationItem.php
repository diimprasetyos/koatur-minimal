<?php

namespace App\Models\Quotations;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // ─── Boot ────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            // Auto-compute subtotal + snapshot nama produk
            $item->subtotal = $item->price * $item->quantity;

            if (empty($item->product_name) && $item->product_id) {
                $item->product_name = Product::find($item->product_id)?->name ?? '';
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
