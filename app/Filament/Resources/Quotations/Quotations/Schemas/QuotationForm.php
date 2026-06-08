<?php

namespace App\Filament\Resources\Quotations\Quotations\Schemas;

use App\Models\Product\Product;
use App\Models\Quotations\Quotation;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class QuotationForm
{
    // Ambil tenant_id yang sedang aktif — null-safe
    protected static function currentTenantId(): ?int
    {
        return Filament::getTenant()?->id;
    }

    // Format angka ke Rupiah
    protected static function rp(float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    // Hitung nilai subtotal
    protected static function recalcSubtotal(Get $get, Set $set): void
    {
        $qty      = (float) ($get('quantity') ?? 0);
        $price    = (float) ($get('price') ?? 0);
        $subtotal = $qty * $price;

        $set('subtotal', $subtotal);
    }

    // Hitung nilai total
    protected static function calcTotalAmount(Get $get): float
    {
        $subtotal = collect($get('items') ?? [])
            ->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('discount_amount') ?? 0);
        $tax      = (float) ($get('tax_amount') ?? 0);

        return max(0, $subtotal - $discount + $tax);
    }

    // Ambil produk hanya milik tenant aktif — cegah manipulasi ID dari luar
    protected static function getProductForCurrentTenant(?string $productId): ?Product
    {
        if (!$productId) return null;

        $tenantId = self::currentTenantId();
        if (!$tenantId) return null;

        return Product::where('id', $productId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    // Hitung ulang total dari items + diskon + pajak, lalu set ke form
    protected static function recalcTotals(Get $get, Set $set): void
    {
        $subtotal = collect($get('items') ?? [])
            ->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('discount_amount') ?? 0);
        $tax      = (float) ($get('tax_amount') ?? 0);

        $total = max(0, $subtotal - $discount + $tax);

        $set('total_amount', $total);
    }

    // Susun dan kembalikan schema form lengkap
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
                            Quotation::STATUS_DRAFT    => 'Draft',
                            Quotation::STATUS_SENT     => 'Terkirim',
                            Quotation::STATUS_ACCEPTED => 'Diterima',
                            Quotation::STATUS_REJECTED => 'Ditolak',
                            Quotation::STATUS_EXPIRED  => 'Kadaluarsa',
                        ])
                        ->default(Quotation::STATUS_DRAFT)
                        ->required(),

                    DatePicker::make('valid_until')
                        ->label('Berlaku Hingga')
                        ->minDate(now()),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->relationship(
                            name: 'customer',
                            titleAttribute: 'name',
                            modifyQueryUsing: function (Builder $query) {
                                $tenantId = self::currentTenantId();
                                if ($tenantId) {
                                    $query->where('tenant_id', $tenantId);
                                }
                            }
                        )
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
                                ->relationship(
                                    name: 'product',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query) {
                                        $tenantId = self::currentTenantId();
                                        if ($tenantId) {
                                            $query->where('tenant_id', $tenantId);
                                        }
                                    }
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                                    $product = self::getProductForCurrentTenant($state);
                                    if (!$product) return;

                                    $price = (float) ($product->selling_price ?? $product->price ?? 0);
                                    $qty   = (float) ($get('quantity') ?? 1);

                                    $set('product_name', $product->name);
                                    $set('price', $price);
                                    $set('subtotal', $price * $qty);
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
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::recalcSubtotal($get, $set);
                                })
                                ->columnSpan(2),

                            TextInput::make('quantity')
                                ->label('Qty')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::recalcSubtotal($get, $set);
                                })
                                ->columnSpan(1),

                            Placeholder::make('subtotal_row')
                                ->label('Subtotal')
                                ->live()
                                ->content(fn(Get $get): HtmlString => new HtmlString(
                                    '<span class="text-sm font-medium">' .
                                    self::rp((float) ($get('subtotal') ?? 0)) .
                                    '</span>'
                                ))
                                ->columnSpan(2),

                            Hidden::make('subtotal')
                                ->default(0)
                                ->dehydrated(),
                        ])
                        ->columns(5)
                        ->addActionLabel('Tambah Item')
                        ->reorderable(false)
                        ->live()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::recalcTotals($get, $set)),
                ]),

            Section::make('Ringkasan')
                ->columns(4)
                ->schema([
                    TextInput::make('discount_amount')
                        ->label('Diskon')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::recalcTotals($get, $set))
                        ->columnSpan(2),

                    TextInput::make('tax_amount')
                        ->label('Pajak')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::recalcTotals($get, $set))
                        ->columnSpan(2),

                    Placeholder::make('total_amount_display')
                        ->label('Total')
                        ->live()
                        ->content(fn(Get $get): HtmlString => new HtmlString(
                            '<span class="text-lg font-bold text-primary-600">' .
                            self::rp(self::calcTotalAmount($get)) .
                            '</span>'
                        ))
                        ->columnSpan(2),

                    Hidden::make('total_amount')
                        ->dehydrated()
                        ->default(0),
                ]),
        ]);
    }
}
