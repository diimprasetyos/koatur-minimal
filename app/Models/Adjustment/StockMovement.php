<?php

namespace App\Models\Adjustment;

use App\Models\Product\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    /**
     * Tabel stock_movements hanya punya created_at, TIDAK ada updated_at.
     */
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    const TYPE_IN  = 'in';
    const TYPE_OUT = 'out';

    // FIX: tambahkan konstanta REF_ADJUSTMENT yang dipakai StockAdjustment
    const REF_SALE             = 'sale';
    const REF_PURCHASE         = 'purchase';
    const REF_SALE_RETURN      = 'sale_return';
    const REF_PURCHASE_RETURN  = 'purchase_return';
    const REF_ADJUSTMENT       = 'adjustment';
    const REF_SALE_CANCELLED   = 'sale_cancelled';

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
        'created_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->created_at = now();
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
