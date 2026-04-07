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

    protected function beforeSave(): void
    {
        // Simpan status lama sebelum data di-save ke DB
        $this->statusBeforeSave = $this->record->getOriginal('status') ?? $this->record->status;
    }

    protected function afterSave(): void
    {
        $oldStatus = $this->statusBeforeSave;
        $newStatus = $this->record->status;

        if ($oldStatus === $newStatus) {
            return; // Tidak ada perubahan status, skip
        }

        // Dari pending/draft ke paid → reduce stock
        if ($newStatus === Sale::STATUS_PAID && $oldStatus !== Sale::STATUS_PAID) {
            $this->record->load('items.product');
            $this->record->reduceStock();
        }

        // Ke cancelled → restore stock
        if ($newStatus === Sale::STATUS_CANCELLED && $oldStatus !== Sale::STATUS_CANCELLED) {
            $this->record->load('items.product');
            $this->record->restoreStock();
        }
    }
}
