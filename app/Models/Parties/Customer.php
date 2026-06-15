<?php

namespace App\Models\Parties;

use App\Models\Return\SaleReturn;
use App\Models\Sales\Sale;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
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

    // Boot

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // UUID
            $model->uuid ??= Str::uuid();

            // Tenant
            $model->tenant_id ??= Filament::getTenant()->id;

            // Code
            if (empty($model->code)) {

                $lastCode = self::query()
                    ->where('tenant_id', $model->tenant_id)
                    ->whereNotNull('code')
                    ->latest('id')
                    ->value('code');

                $nextNumber = 1;

                if ($lastCode) {
                    $parts = explode('-', $lastCode);
                    $nextNumber = ((int) end($parts)) + 1;
                }

                $model->code = sprintf(
                    'CUST-%d-%03d',
                    $model->tenant_id,
                    $nextNumber
                );
            }
        });
    }

    // Relations

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function saleReturns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }

    // Helpers

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
