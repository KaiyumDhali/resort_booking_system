@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container">
        <div class="page-header">
            <h1 class="page-header__title">Product Serials</h1>
        </div>

        <div class="toolbox">
            <div class="toolbox__row row gutter-bottom-xs">
                <!--                <div class="toolbox__left col-12 col-lg">
                                    <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                                        <div class="form-group form-group--inline col-12 col-sm-auto">
                                            <label class="form-label">Show</label>
                                            <div class="input-group input-group--white input-group--append">
                                                <input class="input input--select" type="text" value="10" size="1"
                                                    data-toggle="dropdown" readonly><span class="input-group__arrow">
                                                    <svg class="icon-icon-keyboard-down">
                                                        <use xlink:href="#icon-keyboard-down"></use>
                                                    </svg></span>
                                                <div class="dropdown-menu dropdown-menu--right dropdown-menu--fluid js-dropdown-select">
                                                    <a class="dropdown-menu__item active" href="#" tabindex="0" data-value="10">10</a>
                                                    <a class="dropdown-menu__item" href="#" tabindex="0" data-value="15">15</a>
                                                    <a class="dropdown-menu__item" href="#" tabindex="0" data-value="20">20</a>
                                                    <a class="dropdown-menu__item" href="#" tabindex="0" data-value="25">25</a>
                                                    <a class="dropdown-menu__item" href="#" tabindex="0" data-value="50">50</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>-->
                <div class="toolbox__left col-12 col-lg">
                    <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                        <div class="form-group form-group--inline col-12 col-sm-auto">
                            <label class="form-label">Category</label>
                            <div class="input-group input-group--white input-group--append">
                                <select class="input input--select" name="category_id" required>
                                    <option class="dropdown-menu__item active" value="null">--Select Category--</option>
                                    @foreach($allCategories as $key => $value)
                                    <option class="dropdown-menu__item" value="{{ $key }}">{{ $value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toolbox__left col-12 col-lg">
                    <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                        <div class="form-group form-group--inline col-12 col-sm-auto">
                            <label class="form-label">Sub Category</label>
                            <div class="input-group input-group--white input-group--append">
                                <select class="input input--select" name="subcategory_id" required>
                                    <option class="dropdown-menu__item active" value="null">--Select Sub Category--
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toolbox__left col-12 col-lg">
                    <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                        <div class="form-group form-group--inline col-12 col-sm-auto">
                            <label class="form-label">Product</label>
                            <div class="input-group input-group--white input-group--append">
                                <select class="input input--select" name="product_id" required>
                                    <option class="dropdown-menu__item active" value="null">--Select Product--</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toolbox__right col-12 col-lg-auto">
                    <div class="toolbox__right-row row row--xs flex-nowrap">
                        <!--                        <div class="col col-lg-auto">
                                                    <form class="toolbox__search" method="GET">
                                                        <div class="input-group input-group--white input-group--prepend">
                                                            <div class="input-group__prepend">
                                                                <svg class="icon-icon-search">
                                                                    <use xlink:href="#icon-search"></use>
                                                                </svg>
                                                            </div>
                                                            <input class="input" id="productserialsearch" name="productserialsearch" type="text"
                                                                placeholder="Search Product Serial">
                                                        </div>
                                                    </form>
                                                </div>-->
                        <div class="col-auto">
                            <a class="button-add button-add--blue" href="{{ route('productserials.create') }}"><span
                                    class="button-add__icon">
                                    <svg class="icon-icon-plus">
                                    <use xlink:href="#icon-plus"></use>
                                    </svg>
                                </span>
                                <span class="button-add__text"></span>
                            </a>
                        </div>
                    </div>
                </div>



            </div>
        </div>
        <div class="table-wrapper">
            <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible"
                 data-simplebar>


                <table class="table table--lines" id="productSerialsTable">
                    <thead class="table__header">
                        <tr class="table__header-row">
                            <th class="table__th-sort"><span class="align-middle">ID</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Category Name</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Sub Category Name</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Name</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Code</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Serial</span><span
                                    class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Status</span><span
                                    class="sort sort--down"></span></th>
                        </tr>
                    </thead>
                    <tbody id='tbody'>

                    </tbody>
                </table>
            </div>
            <!--            <div class="table-wrapper__footer">
                            <div class="row">
                                <div class="table-wrapper__show-result col text-grey"><span
                                        class="d-none d-sm-inline-block">Showing</span> 1 to 10 <span
                                        class="d-none d-sm-inline-block">of 50 items</span>
                                </div>
                                <div class="table-wrapper__pagination col-auto">
                                    <ol class="pagination">
                                        <li class="pagination__item">
                                            <a class="pagination__arrow pagination__arrow--prev" href="#">
                                                <svg class="icon-icon-keyboard-left">
                                                    <use xlink:href="#icon-keyboard-left"></use>
                                                </svg>
                                            </a>
                                        </li>
                                        <li class="pagination__item active"><a class="pagination__link" href="#">1</a>
                                        </li>
                                        <li class="pagination__item"><a class="pagination__link" href="#">2</a>
                                        </li>
                                        <li class="pagination__item"><a class="pagination__link" href="#">3</a>
                                        </li>
                                        <li class="pagination__item pagination__item--dots">...</li>
                                        <li class="pagination__item"><a class="pagination__link" href="#">10</a>
                                        </li>
                                        <li class="pagination__item">
                                            <a class="pagination__arrow pagination__arrow--next" href="#">
                                                <svg class="icon-icon-keyboard-right">
                                                    <use xlink:href="#icon-keyboard-right"></use>
                                                </svg>
                                            </a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>-->
        </div>
    </div>



</main>



<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
$(document).ready(function() {
   
    $('select[name="category_id"]').on('change', function() {
    var categoryId = $(this).val();
//    console.log(categoryId);  
        if (categoryId) {
            $.ajax({
                url: '{{ route("get-subcategories", ["category_id" => ":category_id"]) }}'
                    .replace(':category_id', categoryId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('select[name="subcategory_id"]').empty();
                    $('select[name="subcategory_id"]').append(
                        '<option value="">-- Select Sub Category --</option>');
                    $.each(data, function(key, value) {
                        $('select[name="subcategory_id"]').append(
                            '<option value="' + key + '">' + value + '</option>'
                            );
                    });
                }
            });
        } else {
            $('select[name="subcategory_id"]').empty();
            $('select[name="product_id"]').empty();
        }
    });

    $('select[name="subcategory_id"]').on('change', function() {
        var subcategoryId = $(this).val();
        if (subcategoryId) {
            $.ajax({
                url: '{{ route("get-products", ["subcategory_id" => ":subcategory_id"]) }}'
                    .replace(':subcategory_id', subcategoryId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('select[name="product_id"]').empty();
                    $('select[name="product_id"]').append(
                        '<option value="">-- Select Product --</option>');
                    $.each(data, function(key, value) {
                        $('select[name="product_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('select[name="product_id"]').empty();
        }
    });


});
    
    
    var dataTableOptions = {
//         dom: '<"top"fB>rt<"bottom"lip>',
        paging: true,
        ordering: true,
        searching: true,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        pageLength: 25,
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    };

    var table = $('#productSerialsTable').DataTable(dataTableOptions);
    
    function CallBack() {
    $.ajax({
        url: 'getProductSerials',
        type: "GET",
        data: {
            "_token": "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function(data) {
        loaddata=data;
//            console.log(data);
            table.clear().draw();
            if (data.length > 0) {
                $.each(data, function(key, value) {
                    let d = data[key];
                    console.log(d);
                    table.row.add([d.id, d.product.category.category_name, d.product.sub_category.sub_category_name, d.product.product_name, d.product.product_code, d.product_serial, d.status==1? 'Active':'Disable', 
                    ]).draw();
                });
                table.buttons().enable();
            } else {
                console.log('No records found');
            }
        }
    });
}
CallBack();
</script>


@endsection