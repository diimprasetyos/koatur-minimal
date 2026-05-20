<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: sans-serif;
            margin: 5px;
        }

        .wrapper {
            width: 100%;
        }

        .barcode-item {
            width: 29%;
            height: 85px;
            border: 1px solid #000;
            display: inline-block;
            margin: 4px;
            text-align: center;
            vertical-align: top;
            padding: 8px;
            box-sizing: border-box;
        }

        .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .product-code {
            font-size: 11px;
            margin-top: 6px;
        }

        .barcode-image {
            width: 100%;
            height: 45px;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        @foreach ($products as $item)
            @for ($i = 0; $i < $item['barcode_qty']; $i++)
                <div class="barcode-item">

                    <div class="product-name">
                        {{ $item['product']->name }}
                    </div>

                    <img class="barcode-image"
                        src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item['product']->sku, 'C128') }}">

                    <div class="product-code">
                        {{ $item['product']->sku }}
                    </div>

                </div>
            @endfor
        @endforeach

    </div>

</body>

</html>
