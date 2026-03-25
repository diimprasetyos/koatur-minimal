<?php

namespace App\Filament\Resources\Quotations\Quotations\Pages;

use App\Filament\Resources\Quotations\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id']   = auth()->id();

        return $data;
    }
}
