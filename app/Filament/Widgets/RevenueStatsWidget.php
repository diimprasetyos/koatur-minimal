<?php

namespace App\Filament\Widgets;

use App\Models\Product\Product;
use App\Models\Sales\Sale;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class RevenueStatsWidget extends Widget
{
    protected string $view = 'filament.widgets.revenue-stats-widget';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = auth()->user()->tenant_id;

        // ── Hari ini ──────────────────────────────────────────
        $todayRevenue = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayCount = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereDate('created_at', today())
            ->count();

        // ── Bulan ini ─────────────────────────────────────────
        $monthRevenue = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $monthCount = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Bulan lalu (untuk perbandingan) ───────────────────
        $lastMonthRevenue = Sale::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // ── Stok hampir habis ─────────────────────────────────
        $lowStockCount = Product::where('tenant_id', $tenantId)
            ->where('track_stock', true)
            ->where('is_active', true)
            ->where('stock', '<=', 5)
            ->count();

        return [
            Stat::make('Omzet Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description($todayCount . ' transaksi hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Omzet Bulan Ini', 'Rp ' . number_format($monthRevenue, 0, ',', '.'))
                ->description(($revenueChange >= 0 ? '▲ ' : '▼ ') . abs($revenueChange) . '% vs bulan lalu')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart(
                    Sale::where('tenant_id', $tenantId)
                        ->where('status', 'paid')
                        ->whereMonth('created_at', now()->month)
                        ->selectRaw('DATE(created_at) as date, SUM(total) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('total')
                        ->toArray()
                ),

            Stat::make('Total Transaksi Bulan Ini', number_format($monthCount, 0, ',', '.'))
                ->description('Transaksi lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Stok Hampir Habis', $lowStockCount . ' produk')
                ->description('Stok ≤ 5 unit')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),
        ];
    }
}
