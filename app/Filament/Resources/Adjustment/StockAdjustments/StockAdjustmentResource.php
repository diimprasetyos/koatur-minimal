<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments;

use App\Filament\Resources\Adjustment\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Resources\Adjustment\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Resources\Adjustment\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Resources\Adjustment\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Resources\Adjustment\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Models\Adjustment\StockAdjustment;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = 'Penyesuaian Stok';

    protected static string|UnitEnum|null $navigationGroup = 'Penyesuaian';

    // FIX: 'name' tidak ada di model, pakai 'reference_number'
    protected static ?string $recordTitleAttribute = 'reference_number';

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit'   => EditStockAdjustment::route('/{record}/edit'),
        ];
    }

    // FIX: tambahkan tenant filter + eager load relasi yang ada
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->withCount('items')
            ->where('tenant_id', Filament::getTenant()?->id);
    }
}
