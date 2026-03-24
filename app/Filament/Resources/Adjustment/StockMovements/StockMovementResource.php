<?php

namespace App\Filament\Resources\Adjustment\StockMovements;

use App\Filament\Resources\Adjustment\StockMovements\Pages\CreateStockMovement;
use App\Filament\Resources\Adjustment\StockMovements\Pages\EditStockMovement;
use App\Filament\Resources\Adjustment\StockMovements\Pages\ListStockMovements;
use App\Filament\Resources\Adjustment\StockMovements\Schemas\StockMovementForm;
use App\Filament\Resources\Adjustment\StockMovements\Tables\StockMovementsTable;
use App\Models\Adjustment\StockMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPath;

        protected static string |UnitEnum|null $navigationGroup = 'Penyesuaian';

    protected static ?string $navigationLabel = 'Riwayat Stok';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
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
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
            'edit' => EditStockMovement::route('/{record}/edit'),
        ];
    }
}
