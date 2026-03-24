<?php

namespace App\Filament\Resources\Parties\Suppliers;

use App\Filament\Resources\Parties\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Parties\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Parties\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Parties\Suppliers\Schemas\SupplierForm;
use App\Filament\Resources\Parties\Suppliers\Tables\SuppliersTable;
use App\Models\Parties\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'Pemasok';

    protected static string | UnitEnum | null $navigationGroup = 'Pihak';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
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
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
