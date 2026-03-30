<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Resources\Tenants\Pages\EditTenant;
use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use App\Utils\PlanLimit;
use BackedEnum;
use Filament\Notifications\Notification;
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

    protected static ?string $navigationLabel = 'Toko';

    protected static string|UnitEnum|null $navigationGroup = 'Pengelolaan';

    public static function getModelLabel(): string
    {
        return 'Toko';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Toko';
    }

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


    /**
     * Tombol "Buat Toko" hanya muncul jika masih ada kuota.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();

        // Superadmin selalu bisa
        if ($user->isSuperAdmin())
            return true;

        return PlanLimit::canCreateTenant($user);
    }

    /**
     * Intercept sebelum create — double check + tampilkan notifikasi
     * jika user mencoba bypass lewat URL langsung.
     */
    public static function canCreateMore(): bool
    {
        $user = auth()->user();

        if (!PlanLimit::canCreateTenant($user)) {
            Notification::make()
                ->title('Batas kuota tercapai')
                ->body(
                    'Paket ' . PlanLimit::label($user->subscription_plan) .
                    ' hanya dapat membuat ' .
                    PlanLimit::maxTenants($user->subscription_plan) .
                    ' toko. Upgrade paket untuk menambah lebih banyak toko.'
                )
                ->warning()
                ->persistent()
                ->send();

            return false;
        }

        return true;
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
