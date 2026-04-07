<?php

namespace App\Filament\Resources\Return\PurchaseReturns;

use App\Filament\Resources\Return\PurchaseReturns\Pages\CreatePurchaseReturn;
use App\Filament\Resources\Return\PurchaseReturns\Pages\EditPurchaseReturn;
use App\Filament\Resources\Return\PurchaseReturns\Pages\ListPurchaseReturns;
use App\Filament\Resources\Return\PurchaseReturns\Schemas\PurchaseReturnForm;
use App\Filament\Resources\Return\PurchaseReturns\Tables\PurchaseReturnsTable;
use App\Models\Return\PurchaseReturn;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PurchaseReturnResource extends Resource
{
    protected static ?string $model = PurchaseReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptRefund;

    protected static ?string $navigationLabel = 'Retur Pembelian';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    public static function getModelLabel(): string
    {
        return 'Retur Pembelian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Retur Pembelian';
    }

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function form(Schema $schema): Schema
    {
        return PurchaseReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseReturnsTable::configure($table);
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
            'index' => ListPurchaseReturns::route('/'),
            'create' => CreatePurchaseReturn::route('/create'),
            'edit' => EditPurchaseReturn::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['purchase', 'supplier', 'user'])
            ->where('tenant_id', Filament::getTenant()?->id);
    }
}
