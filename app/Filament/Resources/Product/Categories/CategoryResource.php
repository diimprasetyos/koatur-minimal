<?php

namespace App\Filament\Resources\Product\Categories;

use App\Filament\Resources\Product\Categories\Pages\CreateCategory;
use App\Filament\Resources\Product\Categories\Pages\EditCategory;
use App\Filament\Resources\Product\Categories\Pages\ListCategories;
use App\Filament\Resources\Product\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Product\Categories\Tables\CategoriesTable;
use App\Models\Product\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static string|UnitEnum|null $navigationGroup = 'Produk';

    public static function getModelLabel(): string
    {
        return 'Kategori Produk';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Produk';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

}
