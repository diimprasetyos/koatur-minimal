@php
$paperWidthMm = (int) ($paperWidthMm ?? env('POS_PAPER_WIDTH', 58));
$pageW = $paperWidthMm <= 58 ? '58mm' : '80mm' ;

    function rpFmt(float $n): string {
    return 'Rp ' . number_format($n, 0, ',' , '.' );
    }

    $methodLabel=match ($receipt['payment_method']) { 'transfer'=> 'TRANSFER BANK',
    'ewallet' => 'E-WALLET',
    default => 'CASH',
    };
    @endphp
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Struk #{{ $receipt['invoice_number'] }}</title>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            html,
            body {
                font-family: "Courier New", Courier, monospace;
                font-size: 8pt;
                line-height: 1.3;
                background: #fff;
                color: #000;
            }

            @media screen {
                body {
                    display: flex;
                    justify-content: center;
                    padding: 20px;
                    background: #ddd;
                }

                .receipt {
                    background: #fff;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
                }
            }

            .receipt {
                width: {
                        {
                        $pageW
                    }
                }

                ;
                padding: 2mm 0 6mm;
                word-wrap: break-word;
                overflow-wrap: break-word;
                word-break: break-word;
            }

            .receipt div {
                word-wrap: break-word;
                overflow-wrap: break-word;
                word-break: break-word;
            }

            .center {
                text-align: center;
            }

            .bold {
                font-weight: bold;
            }

            .mt {
                margin-top: 2mm;
            }

            .indent {
                padding-left: 4mm;
            }

            .divider {
                overflow: hidden;
                white-space: nowrap;
            }

            @media print {
                @page {
                    size: {
                            {
                            $pageW
                        }
                    }

                    auto;
                    margin: 0;
                }

                html,
                body {
                    width: {
                            {
                            $pageW
                        }
                    }

                    ;
                    margin: 0;
                    padding: 0;
                }

                .receipt {
                    width: {
                            {
                            $pageW
                        }
                    }

                    ;
                    padding: 0 0 6mm;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    word-break: break-word;
                }

                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>

    <body>

        <div class="no-print" style="position:fixed;top:10px;right:10px;z-index:99;display:flex;gap:8px;font-family:sans-serif">
            <button onclick="window.print()" style="background:#2563EB;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:14px;font-weight:700;cursor:pointer">🖨️ Print</button>
            <button onclick="window.close()" style="background:#fff;color:#555;border:1px solid #ddd;border-radius:8px;padding:8px 14px;font-size:14px;cursor:pointer">✕ Tutup</button>
        </div>

        <div class="receipt">

            <div class="center bold">{{ strtoupper($receipt['store_name']) }}</div>
            @if (!empty($receipt['store_address']))
            <div class="center">{{ $receipt['store_address'] }}</div>
            @endif
            @if (!empty($receipt['store_phone']))
            <div class="center">{{ $receipt['store_phone'] }}</div>
            @endif

            <div class="divider">--------------------------------</div>

            <div>Tanggal: {{ $receipt['date'] }}</div>
            <div>Kasir: {{ $receipt['cashier_name'] }}</div>
            <div>ID: #{{ $receipt['invoice_number'] }}</div>

            <div class="divider">--------------------------------</div>

            @foreach ($receipt['items'] as $i => $item)
            <div>{{ $i + 1 }}. {{ $item['name'] }}</div>
            <div class="indent">{{ $item['qty'] }} x {{ rpFmt((float) $item['price']) }}</div>
            <div class="indent">= {{ rpFmt((float) $item['total']) }}</div>
            @endforeach

            <div class="divider">--------------------------------</div>

            @if ((float) ($receipt['discount'] ?? 0) > 0)
            <div>Subtotal: {{ rpFmt((float) $receipt['subtotal']) }}</div>
            <div>Diskon: -{{ rpFmt((float) $receipt['discount']) }}</div>
            <div class="divider">--------------------------------</div>
            @endif

            <div class="bold">Total: {{ rpFmt((float) $receipt['total']) }}</div>
            <div>Bayar ({{ $methodLabel }}): {{ rpFmt((float) $receipt['paid']) }}</div>
            <div>Kembalian: {{ rpFmt((float) $receipt['change']) }}</div>

            <div class="divider">--------------------------------</div>

            <div class="center">{{ $receipt['footer_note'] ?? 'Terimakasih Telah Berbelanja' }}</div>

            <div class="center mt">Powered by Koatur POS</div>

        </div>

        <script>
            var _p = false;
            window.addEventListener('load', function() {
                if (_p) return;
                if (window.opener || document.referrer) {
                    _p = true;
                    setTimeout(function() {
                        window.print();
                    }, 400);
                }
            });
        </script>
    </body>

    </html>