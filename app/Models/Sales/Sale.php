<?php

namespace App\Models\Sales;

use App\Models\Parties\Customer;
use App\Models\Sales\SaleItem;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasUuid, BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'user_id',
        'customer_id',
        'invoice_number',
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
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax'      => 'decimal:2',
        'total'    => 'decimal:2',
        'paid'     => 'decimal:2',
        'change'   => 'decimal:2',
    ];

    const STATUS_PAID      = 'paid';
    const STATUS_PENDING   = 'pending';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_CASH     = 'cash';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_EWALLET  = 'ewallet';

    public static function bootSale(): void
    {
        static::creating(function (self $model) {
            if (empty($model->invoice_number)) {
                $model->invoice_number = self::generateInvoiceNumber($model->tenant_id);
            }
        });
    }

    public static function generateInvoiceNumber(int $tenantId): string
    {
        $count = self::whereDate('created_at', today())
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return sprintf('INV-%s-%04d', now()->format('Ymd'), $count);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
    public function scopeByTenant($query, int $id)
    {
        return $query->where('tenant_id', $id);
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
}
