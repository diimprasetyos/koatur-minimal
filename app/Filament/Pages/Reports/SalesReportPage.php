<?php

namespace App\Filament\Pages\Reports;

use App\Models\Sales\Sale;
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

class SalesReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.reports.sales-report-page';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?int $navigationSort = 1;

    // ── Summary properties ────────────────────────────────────

    public function getTotalRevenue(): string
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

    protected function getFilteredQuery(): Builder
    {
        return Sale::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'paid');
    }

    // ── Table ─────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->where('status', 'paid')
                    ->with(['user', 'customer', 'items'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->placeholder('Walk-in'),

                TextColumn::make('user.name')
                    ->label('Kasir'),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'cash' => '💵 Cash',
                        'transfer' => '🏦 Transfer',
                        'ewallet' => '📱 E-Wallet',
                        default => $state,
                    })
                    ->color('gray'),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Omzet')),
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
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']))
                    )
                    ->indicateUsing(fn(array $data) => array_filter([
                        $data['from'] ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                        $data['until'] ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                    ])),

                SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                        'ewallet' => 'E-Wallet',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Kasir')
                    ->relationship('user', 'name'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
