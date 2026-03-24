<?php

namespace App\Filament\Resources\Product\Products\Schemas;

use App\Models\Product\Category;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Produk')
                    ->schema([
                        Hidden::make('tenant_id')
                            ->default(fn() => Filament::getTenant()?->id)
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(2),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(function () {
                                return Category::where('tenant_id', auth()->user()->tenant_id)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable(),

                        TextInput::make('sku')
                            ->label('SKU / Kode Produk')
                            ->maxLength(50)
                            ->nullable()
                            ->placeholder('Kosongkan untuk generate otomatis'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->nullable()
                            ->columnSpan(2),

                        FileUpload::make('image')
                            ->label('Foto Produk')
                            ->image()
                            ->directory('products')
                            ->imageResizeTargetWidth('400')
                            ->nullable()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Harga & Stok')
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga Jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),

                        TextInput::make('cost_price')
                            ->label('Harga Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Digunakan untuk laporan profit'),

                        TextInput::make('stock')
                            ->label('Stok Awal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn(Get $get) => $get('track_stock')),

                        Toggle::make('track_stock')
                            ->label('Pantau Stok')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk produk tanpa batas stok (misal: jasa)')
                            ->live(),

                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true),
                    ])
                    ->columns(2),

            ]);
    }
}
