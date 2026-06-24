<?php

namespace App\Filament\Resources\Purchases\Purchases\Schemas;

use App\Models\Parties\Supplier;
use App\Models\Product\Product;
use App\Models\Purchases\Purchase;
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

class PurchaseForm
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

    // Ambil produk hanya milik tenant aktif — cegah manipulasi ID dari luar
    protected static function getProductForCurrentTenant(?string $productId): ?Product
    {
        if (!$productId) return null;

        return Product::where('id', $productId)
            ->where('tenant_id', self::currentTenantId())
            ->first();
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

    // Hitung nilai sisa dibayar
    protected static function calcDue(Get $get): float
    {
        $total = self::calcGrandTotal($get);
        $paid  = (float) ($get('paid') ?? 0);

        return max(0, $total - $paid);
    }

    // Hitung nilai kembalian
    protected static function calcChange(Get $get): float
    {
        $total = self::calcGrandTotal($get);
        $paid  = (float) ($get('paid') ?? 0);

        return max(0, $paid - $total);
    }

    // Hitung dan set nilai: subtotal, total, due, payment_status
    protected static function updatePaymentSummary(Set $set, Get $get): void
    {
        $subtotal = self::calcSubtotal($get);
        $discount = (float) ($get('discount') ?? 0);
        $tax      = (float) ($get('tax') ?? 0);
        $paid     = (float) ($get('paid') ?? 0);

        $total = max(0, $subtotal - $discount + $tax);
        // $due   = $total - $paid;
        $due   = max(0, $total - $paid);

        $paymentStatus = match (true) {
            $due <= 0 && $total > 0 => Purchase::PAYMENT_PAID,
            $paid > 0               => Purchase::PAYMENT_PARTIAL,
            default                 => Purchase::PAYMENT_UNPAID,
        };

        $set('subtotal', $subtotal);
        $set('total', $total);
        $set('due', $due);
        $set('payment_status', $paymentStatus);
    }

    protected static function refreshTotals(Set $set, Get $get): void
    {
        $items = $get('../../items') ?? [];

        $subtotal = collect($items)
            ->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));

        $discount = (float) ($get('../../discount') ?? 0);
        $tax      = (float) ($get('../../tax') ?? 0);
        $paid     = (float) ($get('../../paid') ?? 0);

        $total = max(0, $subtotal - $discount + $tax);
        $due   = max(0, $total - $paid);

        $paymentStatus = match (true) {
            $due <= 0 && $total > 0 => Purchase::PAYMENT_PAID,
            $paid > 0               => Purchase::PAYMENT_PARTIAL,
            default                 => Purchase::PAYMENT_UNPAID,
        };

        $set('../../subtotal', $subtotal);
        $set('../../total', $total);
        $set('../../due', $due);
        $set('../../payment_status', $paymentStatus);
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => self::currentTenantId())->required(),
            Hidden::make('user_id')->default(fn() => auth()->id())->required(),

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
                            // Filter supplier hanya milik tenant aktif
                            fn($query) => $query->where('tenant_id', self::currentTenantId())
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
                            // Paksa tenant_id saat create supplier baru
                            return Supplier::create([
                                ...$data,
                                'tenant_id' => self::currentTenantId(),
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
                            Purchase::STATUS_DRAFT     => 'Draft',
                            Purchase::STATUS_ORDERED   => 'Dipesan',
                            Purchase::STATUS_RECEIVED  => 'Diterima',
                            Purchase::STATUS_PARTIAL   => 'Sebagian Diterima',
                            Purchase::STATUS_CANCELLED => 'Dibatalkan',
                        ])
                        ->default(Purchase::STATUS_RECEIVED)
                        ->live(),

                    Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->options([
                            'cash'     => '💵 Cash',
                            'transfer' => '🏦 Transfer',
                            'credit'   => '💳 Kredit',
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
                        ->live(debounce: 300)
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->relationship(
                                    'product',
                                    'name',
                                    // Filter produk hanya milik tenant aktif
                                    fn($query) => $query->where('tenant_id', self::currentTenantId())
                                )
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                    // Validasi produk terhadap tenant sebelum auto-fill harga
                                    $product = self::getProductForCurrentTenant($state);
                                    if (!$product) return;

                                    $rowSubtotal = ($product->cost_price ?? 0) * (int) ($get('qty') ?: 1);

                                    $set('cost_price',    $product->cost_price ?? 0);
                                    $set('qty_received',  0);
                                    $set('subtotal', $rowSubtotal);

                                    self::refreshTotals($set, $get);
                                })
                                ->columnSpan(4),

                            TextInput::make('cost_price')
                                ->label('Harga Beli')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $rowSubtotal = (float) ($get('cost_price') ?: 0) * (int) ($get('qty') ?: 1);

                                    $set('subtotal', $rowSubtotal);

                                    self::refreshTotals($set, $get);
                                })
                                ->columnSpan(2),

                            TextInput::make('qty')
                                ->label('Qty Pesan')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $rowSubtotal = (float) ($get('cost_price') ?: 0) * (int) ($get('qty') ?: 1);

                                    $set('subtotal', $rowSubtotal);

                                    self::refreshTotals($set, $get);
                                })
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
                        ->addActionLabel('+ Tambah Produk')
                        ->minItems(1)
                        ->defaultItems(1)
                        ->collapsible()
                        ->itemLabel(
                            // Label item pakai nama produk milik tenant aktif saja
                            fn(array $state): ?string => self::getProductForCurrentTenant(
                                $state['product_id'] ?? null
                            )?->name ?? 'Produk baru'
                        ),
                ]),

            // ── Totals ───────────────────────────────────────────
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
                        ->label('Diskon')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->minValue(0)
                        ->live()
                        ->afterStateUpdated(fn(Get $get) => self::calcGrandTotal($get)),

                    TextInput::make('tax')
                        ->label('Pajak / PPN')
                        ->prefix('Rp')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->live()
                        ->afterStateUpdated(fn(Get $get) => self::calcGrandTotal($get)),

                    Placeholder::make('total')
                        ->label('Total')
                        ->live()
                        ->content(fn(Get $get): HtmlString => new HtmlString(
                            '<span class="text-lg font-bold text-primary-600">' .
                            self::rp(self::calcGrandTotal($get)) .
                            '</span>'
                        )),

                    TextInput::make('paid')
                        ->label('Dibayar')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->minValue(0)
                        ->live(),

                    Placeholder::make('due_display')
                        ->label('Sisa Hutang')
                        ->live()
                        ->visible(fn(Get $get) => (float) ($get('paid') ?? 0) < self::calcGrandTotal($get))
                        ->color('danger')
                        ->content(fn(Get $get): HtmlString => new HtmlString(
                            '<span class="text-lg font-bold">' .
                            self::rp(self::calcDue($get)) .
                            '</span>'
                        )),

                    Placeholder::make('change_display')
                        ->label('Kembalian')
                        ->live()
                        ->visible(fn(Get $get) => (float) ($get('paid') ?? 0) > self::calcGrandTotal($get))
                        ->color('info')
                        ->content(fn(Get $get): HtmlString => new HtmlString(
                            '<span class="text-lg font-bold">' .
                            self::rp(self::calcChange($get)) .
                            '</span>'
                        )),

                    Hidden::make('subtotal')->dehydrated(),
                    Hidden::make('total')->dehydrated(),
                    Hidden::make('due')->dehydrated(),
                    Hidden::make('payment_status')->dehydrated(),
                ])
                ->columns(3)
                ->afterStateUpdated(fn(Set $set, Get $get)
                    => self::updatePaymentSummary($set, $get)),
        ]);
    }
}
