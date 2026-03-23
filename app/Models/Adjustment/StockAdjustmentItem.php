<?php

namespace App\Models\Adjustment;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'stock_before',
        'stock_after',
        'qty_difference',
        'type',
        'notes',
    ];

    protected $casts = [
        'stock_before'   => 'integer',
        'stock_after'    => 'integer',
        'qty_difference' => 'integer',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->qty_difference = $item->stock_after - $item->stock_before;
            $item->type = match (true) {
                $item->qty_difference > 0 => 'add',
                $item->qty_difference < 0 => 'subtract',
                default                   => 'set',
            };
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
