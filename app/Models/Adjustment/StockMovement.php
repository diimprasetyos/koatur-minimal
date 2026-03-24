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
     * Matikan auto-timestamp Laravel, lalu set CREATED_AT manual.
     */
    public $timestamps = false;

    const CREATED_AT = 'created_at'; // kolom ini ada di tabel
    const UPDATED_AT = null;         // kolom ini TIDAK ada, jangan di-set

    const TYPE_IN  = 'in';
    const TYPE_OUT = 'out';

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
            // Set created_at secara manual karena $timestamps = false
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
