{{-- resources/views/pos/print/receipt.blade.php --}}
{{--
    Template PDF struk thermal — dioptimasi untuk DomPDF.
    DomPDF tidak support CSS variables dan display:table dengan baik,
    jadi semua layout pakai <table> HTML native + inline style.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @php
        $isNarrow  = ($paperWidthMm ?? 58) == 58;
        $szBase    = $isNarrow ? '8pt'  : '9pt';
        $szSm      = $isNarrow ? '7pt'  : '8pt';
        $szLg      = $isNarrow ? '11pt' : '13pt';
        $szXl      = $isNarrow ? '12pt' : '14pt';
        $pageW     = $isNarrow ? '54mm' : '76mm';
        $padH      = $isNarrow ? '3mm'  : '4mm';
    @endphp

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body {
        width: {{ $pageW }};
        font-family: 'Courier New', Courier, monospace;
        font-size: {{ $szBase }};
        color: #111;
        background: #fff;
        line-height: 1.5;
    }

    .wrap {
        width: {{ $pageW }};
        padding: 4mm {{ $padH }} 8mm;
    }

    /* Tabel full-width helper */
    table { width: 100%; border-collapse: collapse; }
    td    { padding: 0; vertical-align: top; }

    /* ── Header toko ── */
    .store-name {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szXl }};
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-bottom: 1mm;
    }
    .store-sub {
        font-size: {{ $szSm }};
        color: #555;
        text-align: center;
        line-height: 1.4;
    }

    /* ── Divider ── */
    .hr-dashed {
        border: none;
        border-top: 1px dashed #aaa;
        margin: 2mm 0;
    }
    .hr-solid {
        border: none;
        border-top: 1px solid #111;
        margin: 2mm 0;
    }
    .hr-bold {
        border: none;
        border-top: 2px solid #111;
        margin: 1.5mm 0;
    }

    /* ── Label "Struk Pembayaran" ── */
    .receipt-label {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szSm }};
        text-align: center;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 2mm;
    }

    /* ── Meta rows ── */
    .meta-key {
        font-size: {{ $szSm }};
        color: #666;
        width: 38%;
    }
    .meta-sep {
        font-size: {{ $szSm }};
        color: #666;
        width: 5%;
    }
    .meta-val {
        font-size: {{ $szSm }};
        color: #111;
        font-weight: bold;
        width: 57%;
    }

    /* ── Item rows ── */
    .item-name {
        font-size: {{ $szBase }};
        font-weight: bold;
        color: #111;
        width: 65%;
        padding-right: 1mm;
        word-break: break-word;
    }
    .item-total {
        font-size: {{ $szBase }};
        font-weight: bold;
        color: #111;
        width: 35%;
        text-align: right;
        white-space: nowrap;
    }
    .item-detail {
        font-size: {{ $szSm }};
        color: #666;
        padding-left: 2mm;
        padding-top: 0.5mm;
        padding-bottom: 1mm;
    }

    /* ── Summary rows ── */
    .sum-label {
        font-size: {{ $szBase }};
        color: #555;
        width: 55%;
    }
    .sum-val {
        font-size: {{ $szBase }};
        color: #111;
        width: 45%;
        text-align: right;
        white-space: nowrap;
    }

    /* Total — baris paling penting */
    .total-label {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szLg }};
        font-weight: bold;
        color: #000;
        width: 45%;
    }
    .total-val {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szLg }};
        font-weight: bold;
        color: #000;
        width: 55%;
        text-align: right;
        white-space: nowrap;
    }

    /* Kembalian */
    .change-label, .change-val {
        font-size: {{ $szBase }};
        font-weight: bold;
        color: #000;
    }
    .change-val { text-align: right; white-space: nowrap; }

    /* Diskon */
    .discount-val { color: #c00; text-align: right; white-space: nowrap; }

    /* Method badge */
    .method-wrap { margin: 1.5mm 0; }
    .method-badge {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szSm }};
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        border: 1px solid #111;
        padding: 0.5mm 2mm;
        display: inline-block;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding-top: 2.5mm;
        margin-top: 2.5mm;
        border-top: 1px dashed #aaa;
    }
    .footer-note {
        font-family: Arial, Helvetica, sans-serif;
        font-size: {{ $szSm }};
        color: #555;
        line-height: 1.6;
    }
    .footer-wave {
        font-size: {{ $szSm }};
        color: #aaa;
        letter-spacing: 3px;
        margin-top: 1.5mm;
    }
</style>
</head>
<body>
<div class="wrap">

    {{-- ── Header Toko ── --}}
    <div class="store-name">{{ $receipt['store_name'] }}</div>
    @if(!empty($receipt['store_address']))
        <div class="store-sub">{{ $receipt['store_address'] }}</div>
    @endif
    @if(!empty($receipt['store_phone']))
        <div class="store-sub">{{ $receipt['store_phone'] }}</div>
    @endif

    <div class="hr-dashed"></div>

    <div class="receipt-label">Struk Pembayaran</div>

    {{-- ── Meta ── --}}
    <table>
        <tr>
            <td class="meta-key">Tanggal</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $receipt['date'] }}</td>
        </tr>
        <tr>
            <td class="meta-key">Kasir</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $receipt['cashier_name'] }}</td>
        </tr>
        <tr>
            <td class="meta-key">No. Struk</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">#{{ $receipt['invoice_number'] }}</td>
        </tr>
    </table>

    <div class="hr-solid"></div>

    {{-- ── Items ── --}}
    <table>
        @foreach($receipt['items'] as $index => $item)
        <tr>
            <td class="item-name">{{ ($index + 1) }}. {{ $item['name'] }}</td>
            <td class="item-total">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="item-detail">
                {{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </table>

    <div class="hr-dashed"></div>

    {{-- ── Summary ── --}}
    <table>
        {{-- Subtotal + diskon (hanya tampil jika ada diskon) --}}
        @if($receipt['discount'] > 0)
        <tr>
            <td class="sum-label">Subtotal</td>
            <td class="sum-val">Rp {{ number_format($receipt['subtotal'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="sum-label">Diskon</td>
            <td class="sum-val discount-val">- Rp {{ number_format($receipt['discount'], 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    {{-- Total dengan garis tebal di atas & bawah --}}
    <div class="hr-bold"></div>
    <table>
        <tr>
            <td class="total-label">TOTAL</td>
            <td class="total-val">Rp {{ number_format($receipt['total'], 0, ',', '.') }}</td>
        </tr>
    </table>
    <div class="hr-bold"></div>

    {{-- Metode bayar --}}
    @php
        $methodLabel = match($receipt['payment_method']) {
            'transfer' => 'Transfer Bank',
            'ewallet'  => 'E-Wallet',
            default    => 'Tunai / Cash',
        };
    @endphp
    <div class="method-wrap">
        <span class="method-badge">{{ $methodLabel }}</span>
    </div>

    {{-- Dibayar & kembalian --}}
    <table>
        <tr>
            <td class="sum-label">Dibayar</td>
            <td class="sum-val">Rp {{ number_format($receipt['paid'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="change-label">Kembalian</td>
            <td class="change-val">Rp {{ number_format($receipt['change'], 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-note">{{ $receipt['note'] }}</div>
        <div class="footer-wave">~ ~ ~ ~ ~</div>
    </div>

</div>
</body>
</html>
