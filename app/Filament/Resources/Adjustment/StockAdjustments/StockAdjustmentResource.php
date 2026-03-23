<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments;

use App\Filament\Resources\Adjustment\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Resources\Adjustment\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Resources\Adjustment\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Resources\Adjustment\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Resources\Adjustment\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Models\Adjustment\StockAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
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
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
