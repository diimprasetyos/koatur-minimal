<?php

namespace App\Filament\Resources\Expenses\ExpenseCategories;

use App\Filament\Resources\Expenses\ExpenseCategories\Pages\CreateExpenseCategory;
use App\Filament\Resources\Expenses\ExpenseCategories\Pages\EditExpenseCategory;
use App\Filament\Resources\Expenses\ExpenseCategories\Pages\ListExpenseCategories;
use App\Filament\Resources\Expenses\ExpenseCategories\Schemas\ExpenseCategoryForm;
use App\Filament\Resources\Expenses\ExpenseCategories\Tables\ExpenseCategoriesTable;
use App\Models\Expenses\ExpenseCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    protected static ?string $navigationLabel = 'Kategori Pengeluaran';

    protected static string|UnitEnum|null $navigationGroup = 'Pengeluaran';

    public static function getModelLabel(): string
    {
        return 'Kategori Pengeluaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Pengeluaran';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExpenseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseCategoriesTable::configure($table);
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
            'index' => ListExpenseCategories::route('/'),
            'create' => CreateExpenseCategory::route('/create'),
            'edit' => EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
