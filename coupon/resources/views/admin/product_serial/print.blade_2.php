

<!DOCTYPE html>
<html>
    <head>
        <title>Page Title</title>
    </head>
    <body>

        <div>
        @for ($i = 1; $i <= $product_serial_qty; $i++)
            <p>{{ $product_serials->product->product_name }}</p>
            <p>{!! DNS1D::getBarcodeHTML("$product_serials->product_code", 'C128', 2, 50, 'black', true) !!}</p>
            <p>{{ $product_serials->product_serial }}</p>
            <p>{!! QrCode::backgroundColor(255, 225, 225, 0)->generate(url('/findproduct') . '/' . $product_serials->product_serial) !!}</p>
        @endfor

          
            


        </div>

    </body>
</html>





