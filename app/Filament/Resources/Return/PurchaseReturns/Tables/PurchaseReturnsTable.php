<?php

namespace App\Filament\Resources\Return\PurchaseReturns\Tables;

use App\Models\Return\PurchaseReturn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('No. Retur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('purchase.reference_number')
                    ->label('No. PO')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Pemasok')
                    ->searchable(),

                TextColumn::make('return_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Jml Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total_return')
                    ->label('Total Retur')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        PurchaseReturn::STATUS_APPROVED => 'success',
                        PurchaseReturn::STATUS_PENDING => 'warning',
                        PurchaseReturn::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        PurchaseReturn::STATUS_APPROVED => 'Disetujui',
                        PurchaseReturn::STATUS_PENDING => 'Menunggu',
                        PurchaseReturn::STATUS_REJECTED => 'Ditolak',
                        default => $state,
                    }),

                TextColumn::make('return_method')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'debit_note' => 'Debit Note',
                        'refund' => 'Refund Tunai',
                        'replacement' => 'Penggantian',
                        default => $state,
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
                        PurchaseReturn::STATUS_APPROVED => 'Disetujui',
                        PurchaseReturn::STATUS_PENDING => 'Menunggu',
                        PurchaseReturn::STATUS_REJECTED => 'Ditolak',
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
