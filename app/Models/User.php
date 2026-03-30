<?php

namespace App\Models;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\Sale;
use App\Traits\HasUuid;
use App\Utils\PlanLimit;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'subscription_plan',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ─── Filament Panel Access ────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            // Panel superadmin: hanya role super_admin, tidak butuh tenant
            'superadmin' => $this->hasRole('super_admin'),

            // Panel admin (tenant-aware): semua role kecuali super_admin
            // dan wajib punya tenant
            'admin' => !$this->hasRole('super_admin')
            && $this->tenants()->exists(),

            default => false,
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

    // Tenant yang sedang aktif
    public function currentTenant()
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────
    public function getSubscriptionPlan(): string
    {
        return $this->subscription_plan ?? 'basic';
    }

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
        return $this->subscription_plan === 'pro';
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