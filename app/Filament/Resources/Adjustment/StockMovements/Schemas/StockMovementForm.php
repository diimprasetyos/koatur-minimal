<?php

namespace App\Filament\Resources\Adjustment\StockMovements\Schemas;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMovementForm
{
    // Ambil tenant_id yang sedang aktif
    protected static function currentTenantId(): ?int
    {
        return Filament::getTenant()?->id;
    }

    // Susun dan kembalikan schema form lengkap
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('tenant_id')->default(fn() => self::currentTenantId())->required(),

            Select::make('product_id')
                ->label('Produk')
                ->relationship(
                    'product',
                    'name',
                    // Filter produk hanya milik tenant aktif
                    fn($q) => $q->where('tenant_id', self::currentTenantId())
                )
                ->searchable()
                ->required(),

            Select::make('user_id')
                ->label('User')
                ->relationship(
                    'user',
                    'name',
                    // Filter user hanya yang tergabung di tenant aktif
                    fn($q) => $q->whereHas(
                        'tenants',
                        fn($t) => $t->where('tenant_id', self::currentTenantId())
                    )
                )
                ->searchable(),

            TextInput::make('reference_type')->label('Tipe Referensi'),

            TextInput::make('reference_id')->label('ID Referensi')->numeric(),

            TextInput::make('type')->label('Tipe Gerakan')->required(),

            TextInput::make('qty')->label('Qty')->required()->numeric(),

            TextInput::make('stock_before')->label('Stok Sebelum')->required()->numeric(),

            TextInput::make('stock_after')->label('Stok Sesudah')->required()->numeric(),

            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }
}
