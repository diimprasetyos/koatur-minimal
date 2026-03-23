<?php

namespace App\Filament\Resources\Expenses\ExpenseCategories\Pages;

use App\Filament\Resources\Expenses\ExpenseCategories\ExpenseCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;
}
