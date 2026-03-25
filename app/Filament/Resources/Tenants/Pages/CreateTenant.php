<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function afterCreate(): void
    {
        $user = auth()->user();

        // Super admin tidak perlu di-attach (dia bisa lihat semua).
        if ($user->is_super_admin ?? false) {
            return;
        }

        /** @var Tenant $tenant */
        $tenant = $this->record;

        // Attach user ke tenant yang baru dibuat (hindari duplikat dengan syncWithoutDetaching).
        $tenant->users()->syncWithoutDetaching([$user->id]);
    }
}
