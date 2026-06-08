<?php

namespace App\Filament\Superadmin\Resources\SubscriptionPlans\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Identitas dan deskripsi plan langganan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Plan')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (callable $set, ?string $state) =>
                                $set('slug', str($state)->slug())
                            ),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Diisi otomatis dari nama. Contoh: basic-plan'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(1000),
                    ]),

                Section::make('Harga & Siklus Tagihan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->helperText('Isi 0 untuk plan gratis.'),

                        Select::make('billing_cycle')
                            ->label('Siklus Tagihan')
                            ->required()
                            ->options([
                                'bulan'  => 'Per Bulan',
                                'tahun'  => 'Per Tahun',
                                'trial'  => 'Trial',
                            ]),
                    ]),

                Section::make('Batas Penggunaan')
                    ->description('Tentukan kuota maksimum untuk plan ini.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('max_tenants')
                            ->label('Maks. Tenant')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->helperText('0 = tidak terbatas'),

                        TextInput::make('max_users_per_tenant')
                            ->label('Maks. User per Tenant')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->helperText('0 = tidak terbatas'),

                        TextInput::make('max_products')
                            ->label('Maks. Produk')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->helperText('0 = tidak terbatas'),
                    ]),

                Section::make('Fitur')
                    ->description('Daftar fitur yang tersedia pada plan ini (key → true/false).')
                    ->schema([
                        KeyValue::make('features')
                            ->label('Fitur')
                            ->keyLabel('Nama Fitur')
                            ->valueLabel('Aktif (true / false)')
                            ->addActionLabel('Tambah Fitur')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Plan Aktif')
                            ->default(true)
                            ->helperText('Hanya plan aktif yang bisa dipilih oleh tenant.'),
                    ]),
            ]);
    }
}