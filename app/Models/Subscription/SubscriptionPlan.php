<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'price',
        'billing_cycle',
        'max_tenants',
        'max_users_per_tenant',
        'max_products',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price'                => 'integer',
        'max_tenants'          => 'integer',
        'max_users_per_tenant' => 'integer',
        'max_products'         => 'integer',
        'features'             => 'array',   // JSON otomatis jadi array PHP
        'is_active'            => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────

    /**
     * Satu plan bisa dipakai banyak subscription (user berbeda).
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Apakah plan ini gratis?
     */
    public function isFree(): bool
    {
        return $this->price === 0;
    }

    /**
     * Apakah plan ini adalah trial?
     */
    public function isTrial(): bool
    {
        return $this->slug === 'trial';
    }

    /**
     * Cek apakah plan punya fitur tertentu.
     * Contoh: $plan->hasFeature('reports')
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return (bool) ($features[$feature] ?? false);
    }

    /**
     * Format harga ke rupiah.
     * Contoh: "Rp 99.000 / bulan"
     */
    public function formattedPrice(): string
    {
        if ($this->price === 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($this->price, 0, ',', '.') . ' / ' . $this->billing_cycle;
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Hanya plan yang aktif.
     * Pakai: SubscriptionPlan::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
