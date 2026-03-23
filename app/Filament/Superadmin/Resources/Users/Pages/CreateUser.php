<?php

namespace App\Filament\Superadmin\Resources\Users\Pages;

use App\Filament\Superadmin\Resources\Users\UserResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->getRecord();

        // Auto-attach ke pivot tenant_user jika punya tenant
        if ($user->tenant_id) {
            Tenant::find($user->tenant_id)
                ?->users()
                ->syncWithoutDetaching([$user->id]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
