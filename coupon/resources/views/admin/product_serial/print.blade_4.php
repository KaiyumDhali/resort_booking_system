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
            size: 2.4in 2.4in;
        }
        </style>
    </head>
    <body>

        <div style="">
        @for ($i = 1; $i <= $product_serial_qty; $i++)
           
            <div class="print" style="text-align: center; margin-top: 5px;">{{ $product_serials->product->product_name }}</div>
            <div class="" style="display: flex; justify-content: center; align-items: center;">{!! DNS1D::getBarcodeHTML("$product_serials->product_code", 'C128', 1, 40, 'black', false, 2) !!}</div>
            <div class="print" style="font-size: 12px; text-align: center; margin-top: 5px;">{{ $product_serials->product_serial }}</div>
            @php $qrcode = base64_encode(QrCode::format('svg')->size(130)->errorCorrection('H')->generate(url('/findproduct') . '/' . $product_serials->product_serial)) @endphp
            <div style="text-align: center; margin-top: 5px; margin-bottom: 0;">
                <img class="print" src="data:image/png;base64, {!! $qrcode !!}">
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

                Quagga.onDetected(function (result) {
                    var code = result.codeResult.code;
                    window.location.href = '/findproduct/' + code; // Redirect to the scanned URL
                });

                Quagga.start();

        </script>
    </body>
</html>