<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static string|UnitEnum|null $navigationGroup = 'Pengelolaan';

    public static function getModelLabel(): string
    {
        return 'Pengguna';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengguna';
    }

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Badge menampilkan jumlah user dalam tenant aktif.
     * Super_admin tidak punya tenant context, jadi hitung semua.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    /**
     * Hanya super_admin dan owner yang boleh mengakses resource ini.
     */
    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Scope query berdasarkan tenant aktif.
     * - Jika ada tenant context (panel admin): filter user dalam tenant tersebut.
     * - Jika tidak ada (panel superadmin): tampilkan semua user.
     * - Exclude super_admin dari listing di panel tenant supaya tidak terekspos.
     */
    public static function getEloquentQuery(): Builder
    {
        $tenantId = filament()->getTenant()?->id;

        return parent::getEloquentQuery()
            ->when(
                $tenantId,
                fn(Builder $query) => $query
                    ->whereHas(
                        'tenants',
                        fn(Builder $q) => $q->where('tenants.id', $tenantId)
                    )
                    ->whereDoesntHave('roles', fn(Builder $q) => $q->where('name', 'super_admin'))
            );
    }
}