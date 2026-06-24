<?php

namespace App\Filament\Resources\Sales\Sales\Schemas;

use App\Models\Parties\Customer;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
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

class SaleForm
{
    // Ambil tenant_id yang sedang aktif
    protected static function currentTenantId(): ?int
    {
        return Filament::getTenant()?->id;
    }

    // Format angka ke Rupiah
    protected static function rp(float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    // Jumlahkan subtotal dari semua item
    protected static function calcSubtotal(Get $get): float
    {
        return (float) collect($get('items') ?? [])
            ->sum(fn($item) => (float) ($item['subtotal'] ?? 0));
    }

    // Hitung nilai total keseluruhan
    protected static function calcGrandTotal(Get $get): float
    {
        $subtotal = self::calcSubtotal($get);
        $discount = (float) ($get('discount') ?? 0);
        $tax      = (float) ($get('tax') ?? 0);

        return max(0, $subtotal - $discount + $tax);
    }

    // Hitung nilai kembalian
    protected static function calcChange(Get $get): float
    {
        $total = self::calcGrandTotal($get);
        $paid  = (float) ($get('paid') ?? 0);

        return max(0, $paid - $total);
    }

    // Hitung nilai sisa dibayar
    protected static function calcDue(Get $get): float
    {
        $total = self::calcGrandTotal($get);
        $paid  = (float) ($get('paid') ?? 0);

        return max(0, $total - $paid);
    }

    // Hitung dan set nilai: subtotal, total, change, status
    protected static function updateSummary(Set $set, Get $get): void
    {
        $subtotal = self::calcSubtotal($get);
        $discount = (float) ($get('discount') ?? 0);
        $tax      = (float) ($get('tax') ?? 0);
        $paid     = (float) ($get('paid') ?? 0);

        $total = max(0, $subtotal - $discount + $tax);
        $change   = max(0,$paid - $total);

        $status = match (true) {
            $change == 0 && $total > 0       => Sale::STATUS_PAID,
            $change > 0 || $total > $paid    => Sale::STATUS_PENDING,
            default                          => Sale::STATUS_CANCELLED,
        };

        $set('subtotal', $subtotal);
        $set('total', $total);
        $set('change', $change);
        $set('status', $status);
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
                            ->helperText(function (Get $get) {
                                $customerId = $get('customer_id');
                                if (!$customerId) return null;

                                $customer = \App\Models\Parties\Customer::find($customerId);
                                if (!$customer || $customer->payable_amount <= 0) return null;

                                return '⚠️ Hutang aktif: Rp ' . number_format($customer->payable_amount, 0, ',', '.');
                            })
                            ->live()
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
                                        $set('discount', $discount);

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
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $price       = (float) ($get('price')    ?: 0);
                                        $qty         = (int)   ($get('qty')      ?: 1);
                                        $discount    = (float) ($get('discount') ?: 0);
                                        $rowSubtotal = ($price - $discount) * $qty;

                                        $set('subtotal', $rowSubtotal);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $price       = (float) ($get('price')    ?: 0);
                                        $qty         = (int)   ($get('qty')      ?: 1);
                                        $discount    = (float) ($get('discount') ?: 0);
                                        $rowSubtotal = ($price - $discount) * $qty;
                                        
                                        $set('subtotal', $rowSubtotal);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('discount')
                                    ->label('Diskon/item')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $price       = (float) ($get('price')    ?: 0);
                                        $qty         = (int)   ($get('qty')      ?: 1);
                                        $discount    = (float) ($get('discount') ?: 0);
                                        $rowSubtotal = ($price - $discount) * $qty;
                                        
                                        $set('subtotal', $rowSubtotal);
                                    })
                                    ->columnSpan(2),

                                Placeholder::make('subtotal_row')
                                    ->label('Subtotal')
                                    ->live()
                                    ->content(fn(Get $get): HtmlString => new HtmlString(
                                        '<span class="text-sm font-medium">' .
                                        self::rp((float) ($get('subtotal') ?? 0)) .
                                        '</span>'
                                    ))
                                    ->columnSpan(2),

                                Hidden::make('subtotal')->dehydrated(),
                            ])
                            ->columns(5)
                            ->live()
                            ->addActionLabel('+ Tambah Produk')
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),

                // Total
                Section::make('Rincian Pembayaran')
                    ->schema([
                        Placeholder::make('subtotal')
                            ->label('Subtotal')
                            ->live()
                            ->content(fn(Get $get): HtmlString => new HtmlString(
                                '<span class="text-lg font-bold text-primary-600">' .
                                self::rp(self::calcSubtotal($get)) .
                                '</span>'
                            )),

                        TextInput::make('discount')
                            ->label('Diskon Tambahan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(fn(Get $get)
                                => self::calcGrandTotal($get)),

                        TextInput::make('tax')
                            ->label('Pajak / PPN')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(fn(Get $get)
                                => self::calcGrandTotal($get)),

                        Placeholder::make('total')
                            ->label('Total')
                            ->live()
                            ->content(fn(Get $get): HtmlString => new HtmlString(
                                '<span class="text-lg font-bold text-primary-600">' .
                                self::rp(self::calcGrandTotal($get)) .
                                '</span>'
                            )),

                        TextInput::make('paid')
                            ->label('Uang Diterima')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->live(),

                        Placeholder::make('change')
                            ->label('Kembalian')
                            ->live()
                            ->visible(fn(Get $get) => (float) ($get('paid') ?? 0) > self::calcGrandTotal($get))
                            ->color('info')
                            ->content(fn(Get $get): HtmlString => new HtmlString(
                                '<span class="text-lg font-bold">' .
                                self::rp(self::calcChange($get)) .
                                '</span>'
                            )),

                        Placeholder::make('due')
                            ->label('Sisa Hutang')
                            ->live()
                            ->visible(fn(Get $get) => (float) ($get('paid') ?? 0) < self::calcGrandTotal($get))
                            ->color('danger')
                            ->content(fn(Get $get): HtmlString => new HtmlString(
                                '<span class="text-lg font-bold">' .
                                self::rp(self::calcDue($get)) .
                                '</span>'
                            )),
                        
                        Hidden::make('subtotal')->dehydrated(),
                        Hidden::make('total')->dehydrated(),
                        Hidden::make('change')->dehydrated(),
                        Hidden::make('due')->dehydrated(),
                        Hidden::make('status')->dehydrated(),
                    ])
                    ->columns(3)
                    ->afterStateUpdated(fn(Set $set, Get $get)
                        => self::updateSummary($set, $get)),
            ]);
    }
}
