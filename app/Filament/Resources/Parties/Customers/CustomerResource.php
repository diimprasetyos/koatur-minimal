<?php

namespace App\Filament\Resources\Parties\Customers;

use App\Filament\Resources\Parties\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Parties\Customers\Pages\EditCustomer;
use App\Filament\Resources\Parties\Customers\Pages\ListCustomers;
use App\Filament\Resources\Parties\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Parties\Customers\Tables\CustomersTable;
use App\Models\Parties\Customer;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;


    protected static ?string $navigationLabel = 'Pelanggan';

    protected static string|UnitEnum|null $navigationGroup = 'Pihak';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function getModelLabel(): string
    {
        return 'Pelanggan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pelanggan';
    }


    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Filament::getTenant()?->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
