<?php

namespace App\Filament\Resources\Parties\Customers\Pages;

use App\Filament\Resources\Parties\Customers\CustomerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }
}
