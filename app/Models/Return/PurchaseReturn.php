<?php

namespace App\Models\Return;

use App\Models\Adjustment\StockMovement;
use App\Models\Parties\Supplier;
use App\Models\Purchases\Purchase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'purchase_id',
        'supplier_id',
        'uuid',
        'reference_number',
        'return_date',
        'total_return',
        'status',
        'return_method',
        'reason',
        'notes',
    ];

    protected $casts = [
        'return_date'  => 'date',
        'total_return' => 'decimal:2',
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

        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_APPROVED) {
                $model->processReturn();
            }
        });

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
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date  = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'PR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kurangi stok + catat movement saat return ke supplier diapprove
     */
    public function processReturn(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product     = $item->product;
                $stockBefore = $product->stock;

                $product->decrement('stock', $item->qty);

                StockMovement::recordPurchaseReturn($item, $stockBefore);
            }

            // Kurangi hutang ke supplier jika return method = debit_note
            if ($this->supplier_id && $this->return_method === 'debit_note') {
                $this->supplier->decrementPayable($this->total_return);
            }
        });
    }
}
