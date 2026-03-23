<?php

namespace App\Filament\Resources\Parties\Customers\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Nama Pelanggan')
                        ->required()
                        ->maxLength(100)
                        ->columnSpan(2),

                    TextInput::make('phone')
                        ->label('No. HP / WhatsApp')
                        ->tel()
                        ->maxLength(20)
                        ->nullable(),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(100)
                        ->nullable(),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3)
                        ->nullable()
                        ->columnSpan(2),

                    TextInput::make('loyalty_points')
                        ->label('Poin Loyalty')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Tambah/kurangi poin secara manual')
                        ->columnSpan(2),
                ])->columns(2),
            ]);
    }
}
