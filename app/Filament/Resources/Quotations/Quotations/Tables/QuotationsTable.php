<?php

namespace App\Filament\Resources\Quotations\Quotations\Tables;

use App\Models\Quotations\Quotation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('valid_until')
                    ->label('Berlaku Hingga')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Quotation $record) =>
                        $record->isExpired() ? 'danger' : null
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        Quotation::STATUS_DRAFT    => 'Draft',
                        Quotation::STATUS_SENT     => 'Terkirim',
                        Quotation::STATUS_ACCEPTED => 'Diterima',
                        Quotation::STATUS_REJECTED => 'Ditolak',
                        Quotation::STATUS_EXPIRED  => 'Kadaluarsa',
                        default                    => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        Quotation::STATUS_DRAFT    => 'gray',
                        Quotation::STATUS_SENT     => 'info',
                        Quotation::STATUS_ACCEPTED => 'success',
                        Quotation::STATUS_REJECTED,
                        Quotation::STATUS_EXPIRED  => 'danger',
                        default                    => 'gray',
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Quotation::STATUS_DRAFT    => 'Draft',
                        Quotation::STATUS_SENT     => 'Terkirim',
                        Quotation::STATUS_ACCEPTED => 'Diterima',
                        Quotation::STATUS_REJECTED => 'Ditolak',
                        Quotation::STATUS_EXPIRED  => 'Kadaluarsa',
                    ]),

                SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('valid_until')
                    ->label('Rentang Tanggal Berlaku')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'],  fn ($q, $v) => $q->whereDate('valid_until', '>=', $v))
                        ->when($data['until'], fn ($q, $v) => $q->whereDate('valid_until', '<=', $v))
                    ),

                Filter::make('expired')
                    ->label('Hanya yang kadaluarsa')
                    ->query(fn (Builder $query) => $query
                        ->whereNotNull('valid_until')
                        ->whereDate('valid_until', '<', now())
                        ->whereNotIn('status', [Quotation::STATUS_ACCEPTED])
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->hidden(fn (Quotation $record) => ! $record->isEditable()),

                    Action::make('mark_sent')
                        ->label('Tandai Terkirim')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->hidden(fn (Quotation $record) => $record->status !== Quotation::STATUS_DRAFT)
                        ->action(function (Quotation $record) {
                            $record->update(['status' => Quotation::STATUS_SENT]);
                            Notification::make()
                                ->title('Penawaran ditandai sebagai terkirim.')
                                ->success()
                                ->send();
                        }),

                    Action::make('convert_to_sale')
                        ->label('Konversi ke Penjualan')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Penawaran ini akan dikonversi menjadi transaksi penjualan. Tindakan ini tidak bisa dibatalkan.')
                        ->hidden(fn (Quotation $record) => ! $record->isEditable())
                        ->action(function (Quotation $record) {
                            try {
                                $sale = $record->convertToSale();
                                Notification::make()
                                    ->title("Berhasil dikonversi ke penjualan #{$sale->code}.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Gagal: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->hidden(fn (Quotation $record) => ! $record->isEditable())
                        ->action(function (Quotation $record) {
                            $record->update(['status' => Quotation::STATUS_REJECTED]);
                            Notification::make()
                                ->title('Penawaran ditolak.')
                                ->warning()
                                ->send();
                        }),

                    DeleteAction::make()
                        ->hidden(fn (Quotation $record) => ! $record->isDraft()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
