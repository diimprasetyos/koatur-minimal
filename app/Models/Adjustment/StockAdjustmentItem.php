<?php

namespace App\Models\Adjustment;

use App\Models\Product\Product;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\UnauthorizedException;

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
            // FIX: Validasi kepemilikan produk di server — cegah product_id dari tenant lain
            $tenantId = $item->stockAdjustment?->tenant_id
                ?? Filament::getTenant()?->id;

            $product = Product::where('id', $item->product_id)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$product) {
                throw new UnauthorizedException(
                    "Produk #{$item->product_id} tidak ditemukan atau bukan milik tenant ini."
                );
            }

            // FIX: stock_before selalu diambil dari DB — jangan percaya nilai dari form/client
            $item->stock_before = $product->stock;

            // Hitung qty_difference dan type dari nilai yang sudah divalidasi
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
