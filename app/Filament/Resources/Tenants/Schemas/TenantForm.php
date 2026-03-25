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
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),

                // Slug di-generate otomatis dari name via bootTenant(),
                // tapi tetap bisa diedit manual jika diperlukan.
                TextInput::make('slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Dibuat otomatis dari nama jika dikosongkan.'),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('address')
                    ->maxLength(500),

                FileUpload::make('logo')
                    ->image()
                    ->directory('tenants/logos')
                    ->visibility('public')
                    ->nullable(),

                // subscription_plan: disabled di UI tapi tetap ikut save (dehydrated).
                TextInput::make('subscription_plan')
                    ->disabled()
                    ->dehydrated()
                    ->default('free'),

                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}