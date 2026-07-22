@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Add Product</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    <form class="add-product__form" action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                        <div class="">
                            <div class="row">

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Category</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="category_id" id="category_id" required>
                                                <option value="">--Select Category--</option>
                                                        @foreach($allCategories as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Sub Category</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="sub_category_id" id="sub_category_id" required>
                                                <option value="">--Select Sub Category--</option>
                                                        @foreach($allSubCategories as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Brand</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">
                                            {{--<select class="form-control" name="brand_id" id="brand_id" required>
                                                <option value="">--Select Brand--</option>
                                                        @foreach($allBrands as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>--}}

                                            <select class="form-control" name="brand_id" id="brand_id" required>
                                                <option value="">--Select Brand--</option>
                                                @foreach($allBrands as $id => $productbrand)
                                                <option value="{{ $productbrand['id'] }}">{{ $productbrand['brand_name'] }} - {{ $productbrand['brand_code'] }}</option>
                                                @endforeach
                                            </select>




                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Color</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="color_id" id="color_id">
                                                <option value="">--Select Color--</option>
                                                        @foreach($allColors as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Model Name/Number</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">

                                            {{--<select class="form-control" name="productmodel_id" required>
                                                <option value="">--Select Product Model Name/Number--</option>
                                                        @foreach($allProductmodel as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>--}}

                                            <select class="form-control" name="productmodel_id" id="productmodel_id" required>
                                                <option value="">--Select Product Model Name/Number--</option>
                                                @foreach($allProductmodel as $id => $productmodel)
                                                <option value="{{ $productmodel['id'] }}">{{ $productmodel['model_code'] }} - {{ $productmodel['model_name'] }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Size/Watt</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="size_id" id="size_id" required>
                                                <option value="">--Select Size--</option>
                                                        @foreach($allSizes as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Unit</label><span style="color: red;">*</span>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="unit_id" id="unit_id" required>
                                                <option value="">--Select Unit--</option>
                                                        @foreach($allUnits as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label><span style="color: red;">*</span>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_code" id="product_code" placeholder="" value="" readonly="readonly" required>
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

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label><span style="color: red;">*</span>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_name" id="product_name" placeholder="" value="{{ old('product_name') }}" readonly="readonly" required>
                                        </div>
                                    </div>
                                </div>

                                <!--                                <div class="col-12 col-md-6">
                                                                    <div class="col-12 form-group form-group--lg">
                                                                        <label class="form-label">Purchase Price</label>
                                                                        <div class="input-group">
                                                                            <input class="form-control" type="text" name="purchase_price" placeholder="" value="{{-- old('purchase_price') --}}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>-->

                                <!--                                <div class="col-12 col-md-6">
                                                                    <div class="col-12 form-group form-group--lg">
                                                                        <label class="form-label">Sales Price</label>
                                                                        <div class="input-group">
                                                                            <input class="form-control" type="text" name="sales_price" placeholder="" value="{{-- old('sales_price') --}}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>-->

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Description</label>
                                        <div class="input-group">
                                            <textarea class="form-control" name="product_description" value="{{ old('product_description') }}" rows="8" cols=""></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Images</label>
                                        <div class="image-upload">
                                            <div class="image-upload__drop">
                                                <input class="image-upload__input" type="file" name="image[]" multiple="multiple" accept="image/png, image/jpeg" />
                                                <div class="image-upload__drop-text">
                                                    <svg class="icon-icon-upload">
                                                    <use xlink:href="#icon-upload"></use>
                                                    </svg> <span>Drag and Drop or </span>  <span class="image-upload__drop-action text-blue">Browse</span>  <span>to upload</span>
                                                </div>
                                            </div>
                                            <ul class="image-upload__list">
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
                                </div>

                                <div class="col-12 col-md-6">
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
                                <div class="col-12 col-md-6">
                                    <div class="add-product__submit px-3">
                                        <div class="modal__footer-button">
                                            <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                            </button>
                                        </div>
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
    // Get the select elements
    const categorySelect = document.getElementById('category_id');
    const subCategorySelect = document.getElementById('sub_category_id');
    const brandSelect = document.getElementById('brand_id');
    const productModelSelect = document.getElementById('productmodel_id');
    const productSizeSelect = document.getElementById('size_id');
    const productCodeInput = document.getElementById('product_code');
    const productNameInput = document.getElementById('product_name');

    // Listen for changes on the selects
//    categorySelect.addEventListener('change', updateProductCode);
//    subCategorySelect.addEventListener('change', updateProductCode);
//    brandSelect.addEventListener('change', updateProductCode);
    productModelSelect.addEventListener('change', updateProductCode);
    productSizeSelect.addEventListener('change', updateProductCode);

    // Function to update the product code
    function updateProductCode() {
        const categoryId = categorySelect.value;
        const subCategoryText = subCategorySelect.options[subCategorySelect.selectedIndex].textContent;
        const brandSelectText = brandSelect.options[brandSelect.selectedIndex].textContent;
        const productModelText = productModelSelect.options[productModelSelect.selectedIndex].textContent;
        const productSizeText = productSizeSelect.options[productSizeSelect.selectedIndex].textContent;

        const subCategoryFirstLetters = subCategoryText.split(' ').map(word => word.charAt(0)).join('');
//        const brandFirstLetters = brandSelectText.split(' ').map(word => word.charAt(0)).join('');
    
        const brandName = brandSelectText.split(' ')[0];
        
        const brandCode = brandSelectText.split(' ').pop();

        const productModelFirstWord = productModelText.split(' ')[0];
        const productModelName = productModelText.substring(7);

//        const productCode = categoryId + '-' + subCategoryFirstLetters.toUpperCase() + '-' + brandFirstLetters.toUpperCase() + '-' + productModelFirstWord;
        const productCode = categoryId + '-' + subCategoryFirstLetters.toUpperCase() + '-' + brandCode.toUpperCase() + '-' + productModelFirstWord;
        
//        const productName = brandFirstLetters.toUpperCase() + '-' + subCategoryFirstLetters.toUpperCase() + '-' + productModelFirstWord + '-' + productSizeText;
        const productName = brandName.toUpperCase() + ' ' + subCategoryText.toUpperCase() + ' ' + productModelName.toUpperCase() + ' ' + productSizeText;

        // Update the product code input field value
        productCodeInput.value = productCode;
        productNameInput.value = productName;
    }
</script>






@endsection
