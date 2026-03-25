<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Sale;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends Widget
{
    protected string $view = 'filament.widgets.sales-chart-widget';

    protected static ?string $heading = 'Grafik Penjualan';

    protected static ?int $sort = 2;

    // Toggle harian / bulanan
    public string $filter = 'daily';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian (30 hari terakhir)',
            'monthly' => 'Bulanan (12 bulan terakhir)',
        ];
    }

    protected function getData(): array
    {
        $tenantId = auth()->user()->tenant_id;

        if ($this->filter === 'monthly') {
            return $this->getMonthlyData($tenantId);
        }

        return $this->getDailyData($tenantId);
    }

    protected function getDailyData(int $tenantId): array
    {
        // Generate 30 hari terakhir
        $days = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

        $sales = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Omzet (Rp)',
                    'data' => $days->map(fn($d) => (float) ($sales[$d] ?? 0))->values()->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->values()->toArray(),
        ];
    }

    protected function getMonthlyData(int $tenantId): array
    {
        // Generate 12 bulan terakhir
        $months = collect(range(11, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $sales = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, SUM(total) as total, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('total', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Omzet (Rp)',
                    'data' => $months->map(fn($m) => (float) ($sales[$m] ?? 0))->values()->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn($m) => Carbon::parse($m . '-01')->format('M Y'))->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
