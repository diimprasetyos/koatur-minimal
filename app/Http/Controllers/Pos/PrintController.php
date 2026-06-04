<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class PrintController extends Controller
{
    /**
     * POST /pos/print/receipt
     * Return HTML struk sebagai string → dibuka JS di tab baru.
     */
    public function receipt(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:64',
            'total'          => 'required|numeric|min:0',
            'paid'           => 'required|numeric|min:0',
            'change'         => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,transfer,ewallet',
            'paper_width'    => 'nullable|integer|in:58,80',
            'cashier_name'   => 'nullable|string|max:64',
            'note'           => 'nullable|string|max:256',
            'sale_id'        => 'nullable|integer',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string',
            'items.*.price'  => 'required|numeric|min:0',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.total'  => 'required|numeric|min:0',
        ]);

        $receipt = $this->buildReceiptArray($validated);
        $paperWidthMm = (int) ($validated['paper_width'] ?? env('POS_PAPER_WIDTH', 58));

        // Render HTML → kirim sebagai string (bukan view response)
        // JS akan buka di tab baru via Blob URL
        $html = view('pos.print.receipt', compact('receipt', 'paperWidthMm'))->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    /**
     * GET /pos/print/receipt/{sale}
     * Buka langsung di browser (reprint dari riwayat).
     */
    public function receiptFromSale(Sale $sale)
    {
        abort_unless((int) $sale->tenant_id === (int) session('pos_tenant_id'), 403);
        $sale->load('items.product');

        $receipt      = $this->buildReceiptFromSale($sale);
        $paperWidthMm = (int) env('POS_PAPER_WIDTH', 58);

        // Langsung render halaman — user bisa Ctrl+P dari sini
        return view('print.receipt', compact('receipt', 'paperWidthMm'));
    }

    // ─────────────────────────────────────────────────────────────────────────

    protected function buildReceiptArray(array $data): array
    {
        Carbon::setLocale('id');

        $tenant = \App\Models\Tenant::find(session('pos_tenant_id'));

        $subtotal = collect($data['items'])->sum('total');

        return [
            'store_name'    => $tenant?->name ?? config('app.name'),
            'store_phone'   => $tenant?->phone ?? '',
            'store_address' => $tenant?->address ?? '',

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

            'note' => $data['note']
                ?? env('POS_RECEIPT_NOTE', 'Terima kasih telah berbelanja!'),
        ];
    }

    protected function buildReceiptFromSale(Sale $sale): array
    {
        Carbon::setLocale('id');
        $items = $sale->items->map(fn($i) => [
            'name'  => $i->product->name ?? $i->product_name ?? '-',
            'price' => (float) $i->price,
            'qty'   => (int)   $i->qty,
            'total' => (float) $i->price * $i->qty,
        ])->toArray();

        return [
            'store_name'     => $tenant?->name ?? config('app.name'),
            'store_address' => $tenant?->address ?? '',
            'store_phone'   => $tenant?->phone ?? '',
            'invoice_number' => $sale->invoice_number,
            'date'           => $sale->created_at->translatedFormat('d F Y, H:i'),
            'cashier_name'   => $sale->cashier?->name ?? '-',
            'items'          => $items,
            'subtotal'       => collect($items)->sum('total'),
            'discount'       => (float) ($sale->discount ?? 0),
            'total'          => (float) $sale->total,
            'paid'           => (float) $sale->paid,
            'change'         => (float) $sale->change,
            'payment_method' => $sale->payment_method,
            'note'           => env('POS_RECEIPT_NOTE', 'Terima kasih telah berbelanja!'),
        ];
    }
}
