<?php

namespace App\Models\Adjustment;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockAdjustment extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'uuid',
        'reference_number',
        'adjustment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_CONFIRMED = 'confirmed';

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid             ??= Str::uuid();
            $model->reference_number ??= self::generateReferenceNumber($model->tenant_id);
        });

        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_CONFIRMED) {
                $model->applyAdjustment();
            }
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
    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date  = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'ADJ-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Apply adjustment ke stok produk + catat movement
     */
    public function applyAdjustment(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product     = $item->product;
                $stockBefore = $product->stock;

                $product->update(['stock' => $item->stock_after]);

                StockMovement::create([
                    'tenant_id'      => $this->tenant_id,
                    'product_id'     => $item->product_id,
                    'user_id'        => $this->user_id,
                    'reference_type' => StockMovement::REF_ADJUSTMENT,
                    'reference_id'   => $this->id,
                    'type'           => $item->qty_difference >= 0
                        ? StockMovement::TYPE_IN
                        : StockMovement::TYPE_OUT,
                    'qty'            => abs($item->qty_difference),
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $item->stock_after,
                    'notes'          => 'Auto: Penyesuaian Stok #' . $this->reference_number,
                ]);
            }
        });
    }
}
