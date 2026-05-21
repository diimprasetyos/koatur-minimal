<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\ResolvesCurrentTenant;
use App\Models\Sales\Sale;
use Filament\Widgets\ChartWidget;

class SalesChartWidget extends ChartWidget
{
    use ResolvesCurrentTenant;

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        $periodLabel = match ($this->period) {
            'today' => 'Hari Ini (per jam)',
            'week'  => '7 Hari Terakhir',
            'month' => 'Bulan Ini (per minggu)',
            'year'  => 'Tahun Ini (per bulan)',
            default => '7 Hari Terakhir',
        };

        return 'Grafik Penjualan — ' . $periodLabel;
    }

    public string $period        = 'week';
    public string $dataType      = 'revenue';
    public string $paymentMethod = 'all';
    public string $saleStatus    = 'paid';

    public ?string $filter = 'revenue';

    protected function getFilters(): ?array
    {
        return null;
    }


    private function buildDataPoints(): array
    {
        $tenantId = $this->getCurrentTenantId();

        if (! $tenantId) {
            return [array_fill(0, 7, 0), array_fill(0, 7, '')];
        }

        $ranges = match ($this->period) {
            'today' => collect(range(6, 0))->map(fn($h) => [
                'label' => now()->subHours($h)->format('H:i'),
                'start' => now()->subHours($h)->startOfHour(),
                'end'   => now()->subHours($h)->endOfHour(),
            ]),
            'week' => collect(range(6, 0))->map(fn($d) => [
                'label' => today()->subDays($d)->translatedFormat('D, d M'),
                'start' => today()->subDays($d)->startOfDay(),
                'end'   => today()->subDays($d)->endOfDay(),
            ]),
            'month' => collect(range(3, 0))->map(fn($w) => [
                'label' => 'Minggu ke-' . (now()->weekOfMonth - $w),
                'start' => now()->subWeeks($w)->startOfWeek(),
                'end'   => now()->subWeeks($w)->endOfWeek(),
            ]),
            'year' => collect(range(11, 0))->map(fn($m) => [
                'label' => now()->subMonths($m)->translatedFormat('M Y'),
                'start' => now()->subMonths($m)->startOfMonth(),
                'end'   => now()->subMonths($m)->endOfMonth(),
            ]),
            default => collect(range(6, 0))->map(fn($d) => [
                'label' => today()->subDays($d)->translatedFormat('D, d M'),
                'start' => today()->subDays($d)->startOfDay(),
                'end'   => today()->subDays($d)->endOfDay(),
            ]),
        };

        $data   = [];
        $labels = [];

        foreach ($ranges as $range) {
            $labels[] = $range['label'];

            $q = Sale::where('tenant_id', $tenantId)
                ->whereBetween('sale_date', [$range['start'], $range['end']]);

            if ($this->saleStatus !== 'all') {
                $q->where('status', $this->saleStatus);
            }

            if ($this->paymentMethod !== 'all') {
                $q->where('payment_method', $this->paymentMethod);
            }

            $data[] = $this->dataType === 'revenue'
                ? (float) $q->sum('total')
                : $q->count();
        }

        return [$data, $labels];
    }

    protected function getData(): array
    {
        [$data, $labels] = $this->buildDataPoints();

        $isRevenue = $this->dataType === 'revenue';

        return [
            'datasets' => [
                [
                    'label'                => $isRevenue ? 'Omzet (Rp)' : 'Jumlah Transaksi',
                    'data'                 => $data,
                    'fill'                 => true,
                    'backgroundColor'      => 'rgba(37, 99, 235, 0.08)',
                    'borderColor'          => 'rgba(37, 99, 235, 1)',
                    'borderWidth'          => 2,
                    'tension'              => 0.4,
                    'pointBackgroundColor' => 'rgba(37, 99, 235, 1)',
                    'pointRadius'          => 4,
                    'pointHoverRadius'     => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['precision' => 0],
                ],
            ],
        ];
    }
}
