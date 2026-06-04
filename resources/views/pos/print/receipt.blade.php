@php
    $paperWidthMm = (int) ($paperWidthMm ?? env('POS_PAPER_WIDTH', 58));
    $isNarrow = $paperWidthMm <= 58;
    $pageW = $isNarrow ? '58mm' : '80mm';
    $cols = $isNarrow ? 28 : 42;
    $fsPt = $isNarrow ? '8pt' : '9pt';

    function strRow(string $left, string $right, int $width): string
    {
        $rLen = mb_strlen($right);
        $left = mb_substr($left, 0, $width - $rLen - 1);
        $space = $width - mb_strlen($left) - $rLen;
        return $left . str_repeat(' ', max(1, $space)) . $right;
    }
    function strLine(int $width, string $char = '-'): string
    {
        return str_repeat($char, $width);
    }
    function strCenter(string $text, int $width): string
    {
        $len = mb_strlen($text);
        if ($len >= $width) {
            return $text;
        }
        return str_repeat(' ', intdiv($width - $len, 2)) . $text;
    }
    function rpFmt(float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    $lines = [];

    foreach (explode("\n", wordwrap(mb_strtoupper($receipt['store_name']), $cols, "\n", true)) as $l) {
        $lines[] = strCenter($l, $cols);
    }
    if (!empty($receipt['store_address'])) {
        foreach (explode("\n", wordwrap($receipt['store_address'], $cols, "\n", true)) as $l) {
            $lines[] = strCenter($l, $cols);
        }
    }
    if (!empty($receipt['store_phone'])) {
        $lines[] = strCenter($receipt['store_phone'], $cols);
    }

    $lines[] = strLine($cols, '-');
    $lines[] = strCenter('STRUK PEMBAYARAN', $cols);
    $lines[] = strLine($cols, '-');

    foreach (
        [
            'Tanggal' => $receipt['date'],
            'Kasir' => $receipt['cashier_name'],
            'No.' => '#' . $receipt['invoice_number'],
        ]
        as $key => $val
    ) {
        if (mb_strlen($key) + 1 + mb_strlen($val) > $cols) {
            $lines[] = $key;
            foreach (explode("\n", wordwrap($val, $cols, "\n", true)) as $vl) {
                $lines[] = '  ' . $vl;
            }
        } else {
            $lines[] = strRow($key, $val, $cols);
        }
    }

    $lines[] = strLine($cols, '-');

    foreach ($receipt['items'] as $i => $item) {
        $totalStr = rpFmt((float) $item['total']);
        $maxNW = $cols - mb_strlen($totalStr) - 1;
        $nameLines = explode("\n", wordwrap($i + 1 . '. ' . $item['name'], $maxNW, "\n", true));
        $lines[] = strRow(mb_substr($nameLines[0], 0, $maxNW), $totalStr, $cols);
        for ($j = 1; $j < count($nameLines); $j++) {
            $lines[] = '   ' . $nameLines[$j];
        }
        $lines[] = '   ' . $item['qty'] . ' x ' . rpFmt((float) $item['price']);
    }

    $lines[] = strLine($cols, '-');

    if ((float) $receipt['discount'] > 0) {
        $lines[] = strRow('Subtotal', rpFmt((float) $receipt['subtotal']), $cols);
        $lines[] = strRow('Diskon', '- ' . rpFmt((float) $receipt['discount']), $cols);
        $lines[] = strLine($cols, '-');
    }

    $lines[] = strLine($cols, '=');
    $lines[] = strRow('TOTAL', rpFmt((float) $receipt['total']), $cols);
    $lines[] = strLine($cols, '=');

    $methodLabel = match ($receipt['payment_method']) {
        'transfer' => 'TRANSFER BANK',
        'ewallet' => 'E-WALLET',
        default => 'TUNAI / CASH',
    };
    $lines[] = strCenter('[ ' . $methodLabel . ' ]', $cols);
    $lines[] = strRow('Dibayar', rpFmt((float) $receipt['paid']), $cols);
    $lines[] = strRow('Kembalian', rpFmt((float) $receipt['change']), $cols);
    $lines[] = strLine($cols, '-');

    $lines[] = strCenter('Terima kasih telah berbelanja', $cols);

    $receiptText = implode("\n", $lines);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $receipt['invoice_number'] }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            background: #fff;
            color: #000;
            margin: 0;
            padding: 0;
        }

        pre {
            width: 100%;
            margin: 0;
            padding: 1mm;
            font-family: "Courier New", monospace;
            font-size: 8pt;
            line-height: 1.1;
            margin: 0;
            padding: 1mm;
            white-space: pre;
        }

        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 999;
            display: flex;
            gap: 8px;
            font-family: sans-serif;
        }

        .btn-print {
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-close {
            background: #fff;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {

            @page {
                size: {{ $pageW }} auto;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: {{ $pageW }};
            }

            pre {
                width: 100%;
                margin: 0;
                padding: 0;
                font-family: Consolas, "Courier New", monospace;
                font-size: 9pt;
                line-height: 1.0;
                white-space: pre;
                overflow: hidden;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    <pre id="receipt-pre">{{ $receiptText }}</pre>

    <script>
        window.addEventListener('load', function() {
            if (window.opener || document.referrer) {
                setTimeout(function() {
                    window.print();
                }, 300);
            }
        });
    </script>

</body>

</html>
