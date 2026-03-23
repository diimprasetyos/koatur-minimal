<?php

namespace App\Filament\Resources\Product\Categories\Pages;

use App\Filament\Resources\Product\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['tenant_id'] = auth()->user()->tenant_id
    //         ?? \App\Models\Tenant::first()->id; // fallback ke tenant pertama

    //     return $data;
    // }
}
