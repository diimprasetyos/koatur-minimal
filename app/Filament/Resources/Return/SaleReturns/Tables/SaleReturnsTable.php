<?php

namespace App\Filament\Resources\Return\SaleReturns\Tables;

use App\Models\Return\SaleReturn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SaleReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('No. Retur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sale.invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('return_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Jml Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total_refund')
                    ->label('Total Refund')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        SaleReturn::STATUS_APPROVED => 'success',
                        SaleReturn::STATUS_PENDING  => 'warning',
                        SaleReturn::STATUS_REJECTED => 'danger',
                        default                     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        SaleReturn::STATUS_APPROVED => 'Disetujui',
                        SaleReturn::STATUS_PENDING  => 'Menunggu',
                        SaleReturn::STATUS_REJECTED => 'Ditolak',
                        default                     => $state,
                    }),

                TextColumn::make('refund_method')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'refund'       => 'Refund Tunai',
                        'exchange'     => 'Tukar Barang',
                        'store_credit' => 'Kredit Toko',
                        default        => $state,
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        SaleReturn::STATUS_APPROVED => 'Disetujui',
                        SaleReturn::STATUS_PENDING  => 'Menunggu',
                        SaleReturn::STATUS_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
