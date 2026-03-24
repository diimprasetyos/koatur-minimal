<?php

namespace App\Filament\Resources\Return\SaleReturns\Schemas;

use App\Models\Return\SaleReturn;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
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
use Illuminate\Support\HtmlString;

class SaleReturnForm
{
    protected static function rp(float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    protected static function calcTotal(Get $get): float
    {
        $items = $get('items') ?? [];
        return (float) collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => Filament::getTenant()?->id)->required(),
            Hidden::make('user_id')->default(fn() => auth()->id())->required(),
            Hidden::make('total_refund')->dehydrated(),

            Section::make('Informasi Retur')
                ->schema([
                    TextInput::make('reference_number')
                        ->label('No. Referensi')
                        ->disabled()
                        ->placeholder('Otomatis terisi')
                        ->dehydrated(false),

                    DatePicker::make('return_date')
                        ->label('Tanggal Retur')
                        ->required()
                        ->default(now())
                        ->native(false),

                    Select::make('sale_id')
                        ->label('No. Invoice Penjualan')
                        ->relationship(
                            'sale',
                            'invoice_number',
                            fn($q) => $q?->where('tenant_id', Filament::getTenant()?->id)
                                ?->whereIn('status', [Sale::STATUS_PAID, Sale::STATUS_PENDING])
                        )
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn(Set $set) => $set('items', [])),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            SaleReturn::STATUS_APPROVED => 'Disetujui',
                            SaleReturn::STATUS_PENDING  => 'Menunggu',
                            SaleReturn::STATUS_REJECTED => 'Ditolak',
                        ])
                        ->default(SaleReturn::STATUS_APPROVED),

                    Select::make('refund_method')
                        ->label('Metode Refund')
                        ->required()
                        ->options([
                            'refund'       => '💵 Refund Tunai',
                            'exchange'     => '🔄 Tukar Barang',
                            'store_credit' => '🎫 Kredit Toko',
                        ])
                        ->default('refund'),

                    Textarea::make('reason')
                        ->label('Alasan Retur')
                        ->rows(2)
                        ->nullable(),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Item yang Diretur')
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->relationship()
                        ->schema([
                            Select::make('sale_item_id')
                                ->label('Item Penjualan')
                                ->options(function (Get $get): array {
                                    $saleId = $get('../../sale_id');
                                    if (!$saleId) return [];

                                    return SaleItem::with('product')
                                        ->where('sale_id', $saleId)
                                        ->get()
                                        ->mapWithKeys(fn($item) => [
                                            $item->id => ($item->product->name ?? '-') .
                                                ' (Qty: ' . $item->qty . ' @ Rp ' .
                                                number_format($item->price, 0, ',', '.') . ')'
                                        ])
                                        ->toArray();
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                    if (!$state) return;
                                    $saleItem = SaleItem::with('product')->find($state);
                                    if (!$saleItem) return;

                                    $set('product_id', $saleItem->product_id);
                                    $set('price',      $saleItem->price);
                                    $set('subtotal',   $saleItem->price * (int) ($get('qty') ?: 1));
                                })
                                ->columnSpan(4),

                            Hidden::make('product_id'),

                            TextInput::make('qty')
                                ->label('Qty')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $price = (float) ($get('price') ?: 0);
                                    $qty   = (int)   ($get('qty')   ?: 1);
                                    $set('subtotal', $price * $qty);
                                })
                                ->columnSpan(2),

                            TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $price = (float) ($get('price') ?: 0);
                                    $qty   = (int)   ($get('qty')   ?: 1);
                                    $set('subtotal', $price * $qty);
                                })
                                ->columnSpan(2),

                            Placeholder::make('subtotal_row')
                                ->label('Subtotal')
                                ->live()
                                ->content(fn(Get $get): HtmlString => new HtmlString(
                                    '<span class="text-sm font-medium">' .
                                        self::rp((float) ($get('price') ?: 0) * (int) ($get('qty') ?: 1)) .
                                        '</span>'
                                ))
                                ->columnSpan(2),

                            Hidden::make('subtotal')->dehydrated(),

                            Textarea::make('reason')
                                ->label('Alasan')
                                ->rows(1)
                                ->nullable()
                                ->columnSpan(4),
                        ])
                        ->columns(10)
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $items = $get('items') ?? [];
                            $set('total_refund', collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0)));
                        })
                        ->addActionLabel('+ Tambah Item')
                        ->minItems(1)
                        ->defaultItems(1),
                ]),

            Section::make('Ringkasan')
                ->schema([
                    Placeholder::make('total_refund_display')
                        ->label('Total Refund')
                        ->live()
                        ->content(fn(Get $get): HtmlString => new HtmlString(
                            '<span class="text-lg font-bold text-primary-600">' .
                                self::rp(self::calcTotal($get)) .
                                '</span>'
                        )),
                ])
                ->columns(1),
        ]);
    }
}
