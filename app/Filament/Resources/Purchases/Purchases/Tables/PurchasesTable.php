<?php

namespace App\Filament\Resources\Purchases\Purchases\Tables;

use App\Models\Purchases\Purchase;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->sortable(),

                TextColumn::make('purchase_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Pemasok')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Pembelian'),
                    ]),

                TextColumn::make('paid')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('due')
                    ->label('Hutang')
                    ->money('IDR')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Purchase::STATUS_DRAFT     => 'gray',
                        Purchase::STATUS_ORDERED   => 'warning',
                        Purchase::STATUS_RECEIVED  => 'success',
                        Purchase::STATUS_PARTIAL   => 'info',
                        Purchase::STATUS_CANCELLED => 'danger',
                        default                    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        Purchase::STATUS_DRAFT     => 'Draft',
                        Purchase::STATUS_ORDERED   => 'Dipesan',
                        Purchase::STATUS_RECEIVED  => 'Diterima',
                        Purchase::STATUS_PARTIAL   => 'Sebagian',
                        Purchase::STATUS_CANCELLED => 'Dibatalkan',
                        default                    => $state,
                    }),

                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Purchase::PAYMENT_PAID    => 'success',
                        Purchase::PAYMENT_PARTIAL => 'warning',
                        Purchase::PAYMENT_UNPAID  => 'danger',
                        default                   => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        Purchase::PAYMENT_PAID    => 'Lunas',
                        Purchase::PAYMENT_PARTIAL => 'Sebagian',
                        Purchase::PAYMENT_UNPAID  => 'Belum Bayar',
                        default                   => $state,
                    }),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('purchase_date', 'desc');
    }
}
