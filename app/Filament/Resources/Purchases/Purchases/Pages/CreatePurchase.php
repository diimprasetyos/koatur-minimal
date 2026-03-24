<?php

namespace App\Filament\Resources\Purchases\Purchases\Pages;

use App\Filament\Resources\Purchases\Purchases\PurchaseResource;
use App\Models\Purchases\Purchase;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function afterCreate(): void
    {
        // Receive stock setelah purchase dan items tersimpan
        if ($this->record->status === Purchase::STATUS_RECEIVED) {
            $this->record->receiveStock();
        }
    }
}
