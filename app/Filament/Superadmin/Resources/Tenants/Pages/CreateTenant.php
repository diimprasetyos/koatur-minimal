<?php

namespace App\Filament\Superadmin\Resources\Tenants\Pages;

use App\Filament\Superadmin\Resources\Tenants\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
