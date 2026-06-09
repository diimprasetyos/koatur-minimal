<?php

namespace App\Filament\Resources\Expenses\ExpenseCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                ColorPicker::make('color')
                    ->label('Warna Badge')
                    ->nullable(),
            ]);
    }
}