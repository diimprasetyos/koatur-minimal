<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function resolveRecord(int|string $key): Model
    {
        $tenant = parent::resolveRecord($key);

        $user = auth()->user();

        // Super admin boleh edit semua.
        if ($user->is_super_admin ?? false) {
            return $tenant;
        }

        // Cek apakah user terdaftar di pivot tenant_user untuk tenant ini.
        $hasAccess = $tenant->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$hasAccess) {
            throw new NotFoundHttpException();
        }

        return $tenant;
    }
}
