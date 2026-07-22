@extends('layouts.admin')

@section('content')

<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Other Product view</h1>
        </div>

        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="card__container">
                    <form class="add-product__form">
                        <div class="add-product__row">
                            
                            <div class="add-product__right">
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label>
                                        <div class="input-group">
                                            <p>{{$other_product->product_name}}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label>
                                        <div class="input-group">
                                            <p>{{$other_product->product_code}}</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="add-product__submit">
                                    
                                    <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('other_product_list') }}"><span class="button__text">Back</span></a>
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


@endsection




