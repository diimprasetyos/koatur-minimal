<?php

namespace App\Filament\Resources\Expenses\ExpenseCategories\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(fn() => Filament::getTenant()?->id)
                    ->required(),
                TextInput::make('name')
                    ->required(),
                ColorPicker::make('color')
                    ->label('Warna Badge')
                    ->nullable(),
            ]);
    }
}
