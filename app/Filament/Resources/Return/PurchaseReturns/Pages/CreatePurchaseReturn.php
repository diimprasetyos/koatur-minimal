<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Pages;

use App\Filament\Resources\Return\PurchaseReturns\PurchaseReturnResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseReturn extends CreateRecord
{
    protected static string $resource = PurchaseReturnResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if ($record->status === \App\Models\Return\PurchaseReturn::STATUS_APPROVED) {
            $record->load('items.product');
            $record->processReturn();
        }
    }
}
