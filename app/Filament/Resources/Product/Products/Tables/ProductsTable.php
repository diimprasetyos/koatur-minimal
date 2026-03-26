<?php

namespace App\Filament\Resources\Product\Products\Tables;

use App\Models\Product\Category;
use App\Models\Product\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=P&background=e2e8f0&color=475569'),

                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Product $record) => $record->sku ?? '—'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),

                TextColumn::make('price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('cost_price')
                    ->label('Modal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn(Product $record): string => match (true) {
                        !$record->track_stock => 'gray',
                        $record->stock <= 0 => 'danger',
                        $record->stock <= 5 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn(Product $record): string => $record->track_stock ? (string) $record->stock : '∞'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn() => Category::where('tenant_id', Filament::getTenant()?->id)->pluck('name', 'id')),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                Filter::make('stok_habis')
                    ->label('Stok Habis')
                    ->query(fn(Builder $query) => $query->where('track_stock', true)->where('stock', '<=', 0)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
