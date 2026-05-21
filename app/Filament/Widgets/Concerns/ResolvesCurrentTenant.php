<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Tenant;
use Filament\Facades\Filament;

trait ResolvesCurrentTenant
{
    protected function getCurrentTenantId(): ?int
    {
        $tenant = Filament::getTenant();
        if ($tenant) {
            return $tenant->id;
        }

        $slug = request()->route('tenant');
        if ($slug) {
            $tenant = Tenant::where('slug', $slug)->first();
            if ($tenant) {
                return $tenant->id;
            }
        }

        // Fallback ke current_tenant_id user yang sedang login
        return auth()->user()?->current_tenant_id;
    }
}
