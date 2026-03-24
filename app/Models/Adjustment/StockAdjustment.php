<?php

namespace App\Models\Adjustment;

use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
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
            $model->tenant_id        ??= Filament::getTenant()?->id;
            $model->user_id          ??= auth()->id();
            $model->adjustment_date  ??= now()->toDateString();
        });

        // FIX: applyAdjustment dipanggil di 'updated' (status draft→confirmed)
        // JANGAN panggil di 'created' — items belum tersimpan saat itu.
        // Untuk create langsung confirmed, panggil dari CreateStockAdjustment::afterCreate()
        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_CONFIRMED) {
                // Guard: jangan apply 2x
                $alreadyApplied = StockMovement::where('reference_type', StockMovement::REF_ADJUSTMENT)
                    ->where('reference_id', $model->id)
                    ->exists();

                if (!$alreadyApplied) {
                    $model->load('items.product');
                    $model->applyAdjustment();
                }
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
     * Terapkan penyesuaian stok ke semua item + catat StockMovement.
     * Dipanggil dari CreateStockAdjustment::afterCreate() atau saat status → confirmed.
     */
    public function applyAdjustment(): void
    {
        // FIX: StockMovement::REF_ADJUSTMENT harus didefinisikan di StockMovement
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                if (!$product || !$product->track_stock) {
                    continue;
                }

                // stock_before diambil dari stok AKTUAL produk saat apply,
                // bukan nilai yang diinput user (karena bisa berubah sejak form diisi)
                $stockBefore = $product->stock;
                $product->update(['stock' => $item->stock_after]);

                // qty_difference dihitung ulang dari stok aktual, bukan dari input
                $qtyDiff = $item->stock_after - $stockBefore;

                if ($qtyDiff === 0) {
                    continue; // tidak perlu catat movement jika tidak ada perubahan
                }

                StockMovement::create([
                    'tenant_id'      => $this->tenant_id,
                    'product_id'     => $item->product_id,
                    'user_id'        => $this->user_id,
                    'reference_type' => StockMovement::REF_ADJUSTMENT,
                    'reference_id'   => $this->id,
                    'type'           => $qtyDiff > 0 ? StockMovement::TYPE_IN : StockMovement::TYPE_OUT,
                    'qty'            => abs($qtyDiff),
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $item->stock_after,
                    'notes'          => ($item->notes ?: 'Penyesuaian Stok #' . $this->reference_number),
                ]);

                // Update qty_difference di item agar sinkron dengan yang benar-benar diterapkan
                $item->updateQuietly([
                    'stock_before'   => $stockBefore,
                    'qty_difference' => $qtyDiff,
                    'type'           => match (true) {
                        $qtyDiff > 0 => 'add',
                        $qtyDiff < 0 => 'subtract',
                        default      => 'set',
                    },
                ]);
            }
        });
    }

    public function scopeForCurrentTenant($query)
    {
        if ($tenantId = Filament::getTenant()?->id) {
            return $query->where('tenant_id', $tenantId);
        }
        return $query;
    }
}
