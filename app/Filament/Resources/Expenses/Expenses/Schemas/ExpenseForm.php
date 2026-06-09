<?php

namespace App\Filament\Resources\Expenses\Expenses\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
                Select::make('expense_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name', fn($query) =>
                        $query->where('tenant_id', Filament::getTenant()?->id)
                    )
                    ->searchable()
                    ->nullable(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('expense_date')
                    ->required(),
                Select::make('payment_method')
                    ->options([
                        'cash' => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet' => '📱 E-Wallet',
                    ]),
                Textarea::make('notes')
                    ->columnSpanFull(),
                FileUpload::make('attachment')
                    ->image(),
            ]);
    }
}