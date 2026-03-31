<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Product\Product;
use App\Models\Purchases\Purchase;
use App\Models\Quotations\Quotation;
use App\Models\Return\PurchaseReturn;
use App\Models\Return\SaleReturn;
use App\Models\Sales\Sale;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    // ── Allowed values ────────────────────────────────────────

    private const ALLOWED_TYPES = [
        'sales',
        'purchases',
        'stock',
        'quotations',
        'sale_return',
        'purchase_return',
    ];

    private const ALLOWED_FORMATS = ['pdf', 'excel', 'csv'];

    // ── Entry point ───────────────────────────────────────────

    public function export(Request $request)
    {
        $type = $request->get('type');
        $format = $request->get('format');

        abort_if(!in_array($type, self::ALLOWED_TYPES, true), 400, 'Invalid report type.');
        abort_if(!in_array($format, self::ALLOWED_FORMATS, true), 400, 'Invalid export format.');

        // Resolve tenant
        $tenantId = (int) $request->get('tenant');
        $tenant = Tenant::findOrFail($tenantId);

        $filters = [];
        if ($request->filled('filters')) {
            $decoded = base64_decode($request->get('filters'), true);
            $filters = $decoded ? (json_decode($decoded, true) ?? []) : [];
        }

        $data = $this->buildQuery($type, $tenantId, $filters);

        $title = $this->reportTitle($type);
        $dateInfo = $this->dateRangeLabel($filters);

        return match ($format) {
            'pdf' => $this->exportPdf($type, $data, $title, $dateInfo, $tenant),
            'excel', 'csv' => $this->exportExcel($type, $data, $title, $format),
        };
    }

    // ── Query Builder ─────────────────────────────────────────

    private function buildQuery(string $type, int $tenantId, array $filters)
    {

        $from = $filters['date_range']['from'] ?? null;
        $until = $filters['date_range']['until'] ?? null;
        $limit = $filters['']['limit'] ?? null;
        return match ($type) {

            'sales' => Sale::with(['customer', 'user', 'items'])
                ->where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('created_at', '<=', $until))
                ->when(
                    $filters['payment_method']['value'] ?? null,
                    fn($q, $v) => $q->where('payment_method', $v)
                )
                ->latest()
                ->get(),

            'purchases' => Purchase::with(['supplier', 'user', 'items'])
                ->where('tenant_id', $tenantId)
                ->whereNotIn('status', [Purchase::STATUS_CANCELLED])
                ->when($from, fn($q) => $q->whereDate('purchase_date', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('purchase_date', '<=', $until))
                ->when(
                    $filters['status']['value'] ?? null,
                    fn($q, $v) => $q->where('status', $v)
                )
                ->when(
                    $filters['payment_status']['value'] ?? null,
                    fn($q, $v) => $q->where('payment_status', $v)
                )
                ->when(
                    $filters['supplier_id']['value'] ?? null,
                    fn($q, $v) => $q->where('supplier_id', $v)
                )
                ->orderBy('purchase_date', 'desc')
                ->get(),

            'stock' => Product::with('category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->when(
                    $filters['category_id']['value'] ?? null,
                    fn($q, $v) => $q->where('category_id', $v)
                )
                ->orderBy('stock', 'asc')
                ->get(),

            'quotations' => Quotation::with(['customer', 'user', 'items'])
                ->where('tenant_id', $tenantId)
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('created_at', '<=', $until))
                ->when(
                    $filters['status']['value'] ?? null,
                    fn($q, $v) => $q->where('status', $v)
                )
                ->latest()
                ->get(),

            'sale_return' => SaleReturn::with(['sale.customer', 'user', 'items'])
                ->where('tenant_id', $tenantId)
                ->when($from, fn($q) => $q->whereDate('return_date', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('return_date', '<=', $until))
                ->when(
                    $filters['status']['value'] ?? null,
                    fn($q, $v) => $q->where('status', $v)
                )
                ->orderBy('return_date', 'desc')
                ->get(),

            'purchase_return' => PurchaseReturn::with(['supplier', 'purchase', 'user', 'items'])
                ->where('tenant_id', $tenantId)
                ->when($from, fn($q) => $q->whereDate('return_date', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('return_date', '<=', $until))
                ->when(
                    $filters['status']['value'] ?? null,
                    fn($q, $v) => $q->where('status', $v)
                )
                ->orderBy('return_date', 'desc')
                ->get(),

            default => collect(),
        };
    }

    // ── PDF Export ────────────────────────────────────────────

    private function exportPdf(string $type, $data, string $title, string $dateInfo, Tenant $tenant)
    {
        $html = view("reports.pdf.{$type}", [
            'data' => $data,
            'title' => $title,
            'dateInfo' => $dateInfo,
            'tenant' => $tenant,
            'printedAt' => now()->format('d M Y, H:i'),
        ])->render();

        // Guard: DomPDF throws on empty HTML
        abort_if(empty(trim($html)), 500, "PDF view [reports.pdf.{$type}] rendered empty.");

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'sans-serif')
            ->setOption('isRemoteEnabled', true);

        $filename = strtolower(str_replace(' ', '-', $title)) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // ── Excel / CSV Export ────────────────────────────────────

    private function exportExcel(string $type, $data, string $title, string $format)
    {
        $filename = strtolower(str_replace(' ', '-', $title)) . '-' . now()->format('Ymd');
        $filename .= $format === 'csv' ? '.csv' : '.xlsx';

        $writerType = $format === 'csv'
            ? \Maatwebsite\Excel\Excel::CSV
            : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(
            new ReportExport($type, $data, $title),
            $filename,
            $writerType
        );
    }

    // ── Helpers ───────────────────────────────────────────────

    private function reportTitle(string $type): string
    {
        return match ($type) {
            'sales' => 'Laporan Penjualan',
            'purchases' => 'Laporan Pembelian',
            'stock' => 'Laporan Stok',
            'quotations' => 'Laporan Penawaran',
            'sale_return' => 'Laporan Retur Penjualan',
            'purchase_return' => 'Laporan Retur Pembelian',
            default => 'Laporan',
        };
    }

    private function dateRangeLabel(array $filters): string
    {
        $from = $filters['date_range']['from'] ?? null;
        $until = $filters['date_range']['until'] ?? null;

        if (!$from && !$until) {
            return 'Semua Periode';
        }

        $f = $from ? Carbon::parse($from)->format('d M Y') : '...';
        $u = $until ? Carbon::parse($until)->format('d M Y') : '...';

        return "{$f} — {$u}";
    }
}