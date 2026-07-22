
@extends('layouts.login')

@section('content')

<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Product Registration</h1>
        </div>
<!--        <div class="page-tools">
            <div class="page-tools__breadcrumbs">
                <div class="breadcrumbs">
                    <div class="breadcrumbs__container">
                        <ol class="breadcrumbs__list">
                            <li class="breadcrumbs__item">
                                <a class="breadcrumbs__link" href="index.php">
                                    <svg class="icon-icon-home breadcrumbs__icon">
                                    <use xlink:href="#icon-home"></use>
                                    </svg>
                                    <svg class="icon-icon-keyboard-right breadcrumbs__arrow">
                                    <use xlink:href="#icon-keyboard-right"></use>
                                    </svg>
                                </a>
                            </li>
                            <li class="breadcrumbs__item disabled"><a class="breadcrumbs__link" href="#"><span>E-commerce</span>
                                    <svg class="icon-icon-keyboard-right breadcrumbs__arrow">
                                    <use xlink:href="#icon-keyboard-right"></use>
                                    </svg></a>
                            </li>
                            <li class="breadcrumbs__item"><a class="breadcrumbs__link" href="products.php"><span>Products</span>
                                    <svg class="icon-icon-keyboard-right breadcrumbs__arrow">
                                    <use xlink:href="#icon-keyboard-right"></use>
                                    </svg></a>
                            </li>
                            <li class="breadcrumbs__item active"><span class="breadcrumbs__link">Add Product</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="card__container">
                    <form class="add-product__form">
                        <div class="add-product__row">
                            
                            <div class="add-product__slider js-lightbox-gallery" id="addProductSlider">
                                <div class="add-product__thumbs">
                                    <div class="add-product__thumbs-slider swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($find_product->product->productimage as $product_image)
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
                                            
                                            @foreach($find_product->product->productimage as $product_image)
                                                @if ($product_image->image_path !== null)
                                            <a class="add-product__gallery-slide swiper-slide js-lightbox-link" href="{{ Storage::url($product_image->image_path) }}" data-pswp-srcset="{{ Storage::url($product_image->image_path) }} 2x" data-title="{{$find_product->product_name}}" data-cropped="true"
                                               target="_blank">
                                                    <img class="add-product__gallery-image" src="{{ Storage::url($product_image->image_path) }}" srcset="{{ Storage::url($product_image->image_path) }} 2x" alt="Product Image" />
                                            </a>
                                              @endif 
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            

                            
                            <div class="add-product__right">
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Serial ID</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->product_id }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Serial No</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->product_serial }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->product_name }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->product_code }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Category</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->category_name }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row--md">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Subcategory</label>
                                        <div class="input-group">
                                            <input class="input" type="text" placeholder="" value="{{ $find_product->sub_category_name }}" required>
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

@endsection

