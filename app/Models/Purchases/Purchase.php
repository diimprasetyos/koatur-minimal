<?php

namespace App\Models\Purchases;

use App\Models\Adjustment\StockMovement;
use App\Models\Parties\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Purchase extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'supplier_id',
        'uuid',
        'reference_number',
        'supplier_invoice',
        'purchase_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid',
        'due',
        'status',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'due_date'       => 'date',
        'subtotal'       => 'decimal:2',
        'discount'       => 'decimal:2',
        'tax'            => 'decimal:2',
        'total'          => 'decimal:2',
        'paid'           => 'decimal:2',
        'due'            => 'decimal:2',
    ];

    const STATUS_DRAFT      = 'draft';
    const STATUS_ORDERED    = 'ordered';
    const STATUS_RECEIVED   = 'received';
    const STATUS_PARTIAL    = 'partial';
    const STATUS_CANCELLED  = 'cancelled';

    const PAYMENT_UNPAID  = 'unpaid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID    = 'paid';

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid             ??= Str::uuid();
            $model->reference_number ??= self::generateReferenceNumber($model->tenant_id);
        });

        // Saat purchase di-receive: update stok + cost_price produk
        static::updated(function (self $model) {
            if ($model->wasChanged('status') && $model->status === self::STATUS_RECEIVED) {
                $model->receiveStock();
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(\App\Models\Return\PurchaseReturn::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateReferenceNumber(int $tenantId): string
    {
        $date  = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'PO-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Receive stock: update product stock + cost_price, catat stock movement
     */
    public function receiveStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                $stockBefore = $product->stock;
                $product->increment('stock', $item->qty);
                // Update cost_price produk dengan harga beli terbaru
                $product->update(['cost_price' => $item->cost_price]);

                StockMovement::create([
                    'tenant_id'      => $this->tenant_id,
                    'product_id'     => $item->product_id,
                    'user_id'        => $this->user_id,
                    'reference_type' => 'purchase',
                    'reference_id'   => $this->id,
                    'type'           => StockMovement::TYPE_IN,
                    'qty'            => $item->qty,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockBefore + $item->qty,
                    'notes'          => 'Auto: Pembelian #' . $this->reference_number,
                ]);
            }

            // Update hutang ke supplier
            if ($this->supplier_id && $this->due > 0) {
                $this->supplier->incrementPayable($this->due);
            }
        });
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total    = $this->subtotal - $this->discount + $this->tax;
        $this->due      = $this->total - $this->paid;

        $this->payment_status = match (true) {
            $this->due <= 0          => self::PAYMENT_PAID,
            $this->paid > 0          => self::PAYMENT_PARTIAL,
            default                  => self::PAYMENT_UNPAID,
        };

        $this->save();
    }
}
