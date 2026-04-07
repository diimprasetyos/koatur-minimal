<?php

namespace App\Filament\Resources\Purchases\Purchases\Schemas;

use App\Models\Parties\Supplier;
use App\Models\Product\Product;
use App\Models\Purchases\Purchase;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PurchaseForm
{
    // ─── Calculation Helpers ─────────────────────────────────────

    /**
     * Hitung subtotal row + update header totals sekaligus.
     * Dipanggil dari field cost_price dan qty di dalam Repeater row.
     */
    protected static function recalculateAll(Set $set, Get $get): void
    {
        // 1. Subtotal row ini
        $costPrice = (float) ($get('cost_price') ?: 0);
        $qty = (int) ($get('qty') ?: 1);
        $rowSubtotal = $costPrice * $qty;
        $set('subtotal', $rowSubtotal);

        // 2. Header totals dari semua items (nilai lama, update setelah re-render)
        $items = $get('../../items') ?? [];
        $allSubtotal = collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
        $discountHeader = (float) ($get('../../discount') ?: 0);
        $tax = (float) ($get('../../tax') ?: 0);
        $total = max(0, $allSubtotal - $discountHeader + $tax);
        $paid = (float) ($get('../../paid') ?: 0);
        $due = max(0, $total - $paid);

        $set('../../subtotal', $allSubtotal);
        $set('../../total', $total);
        $set('../../due', $due);
        $set('../../payment_status', match (true) {
            $due <= 0 => Purchase::PAYMENT_PAID,
            $paid > 0 => Purchase::PAYMENT_PARTIAL,
            default => Purchase::PAYMENT_UNPAID,
        });
    }

    /**
     * Recalculate header totals saja (tambah/hapus item, atau ubah diskon/pajak/paid).
     */
    protected static function recalculateTotals(Set $set, Get $get): void
    {
        $items = $get('items') ?? [];
        $subtotal = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('discount') ?: 0);
        $tax = (float) ($get('tax') ?: 0);
        $total = max(0, $subtotal - $discount + $tax);
        $paid = (float) ($get('paid') ?: 0);
        $due = max(0, $total - $paid);

        $set('subtotal', $subtotal);
        $set('total', $total);
        $set('due', $due);
        $set('payment_status', match (true) {
            $due <= 0 => Purchase::PAYMENT_PAID,
            $paid > 0 => Purchase::PAYMENT_PARTIAL,
            default => Purchase::PAYMENT_UNPAID,
        });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(fn() => Filament::getTenant()?->id)
                    ->required(),

                Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->required(),

                // ── Header Info ──────────────────────────────────────
                Section::make('Informasi Pembelian')
                    ->schema([
                        TextInput::make('reference_number')
                            ->label('No. Referensi')
                            ->disabled()
                            ->placeholder('Otomatis terisi')
                            ->dehydrated(false),

                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->required()
                            ->default(now())
                            ->native(false),

                        DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->nullable()
                            ->native(false)
                            ->afterOrEqual('purchase_date'),

                        Select::make('supplier_id')
                            ->label('Pemasok')
                            ->relationship(
                                'supplier',
                                'name',
                                fn($query) => $query->where('tenant_id', Filament::getTenant()?->id)
                            )
                            ->searchable()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('name')->label('Nama Supplier')->required(),
                                TextInput::make('phone')->label('No. HP')->nullable(),
                                TextInput::make('email')->label('Email')->email()->nullable(),
                                Textarea::make('address')->label('Alamat')->rows(2),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Supplier::create([
                                    ...$data,
                                    'tenant_id' => Filament::getTenant()?->id,
                                ])->id;
                            }),

                        TextInput::make('supplier_invoice')
                            ->label('No. Invoice Supplier')
                            ->placeholder('INV-SUP-001')
                            ->nullable(),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                Purchase::STATUS_DRAFT => 'Draft',
                                Purchase::STATUS_ORDERED => 'Dipesan',
                                Purchase::STATUS_RECEIVED => 'Diterima',
                                Purchase::STATUS_PARTIAL => 'Sebagian Diterima',
                                Purchase::STATUS_CANCELLED => 'Dibatalkan',
                            ])
                            ->default(Purchase::STATUS_RECEIVED)
                            ->live(),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => '💵 Cash',
                                'transfer' => '🏦 Transfer',
                                'credit' => '💳 Kredit',
                            ])
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ── Items Repeater ───────────────────────────────────
                Section::make('Item Produk')
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn($query) => $query
                                            ->where('tenant_id', Filament::getTenant()?->id)
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        if (!$state)
                                            return;
                                        $product = Product::find($state);
                                        if (!$product)
                                            return;

                                        $set('cost_price', $product->cost_price ?? 0);
                                        $set('qty_received', 0); // reset hanya saat produk diganti
                            
                                        $qty = (int) ($get('qty') ?: 1);
                                        $rowSubtotal = ($product->cost_price ?? 0) * $qty;
                                        $set('subtotal', $rowSubtotal);

                                        // Update header
                                        $items = $get('../../items') ?? [];
                                        $allSubtotal = collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
                                        $discountHeader = (float) ($get('../../discount') ?: 0);
                                        $tax = (float) ($get('../../tax') ?: 0);
                                        $total = max(0, $allSubtotal - $discountHeader + $tax);
                                        $paid = (float) ($get('../../paid') ?: 0);
                                        $due = max(0, $total - $paid);

                                        $set('../../subtotal', $allSubtotal);
                                        $set('../../total', $total);
                                        $set('../../due', $due);
                                        $set('../../payment_status', match (true) {
                                            $due <= 0 => Purchase::PAYMENT_PAID,
                                            $paid > 0 => Purchase::PAYMENT_PARTIAL,
                                            default => Purchase::PAYMENT_UNPAID,
                                        });
                                    })
                                    ->columnSpan(4),

                                TextInput::make('cost_price')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateAll($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Qty Pesan')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateAll($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('qty_received')
                                    ->label('Qty Terima')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(fn(Get $get) => (int) ($get('qty') ?: 1))
                                    ->helperText('Qty yang sudah diterima')
                                    ->columnSpan(2)
                                    ->disabled(fn(Get $get) => $get('../../status') !== Purchase::STATUS_PARTIAL),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),
                            ])
                            ->columns(5)
                            ->live()
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get))
                            ->addActionLabel('+ Tambah Produk')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                Product::find($state['product_id'] ?? null)?->name ?? 'Produk baru'
                            ),
                    ]),

                // ── Totals ───────────────────────────────────────────
                Section::make('Rincian Pembayaran')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('discount')
                            ->label('Diskon')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get)),

                        TextInput::make('tax')
                            ->label('Pajak / PPN')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get)),

                        TextInput::make('total')
                            ->label('Total')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'font-bold text-lg']),

                        TextInput::make('paid')
                            ->label('Dibayar')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get)),

                        TextInput::make('due')
                            ->label('Sisa Hutang')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'text-red-600 font-semibold']),

                        Hidden::make('payment_status')
                            ->dehydrated(),
                    ])
                    ->columns(3),
            ]);
    }
}
