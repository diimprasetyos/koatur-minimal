<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Schemas;

use App\Models\Adjustment\StockAdjustment;
use App\Models\Product\Product;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
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

class StockAdjustmentForm
{
    // Ambil tenant_id yang sedang aktif
    protected static function currentTenantId(): ?int
    {
        return Filament::getTenant()?->id;
    }

    // Ambil produk hanya milik tenant aktif — hanya untuk display/UX
    protected static function getProductForCurrentTenant(?string $productId): ?Product
    {
        if (!$productId) return null;

        return Product::where('id', $productId)
            ->where('tenant_id', self::currentTenantId())
            ->first();
    }

    // Hitung dan render badge selisih stok (+/- /tidak berubah)
    protected static function diffBadge(int $before, int $after): HtmlString
    {
        $diff = $after - $before;

        $label = match (true) {
            $diff > 0 => '<span class="text-sm font-semibold text-success-600">+' . $diff . ' pcs</span>',
            $diff < 0 => '<span class="text-sm font-semibold text-danger-600">' . $diff . ' pcs</span>',
            default   => '<span class="text-sm text-gray-400">Tidak berubah</span>',
        };

        return new HtmlString($label);
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // FIX: Hidden tenant_id & user_id DIHAPUS dari form.
            // Nilainya di-set di model StockAdjustment::booted() saat creating,
            // sehingga tidak bisa di-spoof lewat browser/DevTools.

            // ── Header ───────────────────────────────────────────
            Section::make('Informasi Penyesuaian')
                ->schema([
                    TextInput::make('reference_number')
                        ->label('No. Referensi')
                        ->disabled()
                        ->placeholder('Otomatis terisi')
                        ->dehydrated(false),

                    DatePicker::make('adjustment_date')
                        ->label('Tanggal Penyesuaian')
                        ->required()
                        ->default(now())
                        ->native(false),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            StockAdjustment::STATUS_DRAFT     => '📝 Draft',
                            StockAdjustment::STATUS_CONFIRMED => '✅ Konfirmasi & Terapkan',
                        ])
                        ->default(StockAdjustment::STATUS_CONFIRMED)
                        ->helperText('Status "Konfirmasi" akan langsung mengupdate stok produk.')
                        ->live(),

                    Textarea::make('notes')
                        ->label('Catatan Umum')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Items ─────────────────────────────────────────────
            Section::make('Item Penyesuaian')
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->options(function (): array {
                                    $tenantId = self::currentTenantId();
                                    if (!$tenantId) return [];

                                    // Hanya produk aktif + track stock milik tenant aktif
                                    return Product::where('tenant_id', $tenantId)
                                        ->where('is_active', true)
                                        ->where('track_stock', true)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    // Hanya untuk UX/display — validasi sesungguhnya ada di model
                                    $product = self::getProductForCurrentTenant($state);
                                    if (!$product) {
                                        $set('stock_before', 0);
                                        $set('stock_after', 0);
                                        return;
                                    }

                                    $set('stock_before', $product->stock);
                                    $set('stock_after',  $product->stock);
                                })
                                ->columnSpan(2),

                            TextInput::make('stock_before')
                                ->label('Stok Saat Ini')
                                ->numeric()
                                ->disabled()
                                // FIX: dehydrated(false) — nilai ini TIDAK dikirim ke server.
                                // stock_before akan diambil langsung dari DB di StockAdjustmentItem::saving().
                                ->dehydrated(false)
                                ->suffix('pcs')
                                ->columnSpan(2),

                            TextInput::make('stock_after')
                                ->label('Stok Baru')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->live()
                                ->suffix('pcs')
                                ->helperText('Masukkan jumlah stok yang benar')
                                ->columnSpan(2),

                            // Tampil selisih secara realtime (hanya display, bukan acuan server)
                            Placeholder::make('diff_display')
                                ->label('Selisih')
                                ->live()
                                ->content(fn(Get $get): HtmlString => self::diffBadge(
                                    (int) ($get('stock_before') ?? 0),
                                    (int) ($get('stock_after')  ?? 0),
                                ))
                                ->columnSpan(2),

                            // FIX: qty_difference & type tidak perlu dikirim dari form —
                            // keduanya dihitung ulang di StockAdjustmentItem::saving()
                            // Hidden::make('qty_difference') — DIHAPUS
                            // Hidden::make('type') — DIHAPUS

                            Textarea::make('notes')
                                ->label('Catatan Item')
                                ->rows(1)
                                ->placeholder('Contoh: Hasil stock opname, kerusakan, dll.')
                                ->nullable()
                                ->columnSpan(4),
                        ])
                        ->columns(4)
                        ->live()
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

            // ── Summary ───────────────────────────────────────────
            Section::make('Ringkasan')
                ->schema([
                    Placeholder::make('summary_display')
                        ->label('')
                        ->live()
                        ->content(function (Get $get): HtmlString {
                            $items = $get('items') ?? [];
                            $adds = $subs = 0;

                            foreach ($items as $item) {
                                $diff = (int) ($item['stock_after'] ?? 0) - (int) ($item['stock_before'] ?? 0);
                                if ($diff > 0) $adds++;
                                elseif ($diff < 0) $subs++;
                            }

                            $noChange = count($items) - $adds - $subs;

                            return new HtmlString(
                                '<div class="flex gap-6 text-sm">' .
                                    '<span class="text-success-600 font-medium">✅ Penambahan: ' . $adds . ' produk</span>' .
                                    '<span class="text-danger-600 font-medium">❌ Pengurangan: ' . $subs . ' produk</span>' .
                                    '<span class="text-gray-500">— Tidak berubah: ' . $noChange . ' produk</span>' .
                                    '</div>'
                            );
                        })
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }
}
