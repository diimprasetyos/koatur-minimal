<?php

namespace App\Filament\Resources\Purchases\Purchases\Pages;

use App\Filament\Resources\Purchases\Purchases\PurchaseResource;
use App\Models\Purchases\Purchase;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $statusChanged = $this->record->wasChanged('status');

        if ($statusChanged && $this->record->status === Purchase::STATUS_RECEIVED) {
            $this->record->receiveStock();
        }
    }
}
