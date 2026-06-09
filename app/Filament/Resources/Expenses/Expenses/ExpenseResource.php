<?php

namespace App\Filament\Resources\Expenses\Expenses;

use App\Filament\Resources\Expenses\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Expenses\Expenses\Pages\EditExpense;
use App\Filament\Resources\Expenses\Expenses\Pages\ListExpenses;
use App\Filament\Resources\Expenses\Expenses\Schemas\ExpenseForm;
use App\Filament\Resources\Expenses\Expenses\Tables\ExpensesTable;
use App\Models\Expenses\Expense;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BarsArrowUp;

    protected static ?string $navigationLabel = 'Pengeluaran';

    protected static string|UnitEnum|null $navigationGroup = 'Pengeluaran';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function getModelLabel(): string
    {
        return 'Pengeluaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengeluaran';
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Filament::getTenant()?->id);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'edit' => EditExpense::route('/{record}/edit'),
        ];
    }
}