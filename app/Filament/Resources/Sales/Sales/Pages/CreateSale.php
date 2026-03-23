<?php

namespace App\Filament\Resources\Sales\Sales\Pages;

use App\Filament\Resources\Sales\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        // tenant_id sudah diisi otomatis oleh Filament
        return $data;
    }
}
