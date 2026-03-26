<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $user = $this->getRecord();
        $tenant = $this->getTenant();

        // Pastikan tenant aktif tetap ter-assign (jaga-jaga kalau belum ada)
        if ($tenant) {
            $user->tenants()->syncWithoutDetaching([$tenant->id]);
        }

        // Jika super_admin mengubah tenant tambahan via form
        $extraTenants = $this->data['extra_tenants'] ?? [];
        if (!empty($extraTenants)) {
            $user->tenants()->syncWithoutDetaching($extraTenants);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}