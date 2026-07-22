<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Window in New Tab</title>
        
<!--        <style>
            img.qr-code {
    display: block;
}
        </style>-->
    </head>
    <body>
        
        

        <main class="page-content">
            <div class="container container--flex">
                <div class="page-header">
                    <h1 class="page-header__title">Product Barcode QRCode Print</h1>
                </div>
                <div class="card add-product card--content-center">
                    <div class="card__wrapper">
                        <div class="">


                            <form class="add-product__form" style="margin-left: 50px;">
                                <div onload="window.print();">
                                    <div class="row">
                                        @for ($i = 1; $i <= $product_qty; $i++)
                                        <div class="col-12">
                                            <div class="col-12 form-group form-group--lg">
                                                <p>
                                                    {{ $product->id }}
                                                </p>
                                                <p>
                                                    {{ $product->product_name }}
                                                </p>
                                                <p>
                                                    {!! DNS1D::getBarcodeSVG("$product->product_code", 'C128',2,50,'black',true) !!}
                                                </p>
                                                <p>
                                                    P-{{ $product->product_code }}
                                                </p>
                                                <p>
                                                     {!! QrCode::backgroundColor(255,225,225, 0)->generate('http://nrbtelecom.com/saffron_jusal/public/index.php'); !!}
                                                </p>
                                            </div>
                                        </div>
                                        @endfor
                                        <div class="col-12">
                                            <div class="add-product__submit">
                                                <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('products.index') }}"><span class="button__text">Cancel</span></a>
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

<script>
    setTimeout(function() {
        window.print();
    }, 1000); // Adjust the delay time as needed (in milliseconds).
</script>


    </body>
</html>


