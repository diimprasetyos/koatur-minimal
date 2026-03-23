<?php

namespace App\Filament\Resources\Sales\Sales\Pages;

use App\Filament\Resources\Sales\Sales\SaleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
