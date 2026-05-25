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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class PurchaseReturnForm
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

    // Ambil Purchase hanya milik tenant aktif — cegah manipulasi ID
    protected static function getPurchaseForCurrentTenant(?string $purchaseId): ?Purchase
    {
        if (!$purchaseId) return null;

        $tenantId = self::currentTenantId();
        if (!$tenantId) return null;

        return Purchase::where('id', $purchaseId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    // Ambil PurchaseItem hanya jika purchase_id milik tenant aktif
    protected static function getPurchaseItemForCurrentTenant(?string $itemId, ?string $purchaseId): ?PurchaseItem
    {
        if (!$itemId || !$purchaseId) return null;

        $tenantId = self::currentTenantId();
        if (!$tenantId) return null;

        return PurchaseItem::with('product')
            ->where('id', $itemId)
            ->where('purchase_id', $purchaseId)
            ->whereHas('purchase', fn($q) => $q->where('tenant_id', $tenantId))
            ->first();
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => self::currentTenantId())->required(),
            Hidden::make('user_id')->default(fn() => auth()->id())->required(),
            Hidden::make('total_return')->dehydrated(),

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

                    Select::make('purchase_id')
                        ->label('No. PO Pembelian')
                        ->preload()
                        ->relationship(
                            name: 'purchase',
                            titleAttribute: 'reference_number',
                            modifyQueryUsing: function (Builder $query) {
                                $tenantId = self::currentTenantId();
                                if ($tenantId) {
                                    $query->where('tenant_id', $tenantId)
                                        ->whereIn('status', [Purchase::STATUS_RECEIVED, Purchase::STATUS_PARTIAL]);
                                }
                            }
                        )
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $purchase = self::getPurchaseForCurrentTenant($state);
                            $set('supplier_id', $purchase?->supplier_id);
                            $set('items', []);
                        })
                        ->columnSpan(1),

                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship(
                            name: 'supplier',
                            titleAttribute: 'name',
                            modifyQueryUsing: function (Builder $query) {
                                $tenantId = self::currentTenantId();
                                if ($tenantId) {
                                    $query->where('tenant_id', $tenantId);
                                }
                            }
                        )
                        ->searchable()
                        ->nullable()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            PurchaseReturn::STATUS_APPROVED => 'Disetujui',
                            PurchaseReturn::STATUS_PENDING  => 'Menunggu',
                            PurchaseReturn::STATUS_REJECTED => 'Ditolak',
                        ])
                        ->default(PurchaseReturn::STATUS_APPROVED),

                    Select::make('return_method')
                        ->label('Metode Retur')
                        ->required()
                        ->options([
                            'debit_note'  => '📋 Debit Note',
                            'refund'      => '💵 Refund Tunai',
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

            // ── Items Repeater ───────────────────────────────────
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
                                    if (!$purchaseId) return [];

                                    $tenantId = self::currentTenantId();
                                    if (!$tenantId) return [];

                                    return PurchaseItem::with('product')
                                        ->where('purchase_id', $purchaseId)
                                        ->whereHas('purchase', fn($q) => $q->where('tenant_id', $tenantId))
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
                                    $purchaseItem = self::getPurchaseItemForCurrentTenant($state, $get('../../purchase_id'));
                                    if (!$purchaseItem) return;

                                    $set('product_id', $purchaseItem->product_id);
                                    $set('cost_price', $purchaseItem->cost_price);
                                    $set('subtotal',   $purchaseItem->cost_price * (int) ($get('qty') ?: 1));
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
                                    $set('subtotal', (float) ($get('cost_price') ?: 0) * (int) ($get('qty') ?: 1));
                                })
                                ->columnSpan(2),

                            TextInput::make('cost_price')
                                ->label('Harga Beli')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('subtotal', (float) ($get('cost_price') ?: 0) * (int) ($get('qty') ?: 1));
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
                            $set('total_return', self::calcTotal($get));
                        })
                        ->addActionLabel('+ Tambah Item')
                        ->minItems(1)
                        ->defaultItems(1),
                ]),

            // ── Ringkasan ─────────────────────────────────────────
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
