<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private string $type,
        private Collection $data,
        private string $title,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    // ── Headings per type ─────────────────────────────────────

    public function headings(): array
    {
        return match ($this->type) {

            'sales' => [
                'Tanggal',
                'No. Invoice',
                'Pelanggan',
                'Kasir',
                'Metode Bayar',
                'Jumlah Item',
                'Total (Rp)',
            ],

            'purchases' => [
                'Tanggal',
                'No. Referensi',
                'No. Invoice Supplier',
                'Supplier',
                'Dibuat Oleh',
                'Status PO',
                'Status Bayar',
                'Jumlah Item',
                'Total (Rp)',
                'Terbayar (Rp)',
                'Sisa Hutang (Rp)',
            ],

            'stock' => [
                'Produk',
                'SKU',
                'Kategori',
                'Stok',
                'Harga Jual (Rp)',
                'Modal (Rp)',
                'Nilai Stok (Rp)',
            ],

            'quotations' => [
                'Tanggal',
                'No. Penawaran',
                'Pelanggan',
                'Dibuat Oleh',
                'Berlaku Sampai',
                'Status',
                'Jumlah Item',
                'Total (Rp)',
            ],

            'sale_return' => [
                'Tanggal',
                'No. Retur',
                'No. Invoice Asal',
                'Pelanggan',
                'Diproses Oleh',
                'Metode Refund',
                'Status',
                'Jumlah Item',
                'Total Refund (Rp)',
            ],

            'purchase_return' => [
                'Tanggal',
                'No. Retur',
                'No. PO Asal',
                'Supplier',
                'Diproses Oleh',
                'Metode Return',
                'Status',
                'Jumlah Item',
                'Total Retur (Rp)',
            ],

            default => [],
        };
    }

    // ── Row mapping per type ──────────────────────────────────

    public function map($row): array
    {
        return match ($this->type) {

            'sales' => [
                $row->created_at?->format('d/m/Y H:i'),
                $row->invoice_number,
                $row->customer?->name ?? 'Walk-in',
                $row->user?->name,
                $row->payment_method,
                $row->items_count ?? $row->items->count(),
                $row->total,
            ],

            'purchases' => [
                $row->purchase_date?->format('d/m/Y'),
                $row->reference_number,
                $row->supplier_invoice ?? '-',
                $row->supplier?->name ?? '-',
                $row->user?->name,
                $row->status,
                $row->payment_status,
                $row->items->count(),
                $row->total,
                $row->paid,
                $row->due,
            ],

            'stock' => [
                $row->name,
                $row->sku ?? '-',
                $row->category?->name ?? '-',
                $row->track_stock ? $row->stock : '∞',
                $row->price,
                $row->cost_price,
                $row->track_stock ? ($row->stock * $row->cost_price) : 0,
            ],

            'quotations' => [
                $row->created_at?->format('d/m/Y'),
                $row->code,
                $row->customer?->name ?? '-',
                $row->user?->name,
                $row->valid_until?->format('d/m/Y') ?? '-',
                $row->status,
                $row->items->count(),
                $row->total_amount,
            ],

            'sale_return' => [
                $row->return_date?->format('d/m/Y'),
                $row->reference_number,
                $row->sale?->invoice_number ?? '-',
                $row->sale?->customer?->name ?? 'Walk-in',
                $row->user?->name,
                $row->refund_method ?? '-',
                $row->status,
                $row->items->count(),
                $row->total_refund,
            ],

            'purchase_return' => [
                $row->return_date?->format('d/m/Y'),
                $row->reference_number,
                $row->purchase?->reference_number ?? '-',
                $row->supplier?->name ?? '-',
                $row->user?->name,
                $row->return_method ?? '-',
                $row->status,
                $row->items->count(),
                $row->total_return,
            ],

            default => [],
        };
    }

    // ── Styling ───────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a5f'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}