<?php

namespace App\Filament\Pages\Reports;

use App\Models\Sales\SaleItem;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use UnitEnum;

class BestSellingPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Fire;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Produk Terlaris';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.reports.best-selling-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaleItem::query()
                    ->whereHas(
                        'sale',
                        fn($q) => $q
                            ->where('tenant_id', auth()->user()->tenant_id)
                            ->where('status', 'paid')
                    )
                    ->with('product')
                    ->selectRaw('
                        product_id,
                        product_name,
                        SUM(qty) as total_qty,
                        SUM(subtotal) as total_revenue,
                        COUNT(DISTINCT sale_id) as total_orders,
                        AVG(price) as avg_price
                    ')
                    ->groupBy('product_id', 'product_name')
                    ->orderByDesc('total_qty')
            )
            ->columns([
                TextColumn::make('product_name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),

                TextColumn::make('total_qty')
                    ->label('Total Terjual')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->summarize(Sum::make()->label('Total Unit')),

                TextColumn::make('total_orders')
                    ->label('Jumlah Order')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('avg_price')
                    ->label('Rata-rata Harga')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_revenue')
                    ->label('Total Omzet')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->summarize(Sum::make()->money('IDR')->label('Total Omzet')),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari')
                            ->default(now()->startOfMonth())
                            ->native(false),
                        DatePicker::make('until')
                            ->label('Sampai')
                            ->default(now())
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['from'], fn($q) => $q->whereHas('sale', fn($s) => $s->whereDate('created_at', '>=', $data['from'])))
                            ->when($data['until'], fn($q) => $q->whereHas('sale', fn($s) => $s->whereDate('created_at', '<=', $data['until'])))
                    )
                    ->indicateUsing(fn(array $data) => array_filter([
                        $data['from'] ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                        $data['until'] ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                    ])),
            ])
            ->defaultSort('total_qty', 'desc');
    }
}