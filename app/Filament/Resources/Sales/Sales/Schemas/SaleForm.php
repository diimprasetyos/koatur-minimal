<?php

namespace App\Filament\Resources\Sales\Sales\Schemas;

use App\Models\Parties\Customer;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;


class SaleForm
{

    // ─── Calculation Helpers ─────────────────────────────────────

    protected static function recalculateSubtotal(Set $set, Get $get): void
    {
        $price    = (float) ($get('price') ?: 0);
        $qty      = (int) ($get('qty') ?: 1);
        $discount = (float) ($get('discount') ?: 0);
        $set('subtotal', ($price - $discount) * $qty);
    }

    protected static function recalculateTotals(Set $set, Get $get): void
    {
        $items = $get('items') ?? [];

        $subtotal = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('discount') ?: 0);
        $tax      = (float) ($get('tax') ?: 0);
        $total    = $subtotal - $discount + $tax;

        $set('subtotal', $subtotal);
        $set('total', max(0, $total));

        // Update kembalian
        $paid   = (float) ($get('paid') ?: 0);
        $change = $paid - $total;
        $set('change', max(0, $change));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Header Info ──────────────────────────────────────
                Section::make('Informasi Transaksi')
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->disabled()
                            ->placeholder('Otomatis terisi')
                            ->dehydrated(false),

                        Select::make('customer_id')
                            ->label('Pelanggan (Opsional)')
                            ->options(fn() => Customer::where('tenant_id', auth()->user()->tenant_id)
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('name')->label('Nama')->required(),
                                TextInput::make('phone')->label('No. HP')->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Customer::create([
                                    ...$data,
                                    'tenant_id' => auth()->user()->tenant_id,
                                ])->id;
                            }),

                        Select::make('payment_method')
                            ->label('Metode Bayar')
                            ->required()
                            ->options([
                                Sale::PAYMENT_CASH     => '💵 Cash',
                                Sale::PAYMENT_TRANSFER => '🏦 Transfer',
                                Sale::PAYMENT_EWALLET  => '📱 E-Wallet',
                            ])
                            ->default(Sale::PAYMENT_CASH)
                            ->live(),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                Sale::STATUS_PAID      => 'Lunas',
                                Sale::STATUS_PENDING   => 'Belum Lunas',
                                Sale::STATUS_CANCELLED => 'Dibatalkan',
                            ])
                            ->default(Sale::STATUS_PAID),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->nullable()
                            ->columnSpan(2),
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
                                    ->options(fn() => Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('is_active', true)
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        if (! $state) return;

                                        $product = Product::find($state);
                                        if (! $product) return;

                                        $set('price', $product->price);
                                        $set('cost_price', $product->cost_price);
                                        $set('product_name', $product->name);

                                        // Hitung ulang subtotal
                                        $qty = (int) ($get('qty') ?: 1);
                                        $discount = (float) ($get('discount') ?: 0);
                                        $set('subtotal', ($product->price - $discount) * $qty);
                                    })
                                    ->columnSpan(3),

                                Hidden::make('product_name'),
                                Hidden::make('cost_price'),

                                TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateSubtotal($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateSubtotal($set, $get))
                                    ->columnSpan(1),

                                TextInput::make('discount')
                                    ->label('Diskon/item')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateSubtotal($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),
                            ])
                            ->columns(10)
                            ->live()
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get))
                            ->addActionLabel('+ Tambah Produk')
                            ->minItems(1),
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
                            ->label('Diskon Tambahan')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get)),

                        TextInput::make('tax')
                            ->label('Pajak / PPN')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotals($set, $get)),

                        TextInput::make('total')
                            ->label('Total Bayar')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'font-bold text-lg']),

                        TextInput::make('paid')
                            ->label('Uang Diterima')
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $change = (float) $get('paid') - (float) $get('total');
                                $set('change', max(0, $change));
                            }),

                        TextInput::make('change')
                            ->label('Kembalian')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }
}
