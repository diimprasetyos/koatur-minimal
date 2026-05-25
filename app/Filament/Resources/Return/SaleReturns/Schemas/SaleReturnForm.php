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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class SaleReturnForm
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

    // Jumlahkan subtotal dari semua item
    protected static function calcTotal(Get $get): float
    {
        return (float) collect($get('items') ?? [])->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
    }

    // Ambil SaleItem hanya jika sale_id milik tenant aktif — cegah manipulasi ID
    protected static function getSaleItemForCurrentTenant(?string $saleItemId, ?string $saleId): ?SaleItem
    {
        if (!$saleItemId || !$saleId) return null;

        $tenantId = self::currentTenantId();
        if (!$tenantId) return null;

        return SaleItem::with('product')
            ->where('id', $saleItemId)
            ->where('sale_id', $saleId)
            ->whereHas('sale', fn($q) => $q->where('tenant_id', $tenantId))
            ->first();
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => self::currentTenantId())->required(),
            Hidden::make('user_id')->default(fn() => auth()->id())->required(),
            Hidden::make('total_refund')->dehydrated(),

            // ── Header Info ──────────────────────────────────────
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
                            name: 'sale',
                            titleAttribute: 'invoice_number',
                            modifyQueryUsing: function (Builder $query) {
                                $tenantId = self::currentTenantId();
                                if ($tenantId) {
                                    $query->where('tenant_id', $tenantId)
                                        ->whereIn('status', [Sale::STATUS_PAID, Sale::STATUS_PENDING]);
                                }
                            }
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

            // ── Items Repeater ───────────────────────────────────
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

                                    $tenantId = self::currentTenantId();
                                    if (!$tenantId) return [];

                                    return SaleItem::with('product')
                                        ->where('sale_id', $saleId)
                                        ->whereHas('sale', fn($q) => $q->where('tenant_id', $tenantId))
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
                                    $saleItem = self::getSaleItemForCurrentTenant($state, $get('../../sale_id'));
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
                                    $set('subtotal', (float) ($get('price') ?: 0) * (int) ($get('qty') ?: 1));
                                })
                                ->columnSpan(2),

                            TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('subtotal', (float) ($get('price') ?: 0) * (int) ($get('qty') ?: 1));
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
                            $set('total_refund', self::calcTotal($get));
                        })
                        ->addActionLabel('+ Tambah Item')
                        ->minItems(1)
                        ->defaultItems(1),
                ]),

            // ── Ringkasan ─────────────────────────────────────────
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
