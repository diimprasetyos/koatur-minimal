<?php

namespace App\Filament\Superadmin\Resources\Users\Tables;

use App\Models\Subscription\Subscription;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('tenants.name')
                    ->label('Tenant')
                    ->placeholder('- Super Admin - ')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('warning'),

                // ── Kolom Subscription ───────────────────────────────────
                TextColumn::make('latestSubscription.plan.name')
                    ->label('Plan')
                    ->placeholder('—')
                    ->badge()
                    ->color('info'),

                TextColumn::make('latestSubscription.status')
                    ->label('Sub. Status')
                    ->placeholder('—')
                    ->badge()
                    ->color(fn(?string $state) => match ($state) {
                        'active'    => 'success',
                        'trial'     => 'info',
                        'expired'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'active'    => 'Aktif',
                        'trial'     => 'Trial',
                        'expired'   => 'Expired',
                        'cancelled' => 'Dibatalkan',
                        default     => '—',
                    }),

                TextColumn::make('latestSubscription.expires_at')
                    ->label('Expired')
                    ->dateTime('d M Y')
                    ->placeholder('Lifetime')
                    ->color(fn($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : null)
                    ->sortable()
                    ->toggleable(),
                // ────────────────────────────────────────────────────────

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tenants')
                    ->label('Tenant')
                    ->relationship('tenants', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                // Filter status subscription
                SelectFilter::make('subscription_status')
                    ->label('Status Subscription')
                    ->options([
                        Subscription::STATUS_ACTIVE    => 'Aktif',
                        Subscription::STATUS_TRIAL     => 'Trial',
                        Subscription::STATUS_EXPIRED   => 'Expired',
                        Subscription::STATUS_CANCELLED => 'Dibatalkan',
                    ])
                    ->query(function ($query, array $data) {
                        if (blank($data['value'])) return;
                        $query->whereHas('latestSubscription', fn($q) => $q->where('status', $data['value']));
                    }),
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
