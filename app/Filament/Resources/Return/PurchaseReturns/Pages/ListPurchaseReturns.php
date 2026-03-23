<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Pages;

use App\Filament\Resources\Return\PurchaseReturns\PurchaseReturnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseReturns extends ListRecords
{
    protected static string $resource = PurchaseReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
