<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('reference_number')
                    ->required(),
                DatePicker::make('adjustment_date')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('confirmed'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
