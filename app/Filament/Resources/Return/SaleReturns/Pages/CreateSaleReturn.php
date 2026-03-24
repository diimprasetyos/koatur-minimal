<?php

namespace App\Filament\Resources\Return\SaleReturns\Pages;

use App\Filament\Resources\Return\SaleReturns\SaleReturnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaleReturn extends CreateRecord
{
    protected static string $resource = SaleReturnResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if ($record->status === \App\Models\Return\SaleReturn::STATUS_APPROVED) {
            $record->load('items.product');
            $record->processReturn();
        }
    }
}
