<?php

namespace App\Filament\Resources\Adjustment\StockAdjustments\Tables;

use App\Models\Adjustment\StockAdjustment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('adjustment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Jml Produk')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        StockAdjustment::STATUS_CONFIRMED => 'success',
                        StockAdjustment::STATUS_DRAFT     => 'warning',
                        default                           => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        StockAdjustment::STATUS_CONFIRMED => '✅ Diterapkan',
                        StockAdjustment::STATUS_DRAFT     => '📝 Draft',
                        default                           => $state,
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->searchable(),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(40)
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
                        StockAdjustment::STATUS_CONFIRMED => 'Diterapkan',
                        StockAdjustment::STATUS_DRAFT     => 'Draft',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
