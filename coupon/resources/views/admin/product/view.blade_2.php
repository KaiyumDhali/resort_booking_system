@extends('layouts.admin')

@section('content')

<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Product view</h1>
        </div>

        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="card__container">
                    <form class="add-product__form">
                        <div class="add-product__row">
                            <div class="add-product__slider js-lightbox-gallery" id="addProductSlider">
                                <div class="add-product__thumbs">
                                    <div class="add-product__thumbs-slider swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($product->productimage as $product_image)
                                            
                                                
                                                
                                            <div class="add-product__thumb swiper-slide">
                                               @if ($product_image->image_path !== null)
                                                    <img class="add-product__thumb-image swiper-lazy" src="{{ Storage::url($product_image->image_path) }}" srcset="{{ Storage::url($product_image->image_path) }} 2x" alt="Product Image" />
                                              @endif                                                
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                            @endforeach

                                            
                                            
<!--                                            
                                            <div class="add-product__thumb swiper-slide">
                                                <img class="add-product__thumb-image swiper-lazy" src="img/content/product/thumb-2.jpg" srcset="img/content/product/thumb-2.jpg 2x" alt="#">
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="add-product__thumb swiper-slide">
                                                <img class="add-product__thumb-image swiper-lazy" src="img/content/product/thumb-3.jpg" srcset="img/content/product/thumb-3.jpg 2x" alt="#">
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="add-product__thumb swiper-slide">
                                                <img class="add-product__thumb-image swiper-lazy" src="img/content/product/thumb-4.jpg" srcset="img/content/product/thumb-4.jpg 2x" alt="#">
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="add-product__thumb swiper-slide is-empty">
                                                <img class="add-product__thumb-image swiper-lazy" src="img/content/product/placeholder-thumbnail.jpg" srcset="img/content/product/placeholder-thumbnail.jpg 2x" alt="#">
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="add-product__thumb swiper-slide is-empty">
                                                <img class="add-product__thumb-image swiper-lazy" src="img/content/product/placeholder-thumbnail.jpg" srcset="img/content/product/placeholder-thumbnail.jpg 2x" alt="#">
                                                <div class="add-product__lazy-preloader swiper-lazy-preloader">
                                                    <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                    <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>-->



                                        </div>
                                    </div>
                                    <div class="add-product__thumbs-prev">
                                        <a class="add-product__thumbs-arrow add-product__thumbs-arrow--prev" href="#">
                                            <svg class="icon-icon-chevron">
                                            <use xlink:href="#icon-chevron"></use>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="add-product__thumbs-next">
                                        <a class="add-product__thumbs-arrow add-product__thumbs-arrow--next" href="#">
                                            <svg class="icon-icon-chevron">
                                            <use xlink:href="#icon-chevron"></use>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="add-product__gallery">
                                    <div class="add-product__gallery-slider swiper-container">
                                        <div class="swiper-wrapper">
                                            
                                            @foreach($product->productimage as $product_image)
                                                @if ($product_image->image_path !== null)
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link" href="{{ Storage::url($product_image->image_path) }}" data-pswp-srcset="{{ Storage::url($product_image->image_path) }} 2x" data-title="{{$product->product_name}}" data-cropped="true"
                                               target="_blank">
                                                    <img class="add-product__gallery-image" src="{{ Storage::url($product_image->image_path) }}" srcset="{{ Storage::url($product_image->image_path) }} 2x" alt="Product Image" />
                                            </a>
                                              @endif 
                                            @endforeach

                                            
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link" href="img/content/product/item-2.jpg" data-pswp-srcset="img/content/product/item-2.jpg 2x" data-title="Apple Watch Series 4" data-cropped="true"
                                               target="_blank">
                                                <img class="add-product__gallery-image" src="img/content/product/item-2.jpg" srcset="img/content/product/item-2.jpg 2x" alt="#">
                                            </a><!--
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link" href="img/content/product/item-3.jpg" data-pswp-srcset="img/content/product/item-3.jpg 2x" data-title="Apple Watch Series 4" data-cropped="true"
                                               target="_blank">
                                                <img class="add-product__gallery-image" src="img/content/product/item-3.jpg" srcset="img/content/product/item-3.jpg 2x" alt="#">
                                            </a>
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link" href="img/content/product/item-4.jpg" data-pswp-srcset="img/content/product/item-4.jpg 2x" data-title="Apple Watch Series 4" data-cropped="true"
                                               target="_blank">
                                                <img class="add-product__gallery-image" src="img/content/product/item-4.jpg" srcset="img/content/product/item-4.jpg 2x" alt="#">
                                            </a>
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link is-empty" href="img/content/product/placeholder-main.jpg" data-pswp-srcset="img/content/product/placeholder-main.jpg 2x" data-title="Apple Watch Series 4"
                                               data-cropped="true" target="_blank">
                                                <img class="add-product__gallery-image" src="img/content/product/placeholder-main.jpg" srcset="img/content/product/placeholder-main.jpg 2x" alt="#">
                                            </a>
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link is-empty" href="img/content/product/placeholder-main.jpg" data-pswp-srcset="img/content/product/placeholder-main.jpg 2x" data-title="Apple Watch Series 4"
                                               data-cropped="true" target="_blank">
                                                <img class="add-product__gallery-image" src="img/content/product/placeholder-main.jpg" srcset="img/content/product/placeholder-main.jpg 2x" alt="#">
                                            </a>-->
                                            
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            <div class="add-product__right">
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label>
                                        <div class="input-group">
                                            <p>{{$product->product_name}}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label>
                                        <div class="input-group">
                                            <p>{{$product->product_code}}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Barcode</label>
                                        <div class="input-group">
                                            <p>{!! DNS1D::getBarcodeSVG("$product->product_code", 'C128',2,50,'black',true) !!}</p>
                                        </div>
                                    </div>
<!--                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Description</label>
                                        <div class="input-editor">
                                            <div class="js-description-editor">Fundamentally redesigned and reengineered. The largest Apple Watch display yet. Built-in electrical heart sensor. New Digital Crown with haptic feedback. Low and high heart rate notifications. Fall detection
                                                and Emergency SOS.</div>
                                        </div>
                                    </div>-->

                                    
                                        <p>Category: {{$product->category->category_name }}</p>
                                        <p>Sub Category: {{$product->subCategory->sub_category_name }}</p>
                                        <p>Brand: {{$product->brand->brand_name }}</p>
                                        <p>Color: {{$product->color->color_name }}</p>
                                        <p>Size: {{$product->size->size_name }}</p>
                                        <p>Unit: {{$product->unit->unit_name }}</p>
                                        <p>Purchase Price: {{$product->sales_price}}<span class="input-group__symbol">$</span></p>
                                        <p>Sales Price: {{$product->purchase_price}}<span class="input-group__symbol">$</span></p>
                                        
                                    
                                </div>
                                <div class="add-product__submit">
                                    
                                    <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('products.index') }}"><span class="button__text">Back</span></a>
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




