<?php

namespace App\Filament\Resources\Expenses\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('expense_category_id')
                    ->numeric(),
                TextInput::make('reference_number'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('expense_date')
                    ->required(),
                TextInput::make('payment_method')
                    ->required()
                    ->default('cash'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('attachment'),
            ]);
    }
}
