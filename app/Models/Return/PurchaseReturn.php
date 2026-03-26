<?php

namespace App\Models\Return;

use App\Models\Adjustment\StockMovement;
use App\Models\Parties\Supplier;
use App\Models\Purchases\Purchase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'return_date' => 'date',
        'total_return' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= Str::uuid();
            $model->reference_number ??= self::generateReferenceNumber($model->tenant_id);
            $model->user_id ??= auth()->id();
        });

        // FIX: Jangan panggil processReturn di 'created' — items belum tersimpan.
        // Panggil manual dari CreatePurchaseReturn::afterCreate() setelah items tersimpan.
        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_APPROVED) {
                $alreadyProcessed = StockMovement::where('reference_type', 'purchase_return')
                    ->where('reference_id', $model->id)
                    ->exists();

                if (!$alreadyProcessed) {
                    $model->processReturn();
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

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,  // model Tenant
            $this->getTable(),          // pakai tabel model itu sendiri sebagai "pivot"
            'id',                       // FK ke model ini di "pivot"
            'tenant_id',                // FK ke tenant di "pivot"
            'id',                       // PK model ini
            'id',                       // PK tenant
        );
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'PR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kurangi stok + catat movement saat return ke supplier diapprove.
     * Dipanggil manual dari CreatePurchaseReturn::afterCreate() atau saat status → approved.
     */
    public function processReturn(): void
    {
        // Guard: jangan proses jika sudah pernah
        $alreadyProcessed = StockMovement::where('reference_type', 'purchase_return')
            ->where('reference_id', $this->id)
            ->exists();

        if ($alreadyProcessed) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                if (!$product->track_stock) {
                    continue;
                }

                $stockBefore = $product->stock;
                $product->decrement('stock', $item->qty);

                StockMovement::create([
                    'tenant_id' => $this->tenant_id,
                    'product_id' => $item->product_id,
                    'user_id' => $this->user_id,
                    'reference_type' => 'purchase_return',
                    'reference_id' => $this->id,
                    'type' => StockMovement::TYPE_OUT,
                    'qty' => $item->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $item->qty,
                    'notes' => 'Retur Pembelian #' . $this->reference_number,
                ]);
            }

            // Kurangi hutang ke supplier jika return method = debit_note
            if ($this->supplier_id && $this->return_method === 'debit_note') {
                if (method_exists($this->supplier, 'decrementPayable')) {
                    $this->supplier->decrementPayable($this->total_return);
                }
            }
        });
    }

    /**
     * Recalculate total_return dari items
     */
    public function recalculate(): void
    {
        $this->total_return = $this->items->sum('subtotal');
        $this->save();
    }
}
