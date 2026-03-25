<?php

namespace App\Filament\Resources\Quotations\Quotations\Pages;

use App\Filament\Resources\Quotations\Quotations\QuotationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
