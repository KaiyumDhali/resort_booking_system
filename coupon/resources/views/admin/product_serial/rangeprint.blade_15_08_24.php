

{{-- multiple barcode qrcode print --}}


<!DOCTYPE html>
<html>

<head>
    <title>product_barcode_qrcode</title>
    <style>
        * {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;

        }

        .print {
            /* padding-top: 0;
            padding-bottom: 0;
            padding-left: 10px;
            padding-right: 10px;
            margin-top: 0;
            margin-bottom: 0; */

            padding: 0px 10px;
            margin-top: 0;
            margin-bottom: 0;

        }

        @page {
            /* size: 2.4in 1.25in; */
            size: 2in 3in;
        }

        .rotate {
            transform: rotate(270deg)
        }

        .dotted-line {
            border: none;
            border-top: 2px dotted black;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        @foreach ($product_serials as $product_serial)
            @for ($i = 1; $i <= $product_serial_qty; $i++)
                <div class="row" style="text-align:center;">
                    <div class="rotate">
                        <div class="print" style="font-size: 10px; overflow-wrap: break-word; min-height: 3em; line-height: 1.5;">
                            {{ $product_serial->product->product_name }}
                        </div>
                        <div class=""
                            style="text-align: center; display: inline-block; margin-bottom:0; padding-bottom: 0;">
                            {!! DNS1D::getBarcodeHTML("$product_serial->product_code", 'C128', 1, 20, 'black', false, 0.2) !!}
                        </div>
                        <div class="print" style="font-size: 10px;">
                            {{ $product_serial->product_serial }}
                        </div>
                        @php
                            $qrcode = base64_encode(
                                QrCode::format('svg')
                                    ->size(60)
                                    ->errorCorrection('H')
                                    ->generate(url('/findproduct') . '/' . $product_serial->product_serial),
                            );
                        @endphp
                        <div style="margin-top: 3px;">
                            <img class="print" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                        </div>

                        <hr style="margin-top: 10px;" class="dotted-line" />

                        <div class="print"
                            style="font-size: 10px; margin-top: 5px; overflow-wrap: break-word; min-height: 3em; line-height: 1.5;">
                            {{ $product_serial->product->product_name }}
                        </div>

                        <div class=""
                            style="text-align: center; display: inline-block; margin-bottom:0; padding-bottom: 0;">
                            {!! DNS1D::getBarcodeHTML($product_serial->product_code, 'C128', 1, 20, 'black', false, 0.2) !!}
                        </div>
                    </div>

                    <div class="rotate">
                        <div class="row" style="text-align:center; margin-top: 10px;">
                            <div class="print" style="font-size: 10px;">
                                {{ $product_serial->product->product_name }}</div>
                            <div class="print" style="font-size: 10px;">
                                {{ $product_serial->product_serial }}
                            </div>
                            @php
                                $qrcode = base64_encode(
                                    QrCode::format('svg')
                                        // ->size(120)
                                        ->size(110)
                                        ->errorCorrection('H')
                                        ->generate(url('/findproduct') . '/' . $product_serial->product_serial),
                                );
                            @endphp
                            <div style="text-align:center;">
                                <img class="print" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                            </div>

                            <div class=""
                                style="text-align: center; margin-top: 10px; display: inline-block; margin-bottom:0; padding-bottom: 0;">
                                {!! DNS1D::getBarcodeHTML($product_serial->product_code, 'C128', 1, 20, 'black', false, 0.2) !!}
                            </div>

                        </div>
                    </div>
                </div>
            @endfor
        @endforeach
    </div>



    <script type="text/javascript">
        // Add this to public/js/scanner.js
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#qrcode-image'), // Select the QR code image
            },
            decoder: {
                readers: ["code_128_reader"],
            },
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            window.location.href = '/findproduct/' + code; // Redirect to the scanned URL
        });

        Quagga.start();
    </script>



</body>

</html>
