<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Pages;

use App\Filament\Resources\Adjustment\StockAdjustments\StockAdjustmentResource;
use App\Models\Adjustment\StockAdjustment;
use App\Models\Adjustment\StockMovement;
use Filament\Resources\Pages\CreateRecord;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if ($record->status === StockAdjustment::STATUS_CONFIRMED) {
            $alreadyApplied = StockMovement::where('reference_type', StockMovement::REF_ADJUSTMENT)
                ->where('reference_id', $record->id)
                ->exists();

            if (!$alreadyApplied) {
                $record->load('items.product');
                $record->applyAdjustment();
            }
        }
    }
}
