<?php

namespace App\Filament\Resources\Expenses\ExpenseCategories\Pages;

use App\Filament\Resources\Expenses\ExpenseCategories\ExpenseCategoryResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;

        return $data;
    }
}