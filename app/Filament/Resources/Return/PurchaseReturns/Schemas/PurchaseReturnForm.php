<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PurchaseReturnForm
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
                Select::make('purchase_id')
                    ->relationship('purchase', 'id')
                    ->required(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name'),
                TextInput::make('reference_number')
                    ->required(),
                DatePicker::make('return_date')
                    ->required(),
                TextInput::make('total_return')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('approved'),
                TextInput::make('return_method')
                    ->required()
                    ->default('debit_note'),
                Textarea::make('reason')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
