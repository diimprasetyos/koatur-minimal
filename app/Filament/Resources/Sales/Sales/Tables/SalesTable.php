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
                    ->fontFamily('mono'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->placeholder('Walk-in')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'cash'     => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet'  => '📱 E-Wallet',
                        default    => $state,
                    })
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
