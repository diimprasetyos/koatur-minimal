<?php

namespace App\Models\Return;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleReturn extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'sale_id',
        'uuid',
        'reference_number',
        'return_date',
        'total_refund',
        'status',
        'refund_method',
        'reason',
        'notes',
    ];

    protected $casts = [
        'return_date'  => 'date',
        'total_refund' => 'decimal:2',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid             ??= Str::uuid();
            $model->reference_number ??= self::generateReferenceNumber($model->tenant_id);
        });

        // Otomatis restock saat return diapprove
        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_APPROVED) {
                $model->processReturn();
            }
        });

        // Jika langsung approved saat create
        static::created(function (self $model) {
            if ($model->status === self::STATUS_APPROVED) {
                $model->processReturn();
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
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date  = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'SR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Otomatis restock produk yang diretur
     */
    public function processReturn(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product     = $item->product;
                $stockBefore = $product->stock;

                $product->increment('stock', $item->qty);

                StockMovement::recordSaleReturn($item, $stockBefore);
            }
        });
    }
}
