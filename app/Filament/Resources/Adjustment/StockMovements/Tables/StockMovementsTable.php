<?php

namespace App\Filament\Resources\Adjustment\StockMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Toko')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                TextColumn::make('reference_type')
                    ->label('Jenis Referensi')
                    ->searchable(),
                TextColumn::make('reference_id')
                    ->label('ID Referensi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable(),
                TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_before')
                    ->label('Stok Awal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_after')
                    ->label('Stok Akhir')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
