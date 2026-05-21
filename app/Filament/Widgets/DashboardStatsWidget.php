<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\ResolvesCurrentTenant;
use App\Models\Product\Product;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardStatsWidget extends BaseWidget
{
    use ResolvesCurrentTenant;

    protected static ?int $sort = 2;

    // ── Filter state (Livewire properties) ────────────────────────
    public string $period        = 'today';
    public string $saleStatus    = 'paid';
    public string $paymentMethod = 'all';

    // ── Filter form ───────────────────────────────────────────────
    protected function getFormSchema(): array
    {
        return [
            Select::make('period')
                ->label('Periode')
                ->options([
                    'today'   => 'Hari Ini',
                    'week'    => 'Minggu Ini',
                    'month'   => 'Bulan Ini',
                    'year'    => 'Tahun Ini',
                ])
                ->default('today')
                ->live(),

            Select::make('saleStatus')
                ->label('Status')
                ->options([
                    'all'       => 'Semua',
                    'paid'      => 'Lunas',
                    'pending'   => 'Pending',
                    'cancelled' => 'Dibatalkan',
                ])
                ->default('paid')
                ->live(),

            Select::make('paymentMethod')
                ->label('Metode Pembayaran')
                ->options([
                    'all'      => 'Semua',
                    'cash'     => 'Tunai',
                    'transfer' => 'Transfer',
                    'ewallet'  => 'E-Wallet',
                ])
                ->default('all')
                ->live(),
        ];
    }

    // ── Query builder berdasarkan filter ──────────────────────────
    private function baseQuery(int $tenantId, ?string $period = null): Builder
    {
        $query = Sale::where('tenant_id', $tenantId);

        // Period
        $p = $period ?? $this->period;
        match ($p) {
            'today' => $query->whereDate('sale_date', today()),
            'week'  => $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year),
            'year'  => $query->whereYear('sale_date', now()->year),
            default => $query->whereDate('sale_date', today()),
        };

        // Status
        if ($this->saleStatus !== 'all') {
            $query->where('status', $this->saleStatus);
        }

        // Metode pembayaran
        if ($this->paymentMethod !== 'all') {
            $query->where('payment_method', $this->paymentMethod);
        }

        return $query;
    }

    // ── Previous period untuk perbandingan ────────────────────────
    private function previousQuery(int $tenantId): Builder
    {
        $query = Sale::where('tenant_id', $tenantId);

        match ($this->period) {
            'today' => $query->whereDate('sale_date', today()->subDay()),
            'week'  => $query->whereBetween('sale_date', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ]),
            'month' => $query->whereMonth('sale_date', now()->subMonth()->month)
                             ->whereYear('sale_date', now()->subMonth()->year),
            'year'  => $query->whereYear('sale_date', now()->subYear()->year),
            default => $query->whereDate('sale_date', today()->subDay()),
        };

        if ($this->saleStatus !== 'all') {
            $query->where('status', $this->saleStatus);
        }

        if ($this->paymentMethod !== 'all') {
            $query->where('payment_method', $this->paymentMethod);
        }

        return $query;
    }

    private function getPeriodLabel(): string
    {
        return match ($this->period) {
            'today' => 'Hari Ini',
            'week'  => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year'  => 'Tahun Ini',
            default => 'Hari Ini',
        };
    }

    private function getPrevPeriodLabel(): string
    {
        return match ($this->period) {
            'today' => 'kemarin',
            'week'  => 'minggu lalu',
            'month' => 'bulan lalu',
            'year'  => 'tahun lalu',
            default => 'kemarin',
        };
    }


    private function buildChart(int $tenantId, string $type = 'revenue'): array
    {
        $points = match ($this->period) {
            'today' => collect(range(6, 0))->map(fn($h) => [
                'start' => now()->subHours($h)->startOfHour(),
                'end'   => now()->subHours($h)->endOfHour(),
            ]),
            'week' => collect(range(6, 0))->map(fn($d) => [
                'start' => today()->subDays($d)->startOfDay(),
                'end'   => today()->subDays($d)->endOfDay(),
            ]),
            'month' => collect(range(6, 0))->map(fn($d) => [
                'start' => today()->subDays($d * 4)->startOfDay(),
                'end'   => today()->subDays($d * 4)->endOfDay(),
            ]),
            'year' => collect(range(6, 0))->map(fn($m) => [
                'start' => now()->subMonths($m)->startOfMonth(),
                'end'   => now()->subMonths($m)->endOfMonth(),
            ]),
            default => collect(range(6, 0))->map(fn($d) => [
                'start' => today()->subDays($d)->startOfDay(),
                'end'   => today()->subDays($d)->endOfDay(),
            ]),
        };

        return $points->map(function ($range) use ($tenantId, $type) {
            $q = Sale::where('tenant_id', $tenantId)
                ->whereBetween('sale_date', [$range['start'], $range['end']]);

            if ($this->saleStatus !== 'all') {
                $q->where('status', $this->saleStatus);
            }
            if ($this->paymentMethod !== 'all') {
                $q->where('payment_method', $this->paymentMethod);
            }

            return $type === 'revenue'
                ? (float) $q->sum('total')
                : $q->count();
        })->toArray();
    }


    protected function getStats(): array
    {
        $tenantId = $this->getCurrentTenantId();

        if (! $tenantId) {
            return [];
        }

        $periodLabel   = $this->getPeriodLabel();
        $prevLabel     = $this->getPrevPeriodLabel();

        $salesNow      = (float) $this->baseQuery($tenantId)->sum('total');
        $salesPrev     = (float) $this->previousQuery($tenantId)->sum('total');
        $salesTrend    = $salesPrev > 0
            ? round((($salesNow - $salesPrev) / $salesPrev) * 100, 1)
            : 0;

        $txNow         = $this->baseQuery($tenantId)->count();
        $txPrev        = $this->previousQuery($tenantId)->count();

        $lowStockCount = Product::where('tenant_id', $tenantId)
            ->where('track_stock', true)
            ->where('stock', '<=', 5)
            ->count();

        $purchasesNow  = (float) Purchase::where('tenant_id', $tenantId)
            ->when($this->period === 'today', fn($q) => $q->whereDate('purchase_date', today()))
            ->when($this->period === 'week',  fn($q) => $q->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->period === 'month', fn($q) => $q->whereMonth('purchase_date', now()->month)->whereYear('purchase_date', now()->year))
            ->when($this->period === 'year',  fn($q) => $q->whereYear('purchase_date', now()->year))
            ->whereIn('status', [Purchase::STATUS_RECEIVED, Purchase::STATUS_ORDERED])
            ->sum('total');

        $salesChart = $this->buildChart($tenantId, 'revenue');
        $txChart    = $this->buildChart($tenantId, 'count');

        return [
            Stat::make("Penjualan {$periodLabel}", 'Rp ' . number_format($salesNow, 0, ',', '.'))
                ->description($salesTrend >= 0
                    ? "{$salesTrend}% lebih tinggi dari {$prevLabel}"
                    : abs($salesTrend) . "% lebih rendah dari {$prevLabel}")
                ->descriptionIcon($salesTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesTrend >= 0 ? 'success' : 'danger')
                ->chart($salesChart),

            Stat::make("Transaksi {$periodLabel}", $txNow . ' transaksi')
                ->description($txPrev > 0
                    ? "{$prevLabel}: {$txPrev} transaksi"
                    : "Belum ada transaksi {$prevLabel}")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart($txChart),

            Stat::make('Stok Kritis', $lowStockCount . ' produk')
                ->description($lowStockCount > 0 ? 'Segera lakukan restok' : 'Semua stok aman')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),

            Stat::make("Pengeluaran {$periodLabel}", 'Rp ' . number_format($purchasesNow, 0, ',', '.'))
                ->description('Total pembelian ' . $periodLabel)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),
        ];
    }
}
