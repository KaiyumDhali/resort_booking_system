{{-- single qrcode print --}}


<!DOCTYPE html>
<html>

<head>
    <title>product_barcode_qrcode</title>
    <style>
        * {
            margin-top: 5px;
            margin-bottom: 5px;

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

        @page {
            size: 3.9in 8in;
        }
    </style>


</head>

<body>
    <div class="container">
        @for ($i = 1; $i <= $product_serial_qty; $i++)
            <div class="row" style="text-align:center; margin-top: 180px;">
                <div style="text-align: center; display: inline-block;">
                    {{-- <p>{{ $product_serials->product->product_name }}</p> --}}

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
                                ->generate(url('/findproduct') . '/' . $product_serials->coupon_serial),
                        );
                    @endphp
                    <div style="margin-top: 15px;">
                        <img class="img-fluid" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
                    </div>
                    <h3 style="">{{ $product_serials->coupon_serial }}</h3>
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
