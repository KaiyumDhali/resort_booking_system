

{{-- single barcode print --}}

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
            /* size: 2.4in 0.5in; */
            size: 3in 2in;
        }

        </style>


    </head>
    <body>
        <div class="container">
            @for ($i = 1; $i <= $product_serial_qty; $i++)
            <div class="row" style="text-align:center;">
                <div class="col-12" >
                    <div class="print" style="font-size: 14px; margin-top: 30px; ">{{ $product_serials->product->product_name }}</div>
                    <div class="" style="text-align: center; display: inline-block; margin-bottom:0; padding-bottom: 0;">
                    {!! DNS1D::getBarcodeHTML($product_serials->product_code, 'C128', 1, 20, 'black', false, 0.2) !!}
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

                Quagga.onDetected(function (result) {
                    var code = result.codeResult.code;
                    window.location.href = '/findproduct/' + code; // Redirect to the scanned URL
                });

                Quagga.start();

        </script>
    </body>
</html>