<?php

namespace App\Filament\Superadmin\Resources\Tenants;

use App\Filament\Superadmin\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Superadmin\Resources\Tenants\Pages\EditTenant;
use App\Filament\Superadmin\Resources\Tenants\Pages\ListTenants;
use App\Filament\Superadmin\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Superadmin\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string |UnitEnum|null $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Tenant';

    protected static ?string $pluralModelLabel = 'Tenant';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit'   => EditTenant::route('/{record}/edit'),
        ];
    }
}
