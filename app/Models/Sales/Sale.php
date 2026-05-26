<?php

namespace App\Models\Sales;

use App\Models\Adjustment\StockMovement;
use App\Models\Parties\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Filament\Facades\Filament;

class Sale extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'customer_id',
        'uuid',
        'invoice_number',
        'sale_date',
        'payment_method',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid',
        'change',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_CASH = 'cash';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_EWALLET = 'ewallet';

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= Str::uuid();
            $model->invoice_number ??= self::generateInvoiceNumber($model->tenant_id);
            $model->tenant_id ??= Filament::getTenant()?->id;
            $model->user_id ??= auth()->id();
            $model->sale_date ??= now();
        });

        // Saat sale baru dibuat
        static::created(function (Sale $sale) {
            self::syncCustomerDebt($sale, oldStatus: null);
        });

        // Saat sale diupdate (misal status berubah)
        static::updated(function (Sale $sale) {
            self::syncCustomerDebt($sale, oldStatus: $sale->getOriginal('status'));
        });

        // Saat sale dihapus, hapus hutang-nya juga
        static::deleted(function (Sale $sale) {
            self::removeCustomerDebt($sale);
        });
    }

    protected static function syncCustomerDebt(Sale $sale, ?string $oldStatus): void
    {
        if (!$sale->customer_id) return;

        $customer = \App\Models\Parties\Customer::find($sale->customer_id);
        if (!$customer) return;

        $debt = $sale->total - $sale->paid; // selisih = hutang
        $isNowPending  = $sale->status === Sale::STATUS_PENDING;
        $wasPending    = $oldStatus === Sale::STATUS_PENDING;

        if ($isNowPending && !$wasPending) {
            // Baru jadi Belum Lunas → tambah hutang
            $customer->increment('payable_amount', max(0, $debt));
        } elseif (!$isNowPending && $wasPending) {
            // Dari Belum Lunas → Lunas/Batal → hapus hutang lama
            $oldDebt = $sale->getOriginal('total') - $sale->getOriginal('paid');
            $customer->decrement('payable_amount', max(0, $oldDebt));
        } elseif ($isNowPending && $wasPending) {
            // Tetap Belum Lunas tapi nilai berubah → adjust selisihnya
            $oldDebt = $sale->getOriginal('total') - $sale->getOriginal('paid');
            $diff    = max(0, $debt) - max(0, $oldDebt);
            $customer->increment('payable_amount', $diff);
        }
    }

    protected static function removeCustomerDebt(Sale $sale): void
    {
        if (!$sale->customer_id || $sale->status !== Sale::STATUS_PENDING) return;

        $customer = \App\Models\Parties\Customer::find($sale->customer_id);
        $debt = $sale->total - $sale->paid;
        $customer?->decrement('payable_amount', max(0, $debt));
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(\App\Models\Return\SaleReturn::class);
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

    public static function generateInvoiceNumber(int $tenantId): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'INV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Reduce stock saat penjualan
     */
    public function reduceStock(): void
    {
        $alreadyReduced = StockMovement::where('reference_type', 'sale')
            ->where('reference_id', $this->id)
            ->where('type', StockMovement::TYPE_OUT)
            ->exists();

        if ($alreadyReduced) {
            return; // Sudah pernah reduce, skip
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                // Skip jika product tidak track stock
                if (!$product->track_stock) {
                    continue;
                }

                $stockBefore = $product->stock;
                $product->decrement('stock', $item->qty);

                StockMovement::create([
                    'tenant_id' => $this->tenant_id,
                    'product_id' => $item->product_id,
                    'user_id' => $this->user_id,
                    'reference_type' => 'sale',
                    'reference_id' => $this->id,
                    'type' => StockMovement::TYPE_OUT,
                    'qty' => $item->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $item->qty,
                    'notes' => 'Penjualan #' . $this->invoice_number,
                ]);
            }

            // Update piutang customer jika belum lunas
            if ($this->customer_id && $this->status === self::STATUS_PENDING) {
                $remaining = $this->total - $this->paid;
                if ($remaining > 0 && method_exists($this->customer, 'incrementReceivable')) {
                    $this->customer->incrementReceivable($remaining);
                }
            }
        });
    }

    /**
     * Restore stock saat sale dibatalkan
     */
    public function restoreStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                if (!$product->track_stock) {
                    continue;
                }

                $stockBefore = $product->stock;
                $product->increment('stock', $item->qty);

                StockMovement::create([
                    'tenant_id' => $this->tenant_id,
                    'product_id' => $item->product_id,
                    'user_id' => $this->user_id,
                    'reference_type' => 'sale_cancelled',
                    'reference_id' => $this->id,
                    'type' => StockMovement::TYPE_IN,
                    'qty' => $item->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore + $item->qty,
                    'notes' => 'Pembatalan Penjualan #' . $this->invoice_number,
                ]);
            }

            // Hapus stock movement lama
            StockMovement::where('reference_type', 'sale')
                ->where('reference_id', $this->id)
                ->where('type', StockMovement::TYPE_OUT)
                ->delete();
        });
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal - $this->discount + $this->tax;
        $this->change = max(0, $this->paid - $this->total);

        $this->status = match (true) {
            $this->paid >= $this->total => self::STATUS_PAID,
            default => self::STATUS_PENDING,
        };

        $this->save();
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeForCurrentTenant($query)
    {
        if ($tenantId = Filament::getTenant()?->id) {
            return $query->where('tenant_id', $tenantId);
        }
        return $query;
    }
}
