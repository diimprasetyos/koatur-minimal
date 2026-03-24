<?php

namespace App\Filament\Resources\Expenses\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Nette\Utils\Image;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->expense_category?->color ?? 'gray'),
                TextColumn::make('reference_number')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'cash'     => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet'  => '📱 E-Wallet',
                        default    => $state,
                    })
                    ->color('gray')
                    ->toggleable(),
                ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=P&background=e2e8f0&color=475569'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
