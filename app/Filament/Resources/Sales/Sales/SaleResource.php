<?php

namespace App\Filament\Resources\Sales\Sales;

use App\Filament\Resources\Sales\Sales\Pages\CreateSale;
use App\Filament\Resources\Sales\Sales\Pages\EditSale;
use App\Filament\Resources\Sales\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Sales\Schemas\SaleForm;
use App\Filament\Resources\Sales\Sales\Tables\SalesTable;
use App\Models\Sales\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    protected static ?string $navigationLabel = 'Penjualan';

    protected static string | UnitEnum | null $navigationGroup = 'Transaksi';

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function form(Schema $schema): Schema
    {
        return SaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'edit' => EditSale::route('/{record}/edit'),
        ];
    }

    // ─── Tenant Scope ────────────────────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->forCurrentTenant()
            ->with(['user', 'customer', 'items.product'])
            ->withCount('items');
    }
}
