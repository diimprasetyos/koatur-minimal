<?php

namespace App\Filament\Resources\Quotations\Quotations\Pages;

use App\Filament\Resources\Quotations\Quotations\QuotationResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()?->id;
        $data['user_id'] = auth()->id();

        return $data;
    }
}
