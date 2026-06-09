<?php

namespace App\Filament\Resources\Expenses\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// Fix: Import 'Nette\Utils\Image' yang tidak terpakai dihapus

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Fix: Kolom tenant.name dihapus — resource sudah di-scope per tenant
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->category?->color ?? 'gray'),
                TextColumn::make('reference_number')
                    ->label('No Referensi')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Tanggal Pengeluaran')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'cash' => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet' => '📱 E-Wallet',
                        default => $state,
                    })
                    ->color('gray')
                    ->toggleable(),
                ImageColumn::make('attachment')
                    ->label('Bukti')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=P&background=e2e8f0&color=475569'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
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
            ]);
    }
}