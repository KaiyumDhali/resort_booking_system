@extends('layouts.admin')

@section('content')
    @if (isset($message))
        <div class="text-center alert alert-danger">
            {{ $message }}
        </div>
    @endif

    <main class="page-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-header__title">Coupon Serials</h1>
            </div>
            <div class="toolbox">
                <div class="toolbox__row row gutter-bottom-xs">

                    <div class="toolbox__right col-12 col-lg-auto">
                        <div class="toolbox__right-row row row--xs flex-nowrap">
                            <div class="col-auto">
                                <a class="button-add button-add--blue" title="Add Product New Serial"
                                    href="{{ route('couponserials.create') }}">
                                    <span class="button-add__icon">
                                        <svg class="icon-icon-plus">
                                            <use xlink:href="#icon-plus"></use>
                                        </svg>
                                    </span>
                                    <span class="button-add__text"></span>
                                </a>
                            </div>

                            <div class="col-auto">
                                <a class="button-icon" style="background-color: #0081ff;" data-bs-toggle="modal"
                                    title="Print Barcode & QR Code With Range" data-bs-target="#rangeprintQuantity"
                                    onclick='viewModelRange(this)'>
                                    <span class="button-icon__icon">
                                        <svg class="icon-icon-print" style="color: #ffffff">
                                            <use xlink:href="#icon-print"></use>
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
                                <th class="table__th-sort"><span class="align-middle">Coupon Serial</span><span
                                        class="sort sort--down"></span></th>
                                <th class="table__th-sort"><span class="align-middle">Created Date</span><span
                                        class="sort sort--down"></span></th>
                                <th class="table__th-sort"><span class="align-middle">Status</span><span
                                        class="sort sort--down"></span></th>
                                <th class="table__th-sort"><span class="align-middle">Action</span><span
                                        class="sort sort--down"></span></th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->id }}</td>
                                    <td>{{ $coupon->coupon_serial }}</td>
                                    <td>{{ $coupon->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if ($coupon->status == 0)
                                            <span class="badge bg-success">Not Availed</span>
                                        @else
                                            <span class="badge bg-danger">Availed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Example actions -->
                                        {{-- <a href="{{ route('admin.couponserials.edit', $coupon->id) }}"
                                            class="btn btn-sm btn-primary">Edit</a> --}}
                                        {{-- <form action="{{ route('admin.couponserials.destroy', $coupon->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form> --}}

                                        {{-- <a href="{{ route('couponserials.edit', $coupon->id) }}"
                                            class="btn btn-sm btn-primary">Edit</a> --}}
                                        <div class="d-flex">

                                            <a href="{{ route('couponserials.edit', $coupon->id) }}" title="Edit">
                                                <span class="dropdown-items__link-icon">
                                                    <svg class="icon-icon-task-notes">
                                                        <use xlink:href="#icon-task-notes"></use>
                                                    </svg>
                                                </span>
                                            </a>

                                            <a data-bs-toggle="modal" title="Print Barcode & QR Code"
                                                data-bs-target="#kt_modal_new_card" data-all='{{ $coupon }}'
                                                onclick='viewModel(this)'><span class="dropdown-items__link-icon"><svg
                                                        class="icon-icon-print">
                                                        <use xlink:href="#icon-print"></use>
                                                    </svg>
                                                </span>
                                            </a>

                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </main>


    <div class="modal fade" id="rangeprintQuantity" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2>QRCode Print with Range</h2>
                    <!--end::Modal title-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <form class="add-product__form" id="printQuantityForm" action="{{ route('rangecouponcodeprint') }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="modal__body">
                            <div class="modal__container">
                                <div class="row">

                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Range Start</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="rangestart" value=""
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Range End</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="rangeend" value=""
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Print Quantity</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="quantity" value=""
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal__footer">
                            <div class="modal__container">
                                <div class="modal__footer-buttons">
                                    <div class="modal__footer-button"></div>
                                    <div class="modal__footer-button d-flex">


                                        <!-- Add a hidden input field to store the selected value -->
                                        <input type="hidden" name="print_type2" id="printType2" value="1">

                                        <!-- Modify the buttons to set the value when clicked -->
                                        {{-- <button type="submit" class="button button--primary button--block"
                                            onclick="setPrintType2(1)">
                                            <span class="button__text">Barcode & QR</span>
                                        </button> --}}
                                        {{-- <button type="submit" class="button button--primary button--block mx-3"
                                            onclick="setPrintType2(2)">
                                            <span class="button__text">Barcode</span>
                                        </button> --}}
                                        <button type="submit" class="button button--primary button--block"
                                            onclick="setPrintType2(3)">
                                            <span class="button__text">QR</span>
                                        </button>

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

    <div class="modal fade" id="productPrintQuantity" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>QRCode Print Quantity</h2>
                </div>
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form class="add-product__form" id="productPrintQuantityForm" action="{{ route('couponcodeprint') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal__body">
                            <div class="modal__container">
                                <div class="row">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Product Serial</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="coupon_serial"
                                                value="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label form-label--sm">Print Quantity</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="quantity" value=""
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal__footer">
                            <div class="modal__container">
                                <div class="modal__footer-buttons">
                                    <div class="modal__footer-button"></div>
                                    <div class="modal__footer-button d-flex">
                                        <!-- Add a hidden input field to store the selected value -->
                                        <input type="hidden" name="print_type" id="printType" value="1">

                                        <!-- Modify the buttons to set the value when clicked -->
                                        {{-- <button type="submit" class="button button--primary button--block"
                                            onclick="setPrintType(1)">
                                            <span class="button__text">Barcode & QR</span>
                                        </button> --}}
                                        {{-- <button type="submit" class="button button--primary button--block mx-3"
                                            onclick="setPrintType(2)">
                                            <span class="button__text">Barcode</span>
                                        </button> --}}
                                        <button type="submit" class="button button--primary button--block"
                                            onclick="setPrintType(3)">
                                            <span class="button__text">QR</span>
                                        </button>
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




    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script type="text/javascript">
        function setPrintType2(value) {
            document.getElementById('printType2').value = value;
        }

        function setPrintType(value) {
            document.getElementById('printType').value = value;
        }
        //    ===========================================================
        function viewModel(element) {
            let allData = $(element).data('all');
            console.log('allData', allData)
            try {
                const formAction = $('#productPrintQuantity').find('#productPrintQuantityForm').attr('action');
                const productPrintSerialId = allData.coupon_serial;
                console.log('productPrintSerialId', productPrintSerialId)

                const updatedFormAction = formAction.replace(':productPrintSerialId', productPrintSerialId);
                $('#productPrintQuantity').find('#productPrintQuantityForm').attr('action', updatedFormAction);

                $('#productPrintQuantity').find('input[name="coupon_serial"]').val(productPrintSerialId);

                $('#productPrintQuantity').modal('show');
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        }

        function viewModelRange(element) {
            $('#rangeprintQuantity').modal('show');
        }

        $(document).ready(function() {
            let categoryId;
            let subcategoryId;
            let productId;
            let printStatus;
            $('select[name="category_id"]').on('change', function() {
                categoryId = $(this).val();
                CallBack(categoryId, 0, 0, 0);
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

            $('select[name="subcategory_id"]').on('change', function() {
                subcategoryId = $(this).val();
                CallBack(categoryId, subcategoryId, 0, 0);

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
                                // $('select[name="product_id"]').append(
                                //         '<option value="' + key + '"> Name: ' + value.product_name + ' Code: ' + value.product_code +'</option>');

                                $('select[name="product_id"]').append(
                                    '<option value="' + key + '">' + value +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('select[name="product_id"]').empty();
                }
            });

            $('select[name="product_id"]').on('change', function() {
                productId = $(this).val();
                CallBack(categoryId, subcategoryId, productId, 0);
            });
            $('select[name="print_status"]').on('change', function() {
                printStatus = $(this).val();
                CallBack(categoryId, subcategoryId, productId, printStatus);
            });

        });

        let dataTableOptions = {
            //      dom: '<"top"fB>rt<"bottom"lip>',
            paging: true,
            ordering: false,
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

        let table = $('#productSerialsTable').DataTable(dataTableOptions);

        function CallBack(category_id, subcategory_id, product_id, print_status) {
            let url =
                '{{ route('getProductSerials', ['category_id' => ':category_id', 'subcategory_id' => ':subcategory_id', 'product_id' => ':product_id', 'print_status' => ':print_status']) }}';
            url = url.replace(':category_id', category_id).replace(':subcategory_id', subcategory_id).replace(':product_id',
                product_id).replace(':print_status', print_status);
            $.ajax({
                url: url,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(data) {
                    table.clear().draw();

                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            console.log(data);
                            let d = data[key];
                            let alldata = JSON.stringify(d);
                            let downloadLink =
                                `<a data-bs-toggle="modal" title="Print Barcode & QR Code" data-bs-target="#kt_modal_new_card" data-all='${alldata}' 
                                onclick='viewModel(this)'><span class="dropdown-items__link-icon"><svg class="icon-icon-print"><use xlink:href="#icon-print"></use></svg></span></a>`;
                            let printStatus =
                                `<span>${d.status == 1 ? 'Not Printed' : 'Printed'}</span>`;
                            table.row.add([d.product_serial_id, d.category_name, d.sub_category_name, d
                                .product_name, d.product_code, d.product_serial, d.createdDate,
                                printStatus, downloadLink
                            ]).draw();
                        });
                        table.buttons().enable();
                    } else {
                        console.log('No records found');
                    }
                }
            });
        }
    </script>



    <script>
        document.querySelectorAll('.update-status').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                let id = this.dataset.id;

                fetch(`/admin/couponserials/status/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Status updated to: ' + data.status);
                            // Optionally update the UI here
                            document.querySelector('.status-' + id).textContent = data.status;
                        } else {
                            alert('Update failed.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong.');
                    });
            });
        });
    </script>
@endsection
