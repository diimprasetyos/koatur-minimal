<?php

namespace App\Filament\Resources\Sales\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->sortable(),

                TextColumn::make('sale_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->placeholder('Walk-in')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'cash'     => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet'  => '📱 E-Wallet',
                        default    => $state,
                    })
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Penjualan'),
                    ]),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid'      => 'success',
                        'pending'   => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'paid'      => 'Lunas',
                        'pending'   => 'Belum Lunas',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    }),

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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sale_date', 'desc');
    }
}
