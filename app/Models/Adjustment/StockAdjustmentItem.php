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
            // Auto-hitung qty_difference dan type dari stock_before & stock_after
            $diff = $item->stock_after - $item->stock_before;
            $item->qty_difference = $diff;
            $item->type = match (true) {
                $diff > 0 => 'add',
                $diff < 0 => 'subtract',
                default   => 'set',
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

    // ─── Helpers ──────────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'add'      => '+ Penambahan',
            'subtract' => '- Pengurangan',
            default    => '= Set Langsung',
        };
    }
}
