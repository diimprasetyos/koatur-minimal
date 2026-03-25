<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Resources\Tenants\Pages\EditTenant;
use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    protected static ?string $navigationLabel = 'Tenant';

    protected static string|UnitEnum|null $navigationGroup = 'Pengelolaan';
    protected static ?string $recordTitleAttribute = 'name';
    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // Jika super admin, tampilkan semua tenant tanpa filter.
        if ($user->is_super_admin ?? false) {
            return parent::getEloquentQuery();
        }

        // User biasa: hanya tenant yang dia assign/miliki.
        return parent::getEloquentQuery()
            ->whereHas('users', function (Builder $query) use ($user) {
                $query->where('users.id', $user->id);
            });
    }

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
