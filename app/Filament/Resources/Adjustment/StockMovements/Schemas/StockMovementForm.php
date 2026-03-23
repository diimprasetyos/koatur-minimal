<?php

namespace App\Filament\Resources\Adjustment\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('reference_type'),
                TextInput::make('reference_id')
                    ->numeric(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_before')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_after')
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
