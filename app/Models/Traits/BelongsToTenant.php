<?php

namespace App\Models\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-inject tenant_id saat creating
        static::creating(function (self $model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    // ─── Scope ───────────────────────────────────────────────────

    /**
     * Scope query hanya untuk tenant yang sedang login.
     * Digunakan di Filament Resource: getEloquentQuery()
     */
    public function scopeForCurrentTenant(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('tenant_id'), auth()->user()?->tenant_id);
    }

    // ─── Relation ────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
