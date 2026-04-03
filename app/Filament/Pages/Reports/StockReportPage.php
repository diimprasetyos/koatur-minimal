<?php
namespace App\Filament\Pages\Reports;

use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Traits\HasReportActions;
use BackedEnum;
use Filament\Facades\Filament;
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
    use InteractsWithTable, HasReportActions;

    const REPORT_TYPE = 'stock';
    const REPORT_TITLE = 'Laporan Stok';

    protected static ?string $title = self::REPORT_TITLE;

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
                    ->where('tenant_id', Filament::getTenant()?->id)
                    ->where('is_active', true)
                    ->with('category')
            )
            ->columns([
                TextColumn::make('name')->label('Produk')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->fontFamily('mono')->placeholder('—')->copyable(),
                TextColumn::make('category.name')->label('Kategori')->badge()->color('primary')->placeholder('—'),
                TextColumn::make('stock')->label('Stok')->alignCenter()->sortable()->badge()
                    ->color(fn(Product $r): string => match (true) {
                        !$r->track_stock => 'gray',
                        $r->stock <= 0 => 'danger',
                        $r->stock <= 5 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn(Product $r): string => $r->track_stock ? (string) $r->stock : '∞'),
                TextColumn::make('price')->label('Harga Jual')->money('IDR')->sortable(),
                TextColumn::make('cost_price')->label('Modal')->money('IDR')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_value')->label('Nilai Stok')->money('IDR')->sortable()
                    ->getStateUsing(fn(Product $r): float => $r->track_stock ? ($r->stock * $r->cost_price) : 0)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('track_stock')->label('Pantau Stok')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(
                        fn() => Category::query()
                            ->where('tenant_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                Filter::make('out_of_stock')->label('Stok Habis')
                    ->query(fn(Builder $q) => $q->where('track_stock', true)->where('stock', '<=', 0)),
                TernaryFilter::make('track_stock')->label('Pantau Stok'),
            ])
            ->defaultSort('stock', 'asc');
    }
}