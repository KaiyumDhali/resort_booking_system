

{{-- single barcode qrcode print --}}

<!DOCTYPE html>
<html>

<head>
    <title>product_barcode_qrcode</title>
    <style>
        * {
            margin-top: 5px;
            margin-bottom: 5px;
            /* padding-top: 0;
            padding-bottom: 0; */
        }

        p {
            font-size: 10px;
            overflow-wrap: break-word;
            white-space: normal;
            padding-bottom: 0px;
            font-weight: 500;
        }

        .dotted-line {
            border: none;
            border-top: 2px dotted black;
            margin-bottom: 10px;
        }

        @page {
            /* size: 2.4in 1.25in; */
            size: 2in 3in;
        }

        .rotate {
            transform: rotate(270deg)
        }
    </style>
</head>

<body>

    <div class="container">
        @for ($i = 1; $i <= $product_serial_qty; $i++)
            <div class="row" style="text-align: center;">

                <div style="text-align: center; display: inline-block;">
                    <p>{{ $product_serials->product->product_name }}</p>
                    <div style="margin-top: -10px; margin-bottom: 5px">
                        {!! DNS1D::getBarcodeHTML($product_serials->product_code, 'C128', 1, 25, 'black', false, 0.2) !!}
                    </div>
                    <p style="">{{ $product_serials->product_serial }}</p>
                    @php
                        $qrcode = base64_encode(
                            QrCode::format('svg')
                                ->size(60)
                                ->errorCorrection('H')
                                ->generate(url('/findproduct') . '/' . $product_serials->product_serial),
                        );
                    @endphp
                    <div style="margin-top: -5px; margin-bottom: 10px">
                        <img class="img-fluid" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                    </div>
                    <hr class="dotted-line" />
                    <p style="">{{ $product_serials->product->product_name }}</p>
                    <div style="margin-top: -10px; margin-bottom: 0px;">
                        {!! DNS1D::getBarcodeHTML($product_serials->product_code, 'C128', 1, 25, 'black', false, 0.2) !!}
                    </div>

                </div>


            </div>
        @endfor
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
