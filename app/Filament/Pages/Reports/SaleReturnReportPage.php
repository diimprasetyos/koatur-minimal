<?php

namespace App\Filament\Pages\Reports;

use App\Models\Return\SaleReturn;
use BackedEnum;

use Filament\Facades\Filament;
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

class SaleReturnReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.reports.sale-return-report-page';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUturnLeft;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Retur Penjualan';
    protected static ?int $navigationSort = 3;

    // ── Summary properties ────────────────────────────────────

    public function getTotalRefund(): string
    {
        return 'Rp ' . number_format(
            $this->getFilteredQuery()->sum('total_refund'),
            0,
            ',',
            '.'
        );
    }

    public function getTotalTransactions(): int
    {
        return $this->getFilteredQuery()->count();
    }

    public function getTotalApproved(): int
    {
        return $this->getFilteredQuery()
            ->where('status', SaleReturn::STATUS_APPROVED)
            ->count();
    }

    public function getTotalPending(): int
    {
        return $this->getFilteredQuery()
            ->where('status', SaleReturn::STATUS_PENDING)
            ->count();
    }

    protected function getFilteredQuery(): Builder
    {
        return SaleReturn::where('tenant_id', Filament::getTenant()?->id);
    }

    // ── Table ─────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaleReturn::query()
                    ->where('tenant_id', Filament::getTenant()?->id)
                    ->with(['user', 'sale', 'sale.customer', 'items'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('return_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('No. Retur')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('sale.invoice_number')
                    ->label('No. Invoice Asal')
                    ->searchable()
                    ->fontFamily('mono')
                    ->placeholder('-'),

                TextColumn::make('sale.customer.name')
                    ->label('Pelanggan')
                    ->placeholder('Walk-in'),

                TextColumn::make('user.name')
                    ->label('Diproses Oleh'),

                TextColumn::make('refund_method')
                    ->label('Metode Refund')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'cash' => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'store_credit' => '🎫 Kredit Toko',
                        default => $state ?? '-',
                    })
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'pending' => '⏳ Pending',
                        'approved' => '✅ Approved',
                        'rejected' => '❌ Rejected',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(40)
                    ->placeholder('-'),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total_refund')
                    ->label('Total Refund')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Refund')),
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
                            ->when($data['from'], fn($q) => $q->whereDate('return_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('return_date', '<=', $data['until']))
                    )
                    ->indicateUsing(fn(array $data) => array_filter([
                        $data['from'] ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                        $data['until'] ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                    ])),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('refund_method')
                    ->label('Metode Refund')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                        'store_credit' => 'Kredit Toko',
                    ]),
            ])
            ->defaultSort('return_date', 'desc');
    }
}