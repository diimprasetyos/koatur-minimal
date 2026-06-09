<?php

namespace App\Models\Parties;

use App\Models\Purchases\Purchase;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Supplier extends Model
{
    protected $fillable = [
        'tenant_id',
        'uuid',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'contact_person',
        'payable_amount',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'payable_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= Str::uuid();
        });
    }

    // ─── Relations ────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(\App\Models\Return\PurchaseReturn::class);
    }


    // ─── Helpers ──────────────────────────────────────────────────

    public function incrementPayable(float $amount): void
    {
        $this->increment('payable_amount', $amount);
    }

    public function decrementPayable(float $amount): void
    {
        $newAmount = max(0, $this->payable_amount - $amount);
        $this->update(['payable_amount' => $newAmount]);
    }
}
