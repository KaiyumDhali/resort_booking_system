{{-- multiple qrcode print --}}



<!DOCTYPE html>
<html>

<head>
    <title>product_barcode_qrcode</title>
    <style>
        * {
            margin-top: 0px;
            margin-bottom: 0px;

        }

        p {
            font-size: 10px;
            overflow-wrap: break-word;
            white-space: normal;
            padding-bottom: 0px;
            font-weight: 500;
        }

        .rotate {
            transform: rotate(270deg)
        }

        .print-page {
            page-break-after: always;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .content-wrapper {
            display: inline-block;
        }

        @media print {

            body,
            html {
                margin: 0;
                padding: 0;
            }

            .print-page {
                page-break-after: always;
            }
        }

        @page {
            /* size: 2in 3in; */
            size: 3.9in 8in;
        }
    </style>
</head>

<body>
    <div class="container">
        @foreach ($product_serials as $product_serial)
            @for ($i = 1; $i <= $product_serial_qty; $i++)
                <div class="print-page" style="text-align:center; margin-top: 180px;">
                    <div class="content-wrapper">
                        <h3>Wonder Park & Eco Resort</h3>
                        <address>
                            Marjal, Raipura-1630. Narsingdi Dhaka Division, Bangladesh
                        </address>

                        <h3 style="margin-top: 10px;">Eid Special Coupon</h3>

                        @php
                            $qrcode = base64_encode(
                                QrCode::format('svg')
                                    ->size(210)
                                    ->errorCorrection('H')
                                    ->generate(url('/findproduct') . '/' . $product_serial->coupon_serial),
                            );
                        @endphp

                        <div style="margin-top: 15px;">
                            <img class="img-fluid" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                        </div>

                        <h3 style="margin-top: 10px;">{{ $product_serial->coupon_serial }}</h3>
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
