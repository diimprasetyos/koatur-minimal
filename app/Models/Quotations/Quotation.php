<?php

namespace App\Models\Quotations;

use App\Models\Parties\Customer;
use App\Models\Sales\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Quotation extends Model
{
    protected $fillable = [
        'uuid',
        'tenant_id',
        'customer_id',
        'user_id',
        'code',
        'status',
        'valid_until',
        'notes',
        'discount_amount',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    // ─── Boot ────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // UUID
            $model->uuid ??= Str::uuid();

            // Code di-scope ke tenant agar tidak tabrakan antar tenant
            if (empty($model->code)) {
                $model->code = self::generateCode($model->tenant_id);
            }
        });

        static::saved(function (self $model) {
            if ($model->wasChanged(['discount_amount', 'tax_amount'])) {
                $model->recalculate();
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
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

    // ─── Helpers ─────────────────────────────────────────────────

    public static function generateCode(int $tenantId): string
    {
        $year = now()->format('Y');
        $count = self::where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return 'QUO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]);
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast()
            && $this->status !== self::STATUS_ACCEPTED;
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum(DB::raw('price * quantity'));

        $this->total_amount = $subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }

    // ─── Actions ─────────────────────────────────────────────────

    /**
     * Convert quotation ke Sale dalam satu transaction.
     * Melempar exception jika quotation bukan draft/sent.
     */
    public function convertToSale(): Sale
    {
        if (!$this->isEditable()) {
            throw new \LogicException("Quotation #{$this->code} cannot be converted (status: {$this->status}).");
        }

        return DB::transaction(function () {
            $this->loadMissing('items');

            $sale = Sale::create([
                'tenant_id' => $this->tenant_id,
                'customer_id' => $this->customer_id,
                'user_id' => auth()->id(),
                'discount_amount' => $this->discount_amount,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                // Biarkan Sale::booted() generate code-nya sendiri
            ]);

            foreach ($this->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $this->update(['status' => self::STATUS_ACCEPTED]);

            return $sale;
        });
    }


}
