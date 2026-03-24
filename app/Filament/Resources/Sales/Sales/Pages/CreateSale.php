<?php

namespace App\Filament\Resources\Sales\Sales\Pages;

use App\Filament\Resources\Sales\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function afterCreate(): void
    {
        // Reduce stock setelah sale dan items tersimpan
        if ($this->record->status === \App\Models\Sales\Sale::STATUS_PAID) {
            $this->record->reduceStock();
        }
    }
}
