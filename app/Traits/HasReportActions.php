<?php

namespace App\Traits;

use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

trait HasReportActions
{
    // ── Header Actions ────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // 1. Print langsung (browser print)
            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(fn() => $this->dispatch('print-report')),

            // 2. Export PDF
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn() => $this->buildExportUrl('pdf'))
                ->openUrlInNewTab(),

            // 3. Export Excel
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn() => $this->buildExportUrl('excel'))
                ->openUrlInNewTab(),

            // 4. Export CSV
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(fn() => $this->buildExportUrl('csv'))
                ->openUrlInNewTab(),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Bangun URL export dengan meneruskan filter aktif saat ini.
     */
    protected function buildExportUrl(string $format): string
    {
        $tenant = filament()->getTenant();
        $type = static::REPORT_TYPE;

        // Ambil state filter aktif dari tabel Filament
        $filters = $this->tableFilters ?? [];

        return URL::signedRoute('reports.export', [
            'tenant' => $tenant?->id,
            'type' => $type,
            'format' => $format,
            'filters' => base64_encode(json_encode($filters)),
        ]);
    }

    /**
     * Judul laporan untuk header cetak.
     * Override di masing-masing page jika perlu.
     */
    public function getReportTitle(): string
    {
        return defined('static::REPORT_TITLE') ? static::REPORT_TITLE : 'Laporan';
    }
}