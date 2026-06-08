<?php

namespace App\Filament\Superadmin\Resources\SubscriptionPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Plan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string =>
                        $state === 0
                            ? 'Gratis'
                            : 'Rp ' . number_format($state, 0, ',', '.')
                    ),

                TextColumn::make('billing_cycle')
                    ->label('Siklus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bulan'  => 'info',
                        'tahun'  => 'success',
                        'trial'  => 'warning',
                        default  => 'gray',
                    }),

                TextColumn::make('max_tenants')
                    ->label('Maks. Tenant')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string =>
                        $state === 0 ? '∞' : (string) $state
                    )
                    ->alignCenter(),

                TextColumn::make('max_users_per_tenant')
                    ->label('Maks. User/Tenant')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string =>
                        $state === 0 ? '∞' : (string) $state
                    )
                    ->alignCenter(),

                TextColumn::make('max_products')
                    ->label('Maks. Produk')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string =>
                        $state === 0 ? '∞' : (string) $state
                    )
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('subscriptions_count')
                    ->label('Subscriber')
                    ->counts('subscriptions')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),

                SelectFilter::make('billing_cycle')
                    ->label('Siklus Tagihan')
                    ->options([
                        'bulan'  => 'Per Bulan',
                        'tahun'  => 'Per Tahun',
                        'trial'  => 'Trial',
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