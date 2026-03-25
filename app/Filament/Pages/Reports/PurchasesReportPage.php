<?php

namespace App\Filament\Pages\Reports;

use App\Models\Purchases\Purchase;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use UnitEnum;

class PurchasesReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.reports.purchases-report-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Pembelian';

    public function getTotalPurchase(): string
    {
        return 'Rp ' . number_format(
            $this->getFilteredQuery()->sum('total'),
            0,
            ',',
            '.'
        );
    }
    public function getTotalTransactions(): int
    {
        return $this->getFilteredQuery()->count();
    }

    public function getAverageTransaction(): string
    {
        $count = $this->getTotalTransactions();
        if ($count === 0)
            return 'Rp 0';

        return 'Rp ' . number_format(
            $this->getFilteredQuery()->avg('total'),
            0,
            ',',
            '.'
        );
    }

    public function getTotalUnpaid(): string
    {
        return 'Rp ' . number_format(
            $this->getFilteredQuery()->sum('due'),
            0,
            ',',
            '.'
        );
    }

    public function getTotalPaid(): string
    {
        return 'Rp ' . number_format(
            $this->getFilteredQuery()->sum('paid'),
            0,
            ',',
            '.'
        );
    }

    protected function getFilteredQuery(): Builder
    {
        return Purchase::where('tenant_id', auth()->user()->tenant_id)
            ->whereNotIn('status', [Purchase::STATUS_CANCELLED]);
    }

    // ── Table ─────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Purchase::query()
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->whereNotIn('status', [Purchase::STATUS_CANCELLED])
                    ->with(['user', 'supplier', 'items'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('purchase_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('supplier_invoice')
                    ->label('No. Invoice Supplier')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->placeholder('Walk-in'),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh'),

                TextColumn::make('status')
                    ->label('Status PO')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'draft' => '📝 Draft',
                        'ordered' => '📦 Ordered',
                        'received' => '✅ Received',
                        'partial' => '⏳ Partial',
                        'cancelled' => '❌ Cancelled',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'received' => 'success',
                        'ordered' => 'info',
                        'partial' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'paid' => '✅ Lunas',
                        'partial' => '⏳ Sebagian',
                        'unpaid' => '❌ Belum Bayar',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Pembelian')),

                TextColumn::make('paid')
                    ->label('Terbayar')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Terbayar')),

                TextColumn::make('due')
                    ->label('Sisa Hutang')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->summarize(Sum::make()->money('IDR')->label('Total Hutang')),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari')
                            ->default(now()->startOfMonth())
                            ->native(false),
                        DatePicker::make('until')
                            ->label('Sampai')
                            ->default(now())
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['from'], fn($q) => $q->whereDate('purchase_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('purchase_date', '<=', $data['until']))
                    )
                    ->indicateUsing(fn(array $data) => array_filter([
                        $data['from'] ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                        $data['until'] ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                    ])),

                SelectFilter::make('status')
                    ->label('Status PO')
                    ->options([
                        'draft' => 'Draft',
                        'ordered' => 'Ordered',
                        'received' => 'Received',
                        'partial' => 'Partial',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Status Bayar')
                    ->options([
                        'paid' => 'Lunas',
                        'partial' => 'Sebagian',
                        'unpaid' => 'Belum Bayar',
                    ]),

                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name'),
            ])
            ->defaultSort('purchase_date', 'desc');
    }
}
