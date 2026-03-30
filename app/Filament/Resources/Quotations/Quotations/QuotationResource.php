<?php

namespace App\Filament\Resources\Quotations\Quotations;

use App\Filament\Resources\Quotations\Quotations\Pages\CreateQuotation;
use App\Filament\Resources\Quotations\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Quotations\Pages\ListQuotations;
use App\Filament\Resources\Quotations\Quotations\Schemas\QuotationForm;
use App\Filament\Resources\Quotations\Quotations\Tables\QuotationsTable;
use App\Models\Quotations\Quotation;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Penawaran';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    public static function getModelLabel(): string
    {
        return 'Penawaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penawaran';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }

    // ─── Form ─────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    // ─── Table ────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
    }

    // ─── Query dengan eager loading ───────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'user'])
            ->withCount('items')
            ->where('tenant_id', Filament::getTenant()?->id);
    }
}
