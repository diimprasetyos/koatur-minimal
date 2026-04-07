<?php

namespace App\Filament\Resources\Purchases\Purchases;

use App\Filament\Resources\Purchases\Purchases\Pages\CreatePurchase;
use App\Filament\Resources\Purchases\Purchases\Pages\EditPurchase;
use App\Filament\Resources\Purchases\Purchases\Pages\ListPurchases;
use App\Filament\Resources\Purchases\Purchases\Schemas\PurchaseForm;
use App\Filament\Resources\Purchases\Purchases\Tables\PurchasesTable;
use App\Models\Purchases\Purchase;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static ?string $navigationLabel = 'Pembelian';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    public static function getModelLabel(): string
    {
        return 'Pembelian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pembelian';
    }

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function form(Schema $schema): Schema
    {
        return PurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchasesTable::configure($table);
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
            'index' => ListPurchases::route('/'),
            'create' => CreatePurchase::route('/create'),
            'edit' => EditPurchase::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Filament::getTenant()?->id)
            ->with(['supplier', 'user', 'items.product'])
            ->withCount('items');
    }
}
