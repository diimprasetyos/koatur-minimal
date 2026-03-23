<?php

namespace App\Models;

use App\Models\Adjustment\StockMovement;
use App\Models\Sales\Sale;
use App\Models\Traits\HasUuid;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasRoles, Notifiable, HasUuid;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Filament Panel Access ────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            // Panel superadmin: hanya role super_admin, tidak butuh tenant
            'superadmin' => $this->hasRole('super_admin'),

            // Panel admin (tenant-aware): semua role kecuali super_admin
            // dan wajib punya tenant
            'admin' => ! $this->hasRole('super_admin')
                && $this->tenant_id !== null,

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

    /**
     * BelongsToMany — untuk Filament tenancy (pivot tenant_user)
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class);
    }

    /**
     * BelongsTo — untuk query internal (tenant default user ini)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

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
