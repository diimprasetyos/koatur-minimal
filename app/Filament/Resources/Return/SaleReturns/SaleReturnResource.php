<?php

namespace App\Filament\Resources\Return\SaleReturns;

use App\Filament\Resources\Return\SaleReturns\Pages\CreateSaleReturn;
use App\Filament\Resources\Return\SaleReturns\Pages\EditSaleReturn;
use App\Filament\Resources\Return\SaleReturns\Pages\ListSaleReturns;
use App\Filament\Resources\Return\SaleReturns\Schemas\SaleReturnForm;
use App\Filament\Resources\Return\SaleReturns\Tables\SaleReturnsTable;
use App\Models\Return\SaleReturn;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SaleReturnResource extends Resource
{
    protected static ?string $model = SaleReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptRefund;

    protected static ?string $navigationLabel = 'Retur Penjualan';

    protected static string | UnitEnum | null $navigationGroup = 'Transaksi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SaleReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SaleReturnsTable::configure($table);
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
            'index' => ListSaleReturns::route('/'),
            'create' => CreateSaleReturn::route('/create'),
            'edit' => EditSaleReturn::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['sale', 'user'])
            ->withCount('items')
            ->where('tenant_id', Filament::getTenant()?->id);
    }
}
