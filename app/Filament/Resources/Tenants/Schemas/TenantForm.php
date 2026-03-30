<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Toko')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),

                TextInput::make('slug')
                    ->label('Alias')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Dibuat otomatis dari nama jika dikosongkan.'),

                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('address')
                    ->label('Alamat')
                    ->maxLength(500),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('tenants/logos')
                    ->visibility('public')
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
            ]);
    }
}