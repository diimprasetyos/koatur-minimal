<?php

namespace App\Filament\Resources\Parties\Suppliers\Pages;

use App\Filament\Resources\Parties\Suppliers\SupplierResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }
}
