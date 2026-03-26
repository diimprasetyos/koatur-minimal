<?php

namespace App\Filament\Superadmin\Resources\Tenants\Schemas;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Toko')->schema([
                TextInput::make('name')
                    ->label('Nama Toko')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn(Set $set, ?string $state) =>
                        $set('slug', Str::slug($state ?? ''))
                    ),

                TextInput::make('slug')
                    ->label('Slug')
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->helperText('Digunakan sebagai URL: /admin/{slug}'),

                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->nullable(),

                TextInput::make('address')
                    ->label('Alamat')
                    ->nullable(),

                Select::make('subscription_plan')
                    ->label('Paket Langganan')
                    ->options([
                        'free' => 'Free',
                        'basic' => 'Basic',
                        'pro' => 'Pro',
                    ])
                    ->default('free')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                FileUpload::make('logo')
                    ->label('Logo Toko')
                    ->image()
                    ->directory('tenants/logos')
                    ->nullable()
                    ->columnSpan(2),
            ])->columns(2),
        ]);
    }
}
