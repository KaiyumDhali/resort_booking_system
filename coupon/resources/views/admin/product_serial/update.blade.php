@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Update Product</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    
                    @php
//dd($product);
@endphp
                    
                    <form class="add-product__form" action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="">
                            <div class="row">
                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_code" placeholder="" value="{{$product->product_code}}" required>
                                        </div>
                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_name" placeholder="" value="{{$product->product_name}}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Category</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="category_id" required>
                                                <option value="">--Select Category--</option>
                                                        @foreach($allCategories as $key => $value)
                                                <option value="{{ $key }}" {{ $product->category_id==$key ? 'selected' : ''}}>{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Sub Category</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="sub_category_id" required>
                                                <option value="">--Select Sub Category--</option>
                                                        @foreach($allSubCategories as $key => $value)
                                                <option value="{{ $key }}" {{ $product->sub_category_id==$key ? 'selected' : ''}}>{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Brand</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="brand_id" required>
                                                <option value="">--Select Brand--</option>
                                                        @foreach($allBrands as $key => $value)
                                                <option value="{{ $key }}" {{ $product->brand_id==$key ? 'selected' : ''}}>{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Color</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="color_id" required>
                                                <option value="">--Select Color--</option>
                                                        @foreach($allColors as $key => $value)
                                                <option value="{{ $key }}" {{ $product->color_id==$key ? 'selected' : ''}}>{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Size</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="size_id" required>
                                                <option value="">--Select Size--</option>
                                                        @foreach($allSizes as $key => $value)
                                                        @foreach($allSizes as $key => $value)
                                                <option value="{{ $key }}" {{ $product->size_id==$key ? 'selected' : ''}}>{{ $value}}</option>
                                                        @endforeach
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Unit</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="unit_id" required>
                                                <option value="">--Select Unit--</option>
                                                        @foreach($allUnits as $key => $value)
                                                <option value="{{ $key }}" {{ $product->unit_id==$key ? 'selected' : ''}}>
                                                        {{ $value}}
                                                </option>
                                                        @endforeach
                                            </select>


                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Purchase Price</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="purchase_price" placeholder="" value="{{$product->purchase_price}}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Sales Price</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="sales_price" placeholder="" value="{{$product->sales_price}}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Status</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="status" required>
                                                <option value="">--Select Status--</option>
                                                <option value="1" selected>Active</option>
                                                <option value="2">Disable</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label for="image">{{ __('Product Images') }}</label>
                                        <input type="file" class="form-control p-1" id="image" name="image[]" multiple>
                                              @if(!empty($productDetails[0]))
                                        <h5>Old Images:</h5>
                                              @foreach ($productDetails as $productDetail)
                                              @if ($productDetail->image_path != null)
                                        <img src="{{ Storage::url($productDetail->image_path) }}" height="100" width="100" alt="product image" />
                                        <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete ?')"
                                           href="{{route('products.image_destroy', Crypt::encrypt($productDetail->id))}}"><i class="icon-icon-trash"></i>
                                        </a>
                                            @endif

                                            @endforeach
                                            @endif
                                    </div>
                                </div>

                                <!--                                        <div class="col-6">
                                                                            <div class="col-12 form-group form-group--lg">
                                                                                <label class="form-label">Product Images</label>
                                                                                <div class="image-upload">
                                                                                    <div class="image-upload__drop">
                                                                                        <input class="image-upload__input" type="file" name="image[]" multiple="multiple" accept="image/png, image/jpeg, image/jpg" />
                                                                                        <div class="image-upload__drop-text">
                                                                                            <svg class="icon-icon-upload">
                                                                                            <use xlink:href="#icon-upload"></use>
                                                                                            </svg> <span>Drag and Drop or </span>  <span class="image-upload__drop-action text-blue">Browse</span>  <span>to upload</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <ul class="image-upload__list">
                                
                                                        @if(!empty($productDetails[0]))
                                                        @foreach ($productDetails as $productDetail)
                                                        @if ($productDetail->image_path != null)
                                
                                                                                        <li class="">
                                                                                            <div class="">
                                                                                                <img src="{{ Storage::url($productDetail->image_path) }}" height="" width="" alt="product image" />
                                                                                            </div>
                                                                                        </li>
                                                                                        <div class="">
                                                                                            <a class="" onclick="return confirm('Are you sure you want to delete ?')"
                                                                                               href="{{route('products.image_destroy', $productDetail->id)}}"><i class="icon-icon-trash"></i>
                                                                                            </a>
                                                                                        </div>
                                
                                                        @endif
                                                        @endforeach
                                                        @endif
                                
                                                                                        <li class="image-upload__item">
                                                                                            <div class="image-upload__progress">
                                                                                                <svg class="preloader-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <circle cx="18" cy="18" r="16" stroke="#333E4C" stroke-width="3" />
                                                                                                <path class="preloader-icon__border" d="M2 18C2 26.8366 9.16344 34 18 34C26.8366 34 34 26.8366 34 18C34 9.16344 26.8366 2 18 2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                                                                </svg>
                                                                                            </div>
                                
                                                                                            <div class="image-upload__action-remove">
                                                                                                <svg class="icon-icon-cross">
                                                                                                <use xlink:href="#icon-cross"></use>
                                                                                                </svg>
                                                                                            </div>
                                                                                        </li>
                                
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>-->


                            </div>
                            <div class="col-12">

                                <div class="row add-product__submit">
                                    <div class="modal__footer-button">
                                        <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                        </button>
                                    </div>
                                    <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('products.index') }}"><span class="button__text">Cancel</span></a>
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
