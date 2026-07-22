@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container">
        <div class="page-header">
            <h1 class="page-header__title">Other Products</h1>
        </div>

        <div class="toolbox">
            <div class="toolbox__row row gutter-bottom-xs">
                <div class="toolbox__left col-12 col-lg">
                    <div class="toolbox__left-row row row--xs gutter-bottom-xs">



                    </div>
                </div>
                <div class="toolbox__right col-12 col-lg-auto">
                    <div class="toolbox__right-row row row--xs flex-nowrap">
                        <div class="col col-lg-auto">

                        </div>
                        <div class="col-auto">
                            <a class="button-add button-add--blue" href="{{ route('other_product_add') }}"><span class="button-add__icon">
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
            <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible" data-simplebar>
                <table class="table table--lines" id="productTable">
                    <thead class="table__header">
                        <tr class="table__header-row">
                            <th class="table__th-sort"><span class="align-middle">ID</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Name</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Code</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Status</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Action</span><span class="sort sort--down"></span></th>
                        </tr>
                    </thead>
                    <tbody id='tbody'>
                        @foreach($products as $product)
                        <tr class="table__row">
                            <td class="table__td">{{$product->id}}</td>
                            <td class="table__td">{{$product->product_name ?? 'No Product'}}</td>
                            <td class="table__td">{{$product->product_code}}</td>
                            <td class="table__td"><span>{{ $product->status==1?'Active':'Disabled' }}</span></td>
                            <td class="table__td">
                                <span>
                                    <a href="{{ route('other_product_edit', Crypt::encrypt($product->id)) }}" title="Edit">
                                        <span class="dropdown-items__link-icon">
                                            <svg class="icon-icon-task-notes">
                                            <use xlink:href="#icon-task-notes"></use>
                                            </svg>
                                        </span>
                                    </a>
                                </span>
                                <span>
                                    <a href="{{ route('other_product_view', Crypt::encrypt($product->id)) }}" title="View">
                                        <span class="dropdown-items__link-icon">
                                            <svg class="icon-icon-view">
                                            <use xlink:href="#icon-view"></use>
                                            </svg>
                                        </span>
                                    </a>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>


    <div class="modal fade" id="printQuantity" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2>Product Barcode QRCode Print Quantity</h2>
                    <!--end::Modal title-->

                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <form class="add-product__form" id="printQuantityForm" action="{{ route('barcodeprint', ':productId') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                        <div class="modal__body">
                            <div class="modal__container">

                                <div class="row">
                                    <div class="col-12 form-group form-group--lg">
                                        <p id="productid"></p>
                                        <p id="productname">
                                        </p>
                                        <p id="productbarcode">
                                        </p>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Print Quantity</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="quantity" value="" required>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal__footer">
                            <div class="modal__container">
                                <div class="modal__footer-buttons">
                                    <div class="modal__footer-button"></div>
                                    <div class="modal__footer-button">

                                        <button type="submit" class="button button--primary button--block" target="_blank">
                                            <span class="button__text">Send to Print</span>
                                        </button><!-- comment -->

                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
    </div>

</main>

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    let dataTableOptions = {
//      dom: '<"top"fB>rt<"bottom"lip>',
        paging: true,
        ordering: true,
        searching: true,
        responsive: true,
        lengthMenu: [5, 10, 25, 50, 100],
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

$('#productTable').DataTable(dataTableOptions);

    
    function openEditModalPromotion(button) {
//        const productData = JSON.parse(button.getAttribute('data-productPrint'));
        const formAction = $('#printQuantity').find('#printQuantityForm').attr('action');
        const productId = productData.id;
//        console.log(productId);
        const printFormAction = formAction.replace(':productId', productId);
        $('#printQuantity').find('#printQuantityForm').attr('action', printFormAction);
        $('#printQuantity').find('#productid').text(productData.id);
        $('#printQuantity').find('#productname').text(productData.product_name);
        $('#printQuantity').find('#productbarcode').text(productData.product_code);
//        $('#printQuantity').find('input[name="quantity"]').val(productData.id);

        $('#printQuantity').modal('show');
        
    }


    
    const search = document.getElementById('search');
    const tableBody = document.getElementById('tbody');
    function getContent(){
            
    const searchValue = search.value;
            
        const xhr = new XMLHttpRequest();
        xhr.open('GET','{{route('search')}}/?search=' + searchValue ,true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            console.log(xhr);
            if(xhr.readyState == 4 && xhr.status == 200)
            {
                tableBody.innerHTML = xhr.responseText;
            }
        }
        xhr.send()
    }
    search.addEventListener('input',getContent);
    
    
    

</script>



@endsection