<?php

namespace App\Models;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\Sale;
use App\Models\Subscription\Subscription;
use App\Traits\HasUuid;
use App\Utils\PlanLimit;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasRoles, Notifiable, HasUuid, HasApiTokens;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'subscription_plan', // tetap ada untuk backward compatibility
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'password'         => 'hashed',
    ];

    // ─── Filament Panel Access ────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'superadmin' => $this->hasRole('super_admin'),
            'admin'      => !$this->hasRole('super_admin') && $this->tenants()->exists(),
            default      => false,
        };
    }

    // ─── Filament Tenancy Contracts ───────────────────────────────

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants()->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenants()->whereKey($tenant)->exists();
    }

    // ─── Relations ───────────────────────────────────────────────

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    /**
     * Relasi ke tabel subscriptions.
     * User bisa punya banyak subscription (history), tapi yang aktif cukup pakai activeSubscription().
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Subscription yang sedang aktif atau trial.
     * Pakai: $user->activeSubscription
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', [Subscription::STATUS_TRIAL, Subscription::STATUS_ACTIVE])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latestOfMany();
    }

    // ─── Subscription Helpers ─────────────────────────────────────

    /**
     * Apakah user punya subscription yang masih aktif (termasuk trial)?
     * Ini yang dipakai middleware untuk gate keeping.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Apakah user sedang dalam masa trial?
     */
    public function isOnTrial(): bool
    {
        return $this->activeSubscription?->isTrial() ?? false;
    }

    /**
     * Berapa hari tersisa di subscription aktif?
     */
    public function subscriptionDaysRemaining(): ?int
    {
        return $this->activeSubscription?->daysRemaining();
    }

    /**
     * Ambil slug plan yang aktif. Fallback ke 'basic'.
     * Masih kompatibel dengan PlanLimit yang lama.
     */
    public function getSubscriptionPlan(): string
    {
        return $this->activeSubscription?->plan?->slug
            ?? $this->subscription_plan
            ?? 'basic';
    }

    // ─── Role Helpers ─────────────────────────────────────────────

    public function canCreateTenant(): bool
    {
        return PlanLimit::canCreateTenant($this);
    }

    public function remainingTenants(): ?int
    {
        return PlanLimit::remainingTenants($this);
    }

    public function isProPlan(): bool
    {
        return $this->getSubscriptionPlan() === 'pro';
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isKasir(): bool
    {
        return $this->hasRole('kasir');
    }
}
