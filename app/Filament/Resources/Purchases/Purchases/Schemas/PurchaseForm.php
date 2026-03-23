<?php

namespace App\Filament\Resources\Purchases\Purchases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PurchaseForm
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
                Select::make('supplier_id')
                    ->relationship('supplier', 'name'),
                TextInput::make('reference_number')
                    ->required(),
                TextInput::make('supplier_invoice'),
                DatePicker::make('purchase_date')
                    ->required(),
                DatePicker::make('due_date'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('paid')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('due')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('received'),
                TextInput::make('payment_status')
                    ->required()
                    ->default('paid'),
                TextInput::make('payment_method'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
