<?php

namespace App\Filament\Resources\Purchases\Purchases\Pages;

use App\Filament\Resources\Purchases\Purchases\PurchaseResource;
use App\Models\Purchases\Purchase;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected string $statusBeforeSave = '';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->statusBeforeSave = $this->record->getOriginal('status') ?? $this->record->status;
    }

    protected function afterSave(): void
    {
        $this->record->refresh();
        $this->record->recalculate();

        $oldStatus = $this->statusBeforeSave;
        $newStatus = $this->record->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        if ($newStatus === Purchase::STATUS_RECEIVED) {
            $this->record->load('items.product');
            $this->record->receiveStock();
        }
    }
}