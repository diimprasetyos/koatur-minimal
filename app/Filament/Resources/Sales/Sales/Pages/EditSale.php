<?php

namespace App\Filament\Resources\Sales\Sales\Pages;

use App\Filament\Resources\Sales\Sales\SaleResource;
use App\Models\Sales\Sale;
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

    protected function afterSave(): void
    {
        $statusChanged = $this->record->wasChanged('status');

        if ($statusChanged) {
            $oldStatus = $this->record->getOriginal('status');
            $newStatus = $this->record->status;

            // Dari pending/draft ke paid → reduce stock
            if ($newStatus === Sale::STATUS_PAID && $oldStatus !== Sale::STATUS_PAID) {
                $this->record->reduceStock();
            }

            // Ke cancelled → restore stock
            if ($newStatus === Sale::STATUS_CANCELLED && $oldStatus !== Sale::STATUS_CANCELLED) {
                $this->record->restoreStock();
            }

            // Dari cancelled ke paid → reduce stock
            if ($oldStatus === Sale::STATUS_CANCELLED && $newStatus === Sale::STATUS_PAID) {
                $this->record->reduceStock();
            }
        }
    }
}
