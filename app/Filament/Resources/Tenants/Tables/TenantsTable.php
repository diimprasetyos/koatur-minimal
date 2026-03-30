<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Utils\PlanLimit;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Alias')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ])->label('Aksi'),
            ])
            ->emptyStateHeading('Belum ada toko')
            ->emptyStateDescription('Klik tombol di atas untuk menambahkan toko baru.')
            ->searchPlaceholder('Cari toko...')
            ->headerActions([
                Action::make('info_kuota')
                    ->label(function () {
                        $user = auth()->user();
                        if ($user->isSuperAdmin())
                            return 'Superadmin — unlimited';

                        $plan = $user->subscription_plan ?? 'basic';
                        $current = $user->tenants()->count();
                        $max = PlanLimit::maxTenants($plan);
                        $label = PlanLimit::label($plan);

                        if ($max === -1)
                            return "Paket {$label} — Unlimited toko";

                        return "Paket {$label} — {$current}/{$max} toko digunakan";
                    })
                    ->color(function () {
                        $user = auth()->user();
                        $plan = $user->subscription_plan ?? 'basic';
                        return PlanLimit::color($plan);
                    })
                    ->disabled()
                    ->badge(),
            ]);
    }
}