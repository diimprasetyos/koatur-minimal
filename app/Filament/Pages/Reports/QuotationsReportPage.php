<?php

namespace App\Filament\Pages\Reports;

use App\Models\Quotations\Quotation;
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

class QuotationsReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.reports.quotations-report-page';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Penawaran';
    protected static ?int $navigationSort = 2;

    // ── Summary properties ────────────────────────────────────

    public function getTotalQuotations(): int
    {
        return $this->getFilteredQuery()->count();
    }

    public function getTotalAccepted(): int
    {
        return $this->getFilteredQuery()
            ->where('status', Quotation::STATUS_ACCEPTED)
            ->count();
    }

    public function getTotalPotentialValue(): string
    {
        return 'Rp ' . number_format(
            $this->getFilteredQuery()
                ->whereNotIn('status', [Quotation::STATUS_REJECTED, Quotation::STATUS_EXPIRED])
                ->sum('total_amount'),
            0,
            ',',
            '.'
        );
    }

    public function getConversionRate(): string
    {
        $total = $this->getTotalQuotations();
        $accepted = $this->getTotalAccepted();

        if ($total === 0)
            return '0%';

        return number_format(($accepted / $total) * 100, 1) . '%';
    }

    protected function getFilteredQuery(): Builder
    {
        return Quotation::where('tenant_id', Filament::getTenant()?->id);
    }

    // ── Table ─────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quotation::query()
                    ->where('tenant_id', Filament::getTenant()?->id)
                    ->with(['user', 'customer', 'items'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('code')
                    ->label('No. Penawaran')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->placeholder('-'),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh'),

                TextColumn::make('valid_until')
                    ->label('Berlaku Sampai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->color(fn($record) => $record?->isExpired() ? 'danger' : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'draft' => '📝 Draft',
                        'sent' => '📤 Terkirim',
                        'accepted' => '✅ Diterima',
                        'rejected' => '❌ Ditolak',
                        'expired' => '🕒 Kadaluarsa',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'accepted' => 'success',
                        'sent' => 'info',
                        'draft' => 'gray',
                        'rejected' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignCenter(),

                TextColumn::make('total_amount')
                    ->label('Total Penawaran')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Nilai Penawaran')),
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

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                        'expired' => 'Kadaluarsa',
                    ]),

                SelectFilter::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}