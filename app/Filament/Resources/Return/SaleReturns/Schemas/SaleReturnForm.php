<?php

namespace App\Filament\Resources\Return\SaleReturns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SaleReturnForm
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
                Select::make('sale_id')
                    ->relationship('sale', 'id')
                    ->required(),
                TextInput::make('reference_number')
                    ->required(),
                DatePicker::make('return_date')
                    ->required(),
                TextInput::make('total_refund')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('approved'),
                TextInput::make('refund_method')
                    ->required()
                    ->default('refund'),
                Textarea::make('reason')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
