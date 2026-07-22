<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Window in New Tab</title>
    </head>
    <body onload="window.print();">

        <main class="page-content">
            <div class="container container--flex">
                <div class="page-header">
                    <h1 class="page-header__title">Product Serial Print</h1>
                </div>
                <div class="card add-product card--content-center">
                    <div class="card__wrapper">
                        <div class="">


                            <form class="add-product__form" style="margin-left: 50px;">
                                <div onload="window.print();">
                                    <div class="row">
                                        @foreach($product_serials as $product_serial)

                                        @for ($i = 1; $i <= $product_serial_qty; $i++)
                                        <div class="col-12">
                                            <div class="col-12 form-group form-group--lg">
                                                <p>
                                                    {{ $product_serial->product->product_name }}
                                                </p>
                                                <p>
                                                    {!! DNS1D::getBarcodeSVG("$product_serial->product_code", 'C128',2,50,'black',true) !!}
                                                </p>
                                                <p>
                                                    P-{{ $product_serial->product_serial }}
                                                </p>
                                                <p>
                                                    {!! QrCode::backgroundColor(255, 225, 225, 0)->generate(url('/findproduct') . '/' . $product_serial->product_serial) !!}

                                         
                                                </p>
                                            </div>
                                        </div>
                                        @endfor
                                        @endforeach
                                        <div class="col-12">
                                            <div class="add-product__submit">
                                                <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('productserials.index') }}"><span class="button__text">Cancel</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!--        <script>
                    function openPrintWindow() {
                        var printWindow = window.open('', '_blank');
                        printWindow.document.write('<html><head><title>Print</title></head><body>');
                        printWindow.document.write('<h1>Your content goes here</h1>');
                        // Add more content if needed
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.print();
                    }
        
                    // Call the function onload or trigger it as needed
                    window.onload = openPrintWindow;
                </script>-->

    </body>
</html>


