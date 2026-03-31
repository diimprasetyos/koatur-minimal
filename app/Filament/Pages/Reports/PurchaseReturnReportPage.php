<?php
namespace App\Filament\Pages\Reports;

use App\Models\Return\PurchaseReturn;
use App\Traits\HasReportActions;
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

class PurchaseReturnReportPage extends Page implements HasTable
{
    use InteractsWithTable, HasReportActions;

    const REPORT_TYPE = 'purchase_return';
    const REPORT_TITLE = 'Laporan Retur Pembelian';

    protected static ?string $title = self::REPORT_TITLE;

    protected string $view = 'filament.pages.reports.purchase-return-report-page';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUturnRight;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Retur Pembelian';
    protected static ?int $navigationSort = 5;

    public function getTotalReturn(): string
    {
        return 'Rp ' . number_format($this->getFilteredQuery()->sum('total_return'), 0, ',', '.');
    }
    public function getTotalTransactions(): int
    {
        return $this->getFilteredQuery()->count();
    }
    public function getTotalApproved(): int
    {
        return $this->getFilteredQuery()->where('status', PurchaseReturn::STATUS_APPROVED)->count();
    }
    public function getTotalPending(): int
    {
        return $this->getFilteredQuery()->where('status', PurchaseReturn::STATUS_PENDING)->count();
    }

    protected function getFilteredQuery(): Builder
    {
        return PurchaseReturn::where('tenant_id', Filament::getTenant()?->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PurchaseReturn::query()
                    ->where('tenant_id', Filament::getTenant()?->id)
                    ->with(['user', 'supplier', 'purchase', 'items'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('return_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('reference_number')->label('No. Retur')->searchable()->fontFamily('mono')->copyable(),
                TextColumn::make('purchase.reference_number')->label('No. PO Asal')->searchable()->fontFamily('mono')->placeholder('-'),
                TextColumn::make('supplier.name')->label('Supplier')->placeholder('-'),
                TextColumn::make('user.name')->label('Diproses Oleh'),
                TextColumn::make('return_method')->label('Metode Return')->badge()
                    ->formatStateUsing(fn(?string $s) => match ($s) {
                        'debit_note' => '📄 Debit Note', 'cash' => '💵 Cash', 'replace' => '🔄 Penggantian', default => $s ?? '-',
                    })->color('gray'),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn($s) => match ($s) {
                        'pending' => '⏳ Pending', 'approved' => '✅ Approved', 'rejected' => '❌ Rejected', default => $s,
                    })
                    ->color(fn($s) => match ($s) {
                        'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', default => 'gray',
                    }),
                TextColumn::make('reason')->label('Alasan')->limit(40)->placeholder('-'),
                TextColumn::make('items_count')->label('Item')->counts('items')->alignCenter(),
                TextColumn::make('total_return')->label('Total Retur')->money('IDR')->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total Retur')),
            ])
            ->filters([
                Filter::make('date_range')->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari')->default(now()->startOfMonth())->native(false),
                        DatePicker::make('until')->label('Sampai')->default(now())->native(false),
                    ])
                    ->query(
                        fn(Builder $q, array $data) => $q
                            ->when($data['from'], fn($q) => $q->whereDate('return_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('return_date', '<=', $data['until']))
                    )
                    ->indicateUsing(fn(array $data) => array_filter([
                        $data['from'] ? 'Dari: ' . Carbon::parse($data['from'])->format('d M Y') : null,
                        $data['until'] ? 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y') : null,
                    ])),
                SelectFilter::make('status')->label('Status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                SelectFilter::make('return_method')->label('Metode Return')
                    ->options(['debit_note' => 'Debit Note', 'cash' => 'Cash', 'replace' => 'Penggantian']),
                SelectFilter::make('supplier_id')->label('Supplier')->relationship('supplier', 'name'),
            ])
            ->defaultSort('return_date', 'desc');
    }
}