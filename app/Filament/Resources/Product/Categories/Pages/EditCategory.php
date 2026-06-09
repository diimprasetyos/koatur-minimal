<?php

namespace App\Filament\Resources\Product\Categories\Pages;

use App\Filament\Resources\Product\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
