<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PrintController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // PDF dari payload JS
    // ─────────────────────────────────────────────────────────────────────────

    public function pdf(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:64',
            'total'          => 'required|numeric|min:0',
            'paid'           => 'required|numeric|min:0',
            'change'         => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,transfer,ewallet',
            'paper_width'    => 'nullable|integer|in:58,80',
            'cashier_name'   => 'nullable|string|max:64',
            'note'           => 'nullable|string|max:128',
            'sale_id'        => 'nullable|integer',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string',
            'items.*.price'  => 'required|numeric|min:0',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.total'  => 'required|numeric|min:0',
        ]);

        $receipt = $this->buildReceiptArray($validated);
        $pdf     = $this->generatePdf($receipt, $validated['paper_width'] ?? (int) env('POS_PAPER_WIDTH', 58));

        // Kembalikan sebagai base64 — browser buka via JS tanpa redirect
        return response()->json([
            'success'  => true,
            'filename' => 'struk-' . $receipt['invoice_number'] . '.pdf',
            'pdf_b64'  => base64_encode($pdf->output()),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PDF reprint dari Sale model (opsional — untuk riwayat transaksi)
    // ─────────────────────────────────────────────────────────────────────────

    public function pdfFromSale(Sale $sale): Response
    {
        abort_unless($sale->tenant_id === session('pos_tenant_id'), 403);
        $sale->load('items.product');

        $receipt = $this->buildReceiptFromSale($sale);
        $pdf     = $this->generatePdf($receipt, (int) env('POS_PAPER_WIDTH', 58));

        return $pdf->stream('struk-' . $sale->invoice_number . '.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core: generate DomPDF instance
    // ─────────────────────────────────────────────────────────────────────────

    protected function generatePdf(array $receipt, int $paperWidthMm): \Barryvdh\DomPDF\PDF
    {
        // ── Kalkulasi tinggi kertas ──────────────────────────────────────────
        // Setiap item = 2 baris (nama+total & qty×harga) ≈ 10mm
        // Header toko      : ~20mm
        // Meta (3 baris)   : ~12mm
        // Dividers + label : ~10mm
        // Summary (total dst): ~25mm
        // Footer           : ~15mm
        // Buffer bawah     : +10mm
        $itemCount = count($receipt['items']);
        $heightMm  = 20          // header toko
                   + 12          // meta
                   + 10          // dividers + label struk
                   + ($itemCount * 10)  // items (2 baris/item)
                   + 25          // summary, total, metode bayar
                   + 15          // footer
                   + 10;         // buffer bawah (jangan sampai terpotong)

        // Minimum 80mm agar tidak terlalu pendek
        $heightMm = max(80, $heightMm);

        $pdf = app('dompdf.wrapper');

        // setPaper: [x1, y1, x2, y2] dalam points (1mm = 2.8346pt)
        $pdf->setPaper([0, 0, $this->mmToPt($paperWidthMm), $this->mmToPt($heightMm)]);

        $pdf->setOptions([
            'defaultFont'          => 'Courier',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'isFontSubsettingEnabled' => true,
            'dpi'                  => 96,
            // Matikan page break otomatis — struk harus satu halaman penuh
            'isPhpEnabled'         => false,
        ]);

        $html = view('pos.print.receipt', compact('receipt', 'paperWidthMm'))->render();
        $pdf->loadHTML($html);

        return $pdf;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildReceiptArray(array $data): array
    {
        Carbon::setLocale('id');
        $subtotal = collect($data['items'])->sum('total');

        return [
            'store_name'     => env('POS_STORE_NAME', config('app.name', 'Kasir POS')),
            'store_address'  => env('POS_STORE_ADDRESS', ''),
            'store_phone'    => env('POS_STORE_PHONE', ''),
            'invoice_number' => $data['invoice_number'],
            'date'           => Carbon::now()->translatedFormat('d F Y, H:i'),
            'cashier_name'   => $data['cashier_name']
                                ?? Auth::guard('pos')->user()?->name
                                ?? '-',
            'items'          => $data['items'],
            'subtotal'       => $subtotal,
            'discount'       => max(0, $subtotal - $data['total']),
            'total'          => $data['total'],
            'paid'           => $data['paid'],
            'change'         => $data['change'],
            'payment_method' => $data['payment_method'],
            'note'           => $data['note'] ?? env('POS_RECEIPT_NOTE', 'Terima kasih telah berbelanja!'),
        ];
    }

    protected function buildReceiptFromSale(Sale $sale): array
    {
        Carbon::setLocale('id');
        $items = $sale->items->map(fn($i) => [
            'name'  => $i->product->name ?? $i->product_name ?? '-',
            'price' => $i->price,
            'qty'   => $i->qty,
            'total' => $i->price * $i->qty,
        ])->toArray();

        return [
            'store_name'     => env('POS_STORE_NAME', config('app.name', 'Kasir POS')),
            'store_address'  => env('POS_STORE_ADDRESS', ''),
            'store_phone'    => env('POS_STORE_PHONE', ''),
            'invoice_number' => $sale->invoice_number,
            'date'           => $sale->created_at->translatedFormat('d F Y, H:i'),
            'cashier_name'   => $sale->cashier?->name ?? '-',
            'items'          => $items,
            'subtotal'       => collect($items)->sum('total'),
            'discount'       => $sale->discount ?? 0,
            'total'          => $sale->total,
            'paid'           => $sale->paid,
            'change'         => $sale->change,
            'payment_method' => $sale->payment_method,
            'note'           => env('POS_RECEIPT_NOTE', 'Terima kasih telah berbelanja!'),
        ];
    }

    /** Konversi milimeter ke points (satuan DomPDF) */
    private function mmToPt(float $mm): float
    {
        return $mm * 2.8346;
    }
}
