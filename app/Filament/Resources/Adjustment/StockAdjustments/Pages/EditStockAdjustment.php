<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Pages;

use App\Filament\Resources\Adjustment\StockAdjustments\StockAdjustmentResource;
use App\Models\Adjustment\StockAdjustment;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockAdjustment extends EditRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn() => $this->getRecord()->status === StockAdjustment::STATUS_DRAFT),
        ];
    }
}
