<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Schemas;

use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseItem;
use App\Models\Return\PurchaseReturn;
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

class PurchaseReturnForm
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
            Hidden::make('total_return')->dehydrated(),

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

                    Select::make('purchase_id')
                        ->label('No. PO Pembelian')
                        ->preload()
                        ->relationship(
                            'purchase',
                            'reference_number',
                            fn($q) => $q?->where('tenant_id', Filament::getTenant()?->id)
                                    ?->whereIn('status', [Purchase::STATUS_RECEIVED, Purchase::STATUS_PARTIAL])
                        )
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                            // Auto-fill supplier dari purchase yang dipilih
                            if ($state) {
                                $purchase = Purchase::find($state);
                                if ($purchase) {
                                    $set('supplier_id', $purchase->supplier_id);
                                }
                            }
                            $set('items', []);
                        })
                        ->columnSpan(1),

                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship(
                            'supplier',
                            'name',
                            fn($q) => $q?->where('tenant_id', Filament::getTenant()?->id)
                        )
                        ->searchable()
                        ->nullable()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            PurchaseReturn::STATUS_APPROVED => 'Disetujui',
                            PurchaseReturn::STATUS_PENDING => 'Menunggu',
                            PurchaseReturn::STATUS_REJECTED => 'Ditolak',
                        ])
                        ->default(PurchaseReturn::STATUS_APPROVED),

                    Select::make('return_method')
                        ->label('Metode Retur')
                        ->required()
                        ->options([
                            'debit_note' => '📋 Debit Note',
                            'refund' => '💵 Refund Tunai',
                            'replacement' => '🔄 Penggantian Barang',
                        ])
                        ->default('debit_note'),

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
                            Select::make('purchase_item_id')
                                ->label('Item Pembelian')
                                ->options(function (Get $get): array {
                                    $purchaseId = $get('../../purchase_id');
                                    if (!$purchaseId)
                                        return [];

                                    return PurchaseItem::with('product')
                                        ->where('purchase_id', $purchaseId)
                                        ->get()
                                        ->mapWithKeys(fn($item) => [
                                            $item->id => ($item->product->name ?? '-') .
                                                ' (Qty: ' . $item->qty . ' @ Rp ' .
                                                number_format($item->cost_price, 0, ',', '.') . ')'
                                        ])
                                        ->toArray();
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                    if (!$state)
                                        return;
                                    $purchaseItem = PurchaseItem::with('product')->find($state);
                                    if (!$purchaseItem)
                                        return;

                                    $set('product_id', $purchaseItem->product_id);
                                    $set('cost_price', $purchaseItem->cost_price);
                                    $set('subtotal', $purchaseItem->cost_price * (int) ($get('qty') ?: 1));
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
                                    $cost = (float) ($get('cost_price') ?: 0);
                                    $qty = (int) ($get('qty') ?: 1);
                                    $set('subtotal', $cost * $qty);
                                })
                                ->columnSpan(2),

                            TextInput::make('cost_price')
                                ->label('Harga Beli')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $cost = (float) ($get('cost_price') ?: 0);
                                    $qty = (int) ($get('qty') ?: 1);
                                    $set('subtotal', $cost * $qty);
                                })
                                ->columnSpan(2),

                            Placeholder::make('subtotal_row')
                                ->label('Subtotal')
                                ->live()
                                ->content(fn(Get $get): HtmlString => new HtmlString(
                                    '<span class="text-sm font-medium">' .
                                    self::rp((float) ($get('cost_price') ?: 0) * (int) ($get('qty') ?: 1)) .
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
                            $set('total_return', collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0)));
                        })
                        ->addActionLabel('+ Tambah Item')
                        ->minItems(1)
                        ->defaultItems(1),
                ]),

            Section::make('Ringkasan')
                ->schema([
                    Placeholder::make('total_return_display')
                        ->label('Total Nilai Retur')
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
