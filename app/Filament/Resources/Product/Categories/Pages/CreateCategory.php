<?php

namespace App\Filament\Resources\Product\Categories\Pages;

use App\Filament\Resources\Product\Categories\CategoryResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }
}
