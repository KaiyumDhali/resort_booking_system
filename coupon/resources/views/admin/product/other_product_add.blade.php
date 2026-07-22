@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Other Product Add</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    <form class="add-product__form" action="{{ route('other_product_store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                        <div class="">
                            <div class="row">

                                

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Name</label><span style="color: red;">*</span>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_name" id="product_name" placeholder="" value="{{ old('product_name') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Product Code</label><span style="color: red;">*</span>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="product_code" id="product_code" placeholder="" value="" required>
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
                                        <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('other_product_list') }}"><span class="button__text">Cancel</span></a>
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
