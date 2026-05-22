<?php

namespace App\Filament\Resources\Sales\Sales\Schemas;

use App\Models\Parties\Customer;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
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

class SaleForm
{
    // Ambil tenant_id yang sedang aktif
    protected static function currentTenantId(): ?int
    {
        return Filament::getTenant()?->id;
    }

    // Hitung subtotal row item + update total header
    protected static function recalculateAll(Set $set, Get $get): void
    {
        $price       = (float) ($get('price')    ?: 0);
        $qty         = (int)   ($get('qty')      ?: 1);
        $discount    = (float) ($get('discount') ?: 0);
        $rowSubtotal = ($price - $discount) * $qty;
        $set('subtotal', $rowSubtotal);

        $items          = $get('../../items') ?? [];
        $allSubtotal    = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));
        $discountHeader = (float) ($get('../../discount') ?: 0);
        $tax            = (float) ($get('../../tax')      ?: 0);
        $total          = max(0, $allSubtotal - $discountHeader + $tax);

        $set('../../subtotal', $allSubtotal);
        $set('../../total',    $total);

        $paid = (float) ($get('../../paid') ?: 0);
        $set('../../change', max(0, $paid - $total));
    }

    // Hitung ulang total header saja (dipanggil dari field diskon, pajak, paid)
    protected static function recalculateTotals(Set $set, Get $get): void
    {
        $items    = $get('items') ?? [];
        $subtotal = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('discount') ?: 0);
        $tax      = (float) ($get('tax')      ?: 0);
        $total    = max(0, $subtotal - $discount + $tax);

        $set('subtotal', $subtotal);
        $set('total',    $total);

        $paid = (float) ($get('paid') ?: 0);
        $set('change', max(0, $paid - $total));
    }

    // Ambil data produk hanya milik tenant aktif (mencegah kebocoran antar tenant)
    protected static function getProductForCurrentTenant(?string $productId): ?Product
    {
        if (!$productId) return null;

        return Product::where('id', $productId)
            ->where('tenant_id', self::currentTenantId())
            ->first();
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(fn() => self::currentTenantId())
                    ->required(),

                Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->required(),

                // Header
                Section::make('Informasi Transaksi')
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->disabled()
                            ->placeholder('Otomatis terisi')
                            ->dehydrated(false),

                        DatePicker::make('sale_date')
                            ->label('Tanggal Penjualan')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Select::make('customer_id')
                            ->label('Pelanggan (Opsional)')
                            ->relationship(
                                'customer',
                                'name',
                                // Filter customer hanya milik tenant aktif
                                fn($query) => $query->where('tenant_id', self::currentTenantId())
                            )
                            ->searchable()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('name')->label('Nama')->required(),
                                TextInput::make('phone')->label('No. HP')->nullable(),
                                TextInput::make('email')->label('Email')->email()->nullable(),
                                Textarea::make('address')->label('Alamat')->rows(2),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                // Paksa tenant_id saat create customer baru
                                return Customer::create([
                                    ...$data,
                                    'tenant_id' => self::currentTenantId(),
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
                            ->default(Sale::PAYMENT_CASH),

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
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Repeater
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
                                        // Filter produk hanya milik tenant aktif
                                        fn($query) => $query
                                            ->where('tenant_id', self::currentTenantId())
                                            ->where('is_active', true)
                                            ->where('stock', '>', 0)
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        // Ambil produk dengan validasi tenant untuk cegah manipulasi ID
                                        $product = self::getProductForCurrentTenant($state);
                                        if (!$product) return;

                                        $set('price',        $product->price);
                                        $set('cost_price',   $product->cost_price);
                                        $set('product_name', $product->name);

                                        $qty         = (int)   ($get('qty')      ?: 1);
                                        $discount    = (float) ($get('discount') ?: 0);
                                        $rowSubtotal = ($product->price - $discount) * $qty;
                                        $set('subtotal', $rowSubtotal);

                                        // Update header totals setelah produk dipilih
                                        $items          = $get('../../items') ?? [];
                                        $allSubtotal    = collect($items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
                                        $discountHeader = (float) ($get('../../discount') ?: 0);
                                        $tax            = (float) ($get('../../tax')      ?: 0);
                                        $total          = max(0, $allSubtotal - $discountHeader + $tax);
                                        $set('../../subtotal', $allSubtotal);
                                        $set('../../total',    $total);
                                        $paid = (float) ($get('../../paid') ?: 0);
                                        $set('../../change', max(0, $paid - $total));
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
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateAll($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateAll($set, $get))
                                    ->columnSpan(1),

                                TextInput::make('discount')
                                    ->label('Diskon/item')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateAll($set, $get))
                                    ->columnSpan(2),

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
                            ->defaultItems(1),
                    ]),

                // Total
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
                                // Hitung kembalian setiap kali uang diterima berubah
                                $paid  = (float) ($get('paid')  ?: 0);
                                $total = (float) ($get('total') ?: 0);
                                $set('change', max(0, $paid - $total));
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
