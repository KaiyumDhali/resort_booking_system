@extends('layouts.admin')

@section('content')
    <main class="page-content">
        <div class="container container--flex">
            <div class="page-header">
                <h1 class="page-header__title">Other Product Serial</h1>
            </div>
            <div class="card add-product card--content-center">
                <div class="card__wrapper">
                    <div class="">
                        <form class="add-product__form" action="{{ route('other_product_serial_store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="">
                                <div class="row">

                                    
                                    {{-- <div class="col-12 col-md-4">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Product</label>
                                                <div class="input-group input-group--white input-group--append">
                                                    <select class="input input--select" id="product_id" name="product_id" required>
                                                        <option class="dropdown-menu__item active" value="null">--Select Product--
                                                        </option>
                                                    </select>
                                                </div>
                                        </div>
                                    </div> --}}


                                    <div class="col-12 col-md-4">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Select Product</label>
                                            <div class="input-group input-group--append">
                                                <select class="form-control" name="product_id" required>
                                                    <option value="">--Select Product--</option>
                                                    @foreach ($allProducts2 as $id => $productserial)
                                                        <option value="{{ $productserial['id'] }}">
                                                            Name:{{ $productserial['product_name'] }} Code:
                                                            {{ $productserial['product_code'] }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 col-md-4">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Date</label>
                                            <div class="input-group">
                                                <input class="form-control" type="date" name="productiondate"
                                                    placeholder="" value="" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Quantity</label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" name="quantity" placeholder=""
                                                    value="" required>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12">
                                    <div class="add-product__submit">
                                        <div class="modal__footer-button">
                                            <button class="button button--primary button--block" type="submit"><span
                                                    class="button__text">Save</span>
                                            </button>
                                        </div>
                                        <div class="modal__footer-button"><a class="button button--secondary button--block"
                                                href="{{ route('other_product_serial_list') }}"><span
                                                    class="button__text">Cancel</span></a>
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

    <!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let categoryId;
            let subcategoryId;
            let product_id;
            $('select[name="category_id"]').on('change', function() {
                categoryId = $(this).val();
                //    console.log(categoryId);  
                if (categoryId) {
                    $.ajax({
                        url: '{{ route('get-subcategories', ['category_id' => ':category_id']) }}'
                            .replace(':category_id', categoryId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('select[name="subcategory_id"]').empty();
                            $('select[name="subcategory_id"]').append(
                                '<option value="">-- Select Sub Category --</option>');
                            $.each(data, function(key, value) {
                                $('select[name="subcategory_id"]').append(
                                    '<option value="' + key + '">' + value +
                                    '</option>'
                                );
                            });
                        }
                    });
                } else {
                    $('select[name="subcategory_id"]').empty();
                    $('select[name="product_id"]').empty();
                }
            });

                                                    // @foreach ($allProducts2 as $id => $productserial)
                                                    //     <option value="{{ $productserial['id'] }}">
                                                    //         Name:{{ $productserial['product_name'] }} Code:
                                                    //         {{ $productserial['product_code'] }}</option>
                                                    // @endforeach

            $('select[name="subcategory_id"]').on('change', function() {
                subcategoryId = $(this).val();
                if (subcategoryId) {
                    $.ajax({
                        url: '{{ route('get-products', ['subcategory_id' => ':subcategory_id']) }}'
                            .replace(':subcategory_id', subcategoryId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            
                            $('select[name="product_id"]').empty();
                            $('select[name="product_id"]').append(
                                '<option value="">-- Select Product --</option>');
                            $.each(data, function(key, value) {
                                console.log(data);
                                // $('select[name="product_id"]').append(
                                //     '<option value="' + key + '"> Name: ' + value.product_name + ' Code: ' + value.product_code +'</option>');

                                $('select[name="product_id"]').append(
                                    '<option value="' + key + '">' + value +'</option>');
                            });
                        }
                    });
                } else {
                    $('select[name="product_id"]').empty();
                }
            });

            // $('select[name="product_id"]').on('change', function() {
            //     productId = $(this).val();
            // });

        });
    </script>
@endsection
