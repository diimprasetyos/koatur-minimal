<?php

namespace App\Filament\Resources\Parties\Customers\Pages;

use App\Filament\Resources\Parties\Customers\CustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }
}
