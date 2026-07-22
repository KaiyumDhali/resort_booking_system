
<!DOCTYPE html>
<html>
    <head>
        <title>product_barcode_qrcode</title>
        <style>
        *{  margin-top: 0;
            margin-bottom: 0;
            padding-top: 0; 
            padding-bottom: 0; 

            }

        .print{
            padding-top: 0; 
            padding-bottom: 0; 
            margin-top: 0;
            margin-bottom: 0;

            }
            @page {
            /* size: 2.4in 1.25in; */
            size: 3in 2in;
        }


        </style>
    </head>
    <body>
        <div class="container">
        @foreach($product_serials as $product_serial)
        @for ($i = 1; $i <= $product_serial_qty; $i++)
            <div class="row" style="text-align:center;">
                <div class="col-12" >
                    <div class="print" style="font-size: 8px; margin-top: 10px;">{{ $product_serial->product->product_name }}</div>
                    <div class="" style="display: inline-block;">{!! DNS1D::getBarcodeHTML("$product_serial->product_code", 'C128', 1, 20, 'black', false, 0.5) !!}</div>
                    <div class="print" style="font-size: 8px;">{{ $product_serial->product_serial }}</div>
                    @php $qrcode = base64_encode(QrCode::format('svg')->size(60)->errorCorrection('H')->generate(url('/findproduct') . '/' . $product_serial->product_serial)) @endphp
                    <div style="">
                        <img class="print" src="data:image/png;base64, {!! $qrcode !!}" alt="QR Code">
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

                        Quagga.onDetected(function (result) {
                            var code = result.codeResult.code;
                            window.location.href = '/findproduct/' + code; // Redirect to the scanned URL
                        });

                        Quagga.start();

        </script>



    </body>
</html>


