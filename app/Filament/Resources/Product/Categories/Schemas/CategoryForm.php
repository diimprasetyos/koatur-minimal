<?php

namespace App\Filament\Resources\Product\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(100)
                        ->columnSpan(2),

                    ColorPicker::make('color')
                        ->label('Warna Badge')
                        ->nullable(),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),
            ]);
    }
}