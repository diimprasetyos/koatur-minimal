<?php

namespace App\Filament\Pages\Reports;

use App\Models\Product\Product;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StockReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Stok';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.stock-report-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->where('is_active', true)
                    ->with('category')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->copyable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn(Product $record): string => match (true) {
                        !$record->track_stock => 'gray',
                        $record->stock <= 0 => 'danger',
                        $record->stock <= 5 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(
                        fn(Product $record): string =>
                        $record->track_stock ? (string) $record->stock : '∞'
                    ),

                TextColumn::make('price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('cost_price')
                    ->label('Modal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Nilai stok = stock * cost_price
                TextColumn::make('stock_value')
                    ->label('Nilai Stok')
                    ->money('IDR')
                    ->sortable()
                    ->getStateUsing(
                        fn(Product $record): float =>
                        $record->track_stock ? ($record->stock * $record->cost_price) : 0
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('track_stock')
                    ->label('Pantau Stok')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                Filter::make('low_stock')
                    ->label('Stok Hampir Habis (≤ 5)')
                    ->query(
                        fn(Builder $query) => $query
                            ->where('track_stock', true)
                            ->where('stock', '<=', 5)
                            ->where('stock', '>', 0)
                    ),

                Filter::make('out_of_stock')
                    ->label('Stok Habis')
                    ->query(
                        fn(Builder $query) => $query
                            ->where('track_stock', true)
                            ->where('stock', '<=', 0)
                    ),

                TernaryFilter::make('track_stock')
                    ->label('Pantau Stok'),
            ])
            ->defaultSort('stock', 'asc');
    }
}