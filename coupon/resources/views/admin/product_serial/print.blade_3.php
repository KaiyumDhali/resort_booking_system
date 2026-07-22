<!DOCTYPE html>
<html>
    <head>
        <title>Page Title</title>
        <style>
        .print{
            padding-bottom: 0; 
            padding-top: 0; 
            margin-bottom: 0;
            margin-top: 0;
            }
            @page {
            size: 2.4in 3in;
        }
        </style>
    </head>
    <body>

        <div style="">
        @for ($i = 1; $i <= $product_serial_qty; $i++)
            <p class="print" style="text-align: center; font-size: 12px;">{{ $product_serials->product->product_name }}</p>
            <p class="print">{!! DNS1D::getBarcodeHTML("$product_serials->product_code", 'C128', 1, 30, 'black', true) !!}</p>
            <p class="print" style="font-size: 12px; text-align: center;">{{ $product_serials->product_serial }}</p>
            <p class="print">@php $qrcode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate(url('/findproduct') . '/' . $product_serials->product_serial)) @endphp</p>
            <p style="text-align: center;">
                <img class="print" src="data:image/png;base64, {!! $qrcode !!}">
            </p>
        @endfor
        </div>

    </body>
</html>