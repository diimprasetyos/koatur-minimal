<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->getRecord();
        $tenant = filament()->getTenant();

        // Otomatis assign ke tenant yang sedang aktif
        $tenantIds = [];
        if ($tenant) {
            $tenantIds[] = $tenant->id;
        }

        // Jika super_admin memilih tenant tambahan via form, merge sekalian
        $extraTenants = $this->data['extra_tenants'] ?? [];
        if (!empty($extraTenants)) {
            $tenantIds = array_unique(array_merge($tenantIds, $extraTenants));
        }

        // syncWithoutDetaching agar tidak mencabut tenant yang sudah ada sebelumnya
        if (!empty($tenantIds)) {
            $user->tenants()->syncWithoutDetaching($tenantIds);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}