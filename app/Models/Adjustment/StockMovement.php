<?php

namespace App\Models\Adjustment;

use App\Models\Product\Product;
use App\Models\Sales\SaleItem;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'user_id',
        'reference_type',
        'reference_id',
        'type',
        'qty',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'stock_before' => 'integer',
        'stock_after'  => 'integer',
    ];

    // Type constants
    const TYPE_IN  = 'in';   // stok masuk (restock, adjustment +)
    const TYPE_OUT = 'out';  // stok keluar (sale, adjustment -)

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Buat movement otomatis dari transaksi penjualan
     */
    public static function recordSale(SaleItem $item, int $stockBefore): void
    {
        self::create([
            'tenant_id'      => $item->sale->tenant_id,
            'product_id'     => $item->product_id,
            'user_id'        => $item->sale->user_id,
            'reference_type' => 'sale',
            'reference_id'   => $item->sale_id,
            'type'           => self::TYPE_OUT,
            'qty'            => $item->qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockBefore - $item->qty,
            'notes'          => 'Auto: Penjualan #' . $item->sale->invoice_number,
        ]);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeIn($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    public function scopeOut($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    // ─── Relations ───────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
