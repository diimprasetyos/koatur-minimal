<?php

namespace App\Traits;

use Filament\Facades\Filament;

trait TenantAware
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = Filament::getTenant()?->id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            $tenantId = Filament::getTenant()?->id;
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }
        });
    }
}