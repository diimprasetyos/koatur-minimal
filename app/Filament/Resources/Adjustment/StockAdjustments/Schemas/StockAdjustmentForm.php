<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Schemas;

use App\Models\Adjustment\StockAdjustment;
use App\Models\Product\Product;
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

class StockAdjustmentForm
{
    protected static function rp(int $n): string
    {
        return number_format($n, 0, ',', '.');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => Filament::getTenant()?->id)->required(),
            Hidden::make('user_id')->default(fn() => auth()->id())->required(),

            // ── Header ──────────────────────────────────────────────────
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
                            StockAdjustment::STATUS_DRAFT => '📝 Draft',
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

            // ── Items ────────────────────────────────────────────────────
            Section::make('Item Penyesuaian')
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->relationship()
                        ->schema([

                            // Pilih produk
                            Select::make('product_id')
                                ->label('Produk')
                                ->options(function (): array {
                                    $tenantId = Filament::getTenant()?->id;

                                    if (!$tenantId)
                                        return [];

                                    return Product::query()
                                        ->where('tenant_id', $tenantId)
                                        ->where('is_active', true)
                                        ->where('track_stock', true)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    if (!$state)
                                        return;
                                    $product = Product::find($state);
                                    if (!$product)
                                        return;

                                    $set('stock_before', $product->stock);
                                    $set('stock_after', $product->stock);
                                })
                                ->columnSpan(2),

                            // Stok saat ini (read-only, auto-fill saat produk dipilih)
                            TextInput::make('stock_before')
                                ->label('Stok Saat Ini')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->suffix('pcs')
                                ->columnSpan(2),

                            // Stok setelah adjustment (input utama user)
                            TextInput::make('stock_after')
                                ->label('Stok Baru')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->live()
                                ->suffix('pcs')
                                ->helperText('Masukkan jumlah stok yang benar')
                                ->columnSpan(2),

                            // Selisih: tampil otomatis (Placeholder, realtime)
                            Placeholder::make('diff_display')
                                ->label('Selisih')
                                ->live()
                                ->content(function (Get $get): HtmlString {
                                    $before = (int) ($get('stock_before') ?? 0);
                                    $after = (int) ($get('stock_after') ?? 0);
                                    $diff = $after - $before;

                                    if ($diff > 0) {
                                        $label = '<span class="text-sm font-semibold text-success-600">+' . $diff . ' pcs</span>';
                                    } elseif ($diff < 0) {
                                        $label = '<span class="text-sm font-semibold text-danger-600">' . $diff . ' pcs</span>';
                                    } else {
                                        $label = '<span class="text-sm text-gray-400">Tidak berubah</span>';
                                    }

                                    return new HtmlString($label);
                                })
                                ->columnSpan(2),

                            // Hidden fields yang disimpan ke DB
                            Hidden::make('qty_difference')->dehydrated(),
                            Hidden::make('type')->dehydrated(),

                            // Catatan per item
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
                            fn(array $state): ?string =>
                            Product::find($state['product_id'] ?? null)?->name ?? 'Produk baru'
                        ),
                ]),

            // ── Summary ──────────────────────────────────────────────────
            Section::make('Ringkasan')
                ->schema([
                    Placeholder::make('summary_display')
                        ->label('')
                        ->live()
                        ->content(function (Get $get): HtmlString {
                            $items = $get('items') ?? [];
                            $adds = 0;
                            $subtracts = 0;
                            $total = count($items);

                            foreach ($items as $item) {
                                $diff = (int) ($item['stock_after'] ?? 0) - (int) ($item['stock_before'] ?? 0);
                                if ($diff > 0)
                                    $adds++;
                                elseif ($diff < 0)
                                    $subtracts++;
                            }

                            $noChange = $total - $adds - $subtracts;

                            return new HtmlString(
                                '<div class="flex gap-6 text-sm">' .
                                '<span class="text-success-600 font-medium">✅ Penambahan: ' . $adds . ' produk</span>' .
                                '<span class="text-danger-600 font-medium">❌ Pengurangan: ' . $subtracts . ' produk</span>' .
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
