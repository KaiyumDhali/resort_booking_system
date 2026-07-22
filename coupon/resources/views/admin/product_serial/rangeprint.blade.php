

{{-- multiple barcode qrcode print --}}


<!DOCTYPE html>
<html>

<head>
    <title>product_barcode_qrcode</title>
    <style>
        * {
            margin-top: 0px;
            margin-bottom: 0px;
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
        <style>
    /* .page {
        page-break-after: always;
    } */
</style>

    </style>
</head>

<body>
    <div class="container">
        @foreach ($product_serials as $product_serial)
            @for ($i = 1; $i <= $product_serial_qty; $i++)
                <div class="row" style="text-align:center;">
                    
                    <div id="div1" class="" style="text-align: center; display: inline-block; height: 3in;">
                        <p style="margin-top: 20px;">{{ $product_serial->product->product_name }}</p>
                        <div style="margin-top: 5px; margin-bottom: 5px">
                            {!! DNS1D::getBarcodeHTML("$product_serial->product_code", 'C128', 1, 20, 'black', false, 0.2) !!}
                        </div>
                        <p style="">{{ $product_serial->product_serial }}</p>

                        @php
                            $qrcode = base64_encode(
                                QrCode::format('svg')
                                    ->size(60)
                                    ->errorCorrection('H')
                                    ->generate(url('/findproduct') . '/' . $product_serial->product_serial),
                            );
                        @endphp
                        <div style="margin-top: 5px; margin-bottom: 10px">
                            <img class="print" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                        </div>

                        <hr class="dotted-line" />

                        <p style="">{{ $product_serial->product->product_name }}</p>
                        
                        <div style="margin-top: 5px; margin-bottom: 5px;">
                            {!! DNS1D::getBarcodeHTML($product_serial->product_code, 'C128', 1, 25, 'black', false, 0.2) !!}
                        </div>
                    </div>


                    <div id="div2" class="" style="text-align: center; display: inline-block;  height: 3in;">
                        <p style="margin-top: 20px;">{{ $product_serial->product->product_name }}</p>
                        <p style="margin-top: 5px; margin-bottom: 5px;">{{ $product_serial->product_serial }}</p>
                        @php
                            $qrcode = base64_encode(
                                QrCode::format('svg')
                                    // ->size(120)
                                    ->size(120)
                                    ->errorCorrection('H')
                                    ->generate(url('/findproduct') . '/' . $product_serial->product_serial),
                            );
                        @endphp
                        <div style="margin-top: 5px; margin-bottom: 10px">
                            <img class="print" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                        </div>

                        <div style="margin-top: 5px; margin-bottom: 5px;">
                            {!! DNS1D::getBarcodeHTML($product_serial->product_code, 'C128', 1, 25, 'black', false, 0.2) !!}
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
