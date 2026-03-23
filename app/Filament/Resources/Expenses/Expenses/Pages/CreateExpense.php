<?php

namespace App\Filament\Resources\Expenses\Expenses\Pages;

use App\Filament\Resources\Expenses\Expenses\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
