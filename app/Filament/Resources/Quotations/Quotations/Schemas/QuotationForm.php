<?php

namespace App\Filament\Resources\Quotations\Quotations\Schemas;

use App\Models\Quotations\Quotation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Info Penawaran')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->disabled()
                        ->placeholder('Auto-generate')
                        ->dehydrated(false),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            Quotation::STATUS_DRAFT => 'Draft',
                            Quotation::STATUS_SENT => 'Terkirim',
                            Quotation::STATUS_ACCEPTED => 'Diterima',
                            Quotation::STATUS_REJECTED => 'Ditolak',
                            Quotation::STATUS_EXPIRED => 'Kadaluarsa',
                        ])
                        ->default(Quotation::STATUS_DRAFT)
                        ->required(),

                    DatePicker::make('valid_until')
                        ->label('Berlaku Hingga')
                        ->minDate(now()),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Item Penawaran')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->label('')
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (!$state)
                                        return;
                                    $product = \App\Models\Product\Product::find($state);
                                    if ($product) {
                                        $set('product_name', $product->name);
                                        $set('price', $product->selling_price ?? $product->price ?? 0);
                                    }
                                })
                                ->columnSpan(3),

                            TextInput::make('product_name')
                                ->label('Nama Snapshot')
                                ->required()
                                ->helperText('Otomatis dari produk, bisa diedit manual')
                                ->columnSpan(2),

                            TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $qty = (float) ($get('quantity') ?? 0);
                                    $price = (float) ($state ?? 0);
                                    $set('subtotal', $qty * $price);
                                }),

                            TextInput::make('quantity')
                                ->label('Qty')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->default(1)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $qty = (float) ($state ?? 0);
                                    $price = (float) ($get('price') ?? 0);
                                    $set('subtotal', $qty * $price);
                                }),

                            TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(5)
                        ->addActionLabel('Tambah Item')
                        ->reorderable(false)
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $items = $get('items') ?? [];
                            $subtotal = collect($items)->sum(
                                fn($i) => (float) ($i['quantity'] ?? 0) * (float) ($i['price'] ?? 0)
                            );
                            $discount = (float) ($get('discount_amount') ?? 0);
                            $tax = (float) ($get('tax_amount') ?? 0);
                            $set('total_amount', round($subtotal - $discount + $tax, 2));
                        }),
                ]),

            Section::make('Ringkasan')
                ->columns(4)
                ->schema([
                    TextInput::make('discount_amount')
                        ->label('Diskon')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $items = $get('items') ?? [];
                            $subtotal = collect($items)->sum(
                                fn($i) => (float) ($i['quantity'] ?? 0) * (float) ($i['price'] ?? 0)
                            );
                            $discount = (float) ($get('discount_amount') ?? 0);
                            $tax = (float) ($get('tax_amount') ?? 0);
                            $set('total_amount', round($subtotal - $discount + $tax, 2));
                        }),

                    TextInput::make('tax_amount')
                        ->label('Pajak')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $items = $get('items') ?? [];
                            $subtotal = collect($items)->sum(
                                fn($i) => (float) ($i['quantity'] ?? 0) * (float) ($i['price'] ?? 0)
                            );
                            $discount = (float) ($get('discount_amount') ?? 0);
                            $tax = (float) ($get('tax_amount') ?? 0);
                            $set('total_amount', round($subtotal - $discount + $tax, 2));
                        }),

                    TextInput::make('total_amount')
                        ->label('Total')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(true)
                        ->columnSpan(2),
                ]),
        ]);
    }

    protected static function recalcTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = collect($items)->sum(
            fn($i) => (float) ($i['quantity'] ?? 0) * (float) ($i['price'] ?? 0)
        );
        $discount = (float) ($get('discount_amount') ?? 0);
        $tax = (float) ($get('tax_amount') ?? 0);

        $set('total_amount', round($subtotal - $discount + $tax, 2));
    }
}
