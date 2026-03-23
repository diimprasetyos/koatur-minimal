<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Pages;

use App\Filament\Resources\Return\PurchaseReturns\PurchaseReturnResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseReturn extends EditRecord
{
    protected static string $resource = PurchaseReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
