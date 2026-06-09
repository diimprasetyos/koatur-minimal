<?php

namespace App\Filament\Resources\Expenses\Expenses\Pages;

use App\Filament\Resources\Expenses\Expenses\ExpenseResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;
        $data['user_id'] = Auth::id();

        return $data;
    }
}
