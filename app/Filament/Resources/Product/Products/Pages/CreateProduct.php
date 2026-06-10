<?php

namespace App\Filament\Resources\Product\Products\Pages;

use App\Filament\Resources\Product\Products\ProductResource;
use App\Models\Product\Product;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['sku'])) {
            do {
                $sku = 'PRD-' . strtoupper(substr(uniqid(), -6));
            } while (Product::where('sku', $sku)->exists());

            $data['sku'] = $sku;
        }

        return $data;
    }
}
