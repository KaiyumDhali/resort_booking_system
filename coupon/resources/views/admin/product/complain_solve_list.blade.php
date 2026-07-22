@extends('layouts.admin')

@section('content')
    <main class="page-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-header__title">Complain Solve List</h1>
            </div>

            <div class="toolbox">
                <div class="toolbox__row row gutter-bottom-xs">
                    <div class="toolbox__left col-12 col-lg">
                        <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                            <input type="number" hidden name="status" id="status" value="1">
                            <div class="form-group form-group--inline col col-sm-auto">
                                <div class="input-group input-group--white input-group--append">
                                    <select class="input js-input-select" id="category_id" name="category_id">
                                        <option value="0" selected="selected">Categories</option>
                                        @foreach ($categories as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group__arrow">
                                        <svg class="icon-icon-keyboard-down">
                                            <use xlink:href="#icon-keyboard-down"></use>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group form-group--inline col col-sm-auto">
                                <div class="input-group input-group--white input-group--append">
                                    <select class="input js-input-select" id="subcategory_id" name="subcategory_id">
                                        <option value="0" selected="selected">Sub Categories</option>
                                    </select>
                                    <span class="input-group__arrow">
                                        <svg class="icon-icon-keyboard-down">
                                            <use xlink:href="#icon-keyboard-down"></use>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group form-group--inline col col-sm-auto">
                                <div class="input-group input-group--white input-group--append">
                                    <select class="input js-input-select" id="product_id" name="product_id">
                                        <option value="0" selected="selected">Products</option>
                                    </select>
                                    <span class="input-group__arrow">
                                        <svg class="icon-icon-keyboard-down">
                                            <use xlink:href="#icon-keyboard-down"></use>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="toolbox__right col-12 col-lg-auto">
                        <div class="toolbox__right-row row row--xs flex-nowrap">
                            <div class="col-auto">
                                <a class="btn btn-primary" id="downloadPdf" target="_blank">Download PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-wrapper">
                        <div class=""
                            data-simplebar>

                            <table class="table table-striped table-hover display table--lines" id="complain_list_table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 100px;">Category</th>
                                        <th style="min-width: 100px;">Subcategory</th>
                                        <th style="min-width: 100px;">Product</th>
                                        <th style="min-width: 150px;">Serial</th>
                                        <th style="min-width: 50px;">Complain Date</th>
                                        <th style="min-width: 50px;">Memo</th>
                                        <th style="min-width: 100px;">Name</th>
                                        <th style="min-width: 80px;">Mobile</th>
                                        <th style="min-width: 120px;">Shop</th>
                                        <th style="min-width: 120px;">View Details</th>
                                        <th style="min-width: 110px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!--begin::Modal Complain - Create App-->
    <div class="modal fade" id="complainModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px ">
            <!--begin::Modal content-->
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="complainModalLabel">Complain Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body scroll-y">
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Product:</strong> <span id="product_name"></span>&nbsp; (<strong>Serial:</strong> <span
                                id="product_serial"></span>)</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p> <strong>Registrations Date:</strong> <span id="registrations_date"></span>&nbsp; <strong>Complain Date:</strong> <span id="complain_date"></span>&nbsp; </p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p> <strong>Duration:</strong> <span id="duretion"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Customer Name:</strong> <span id="name"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Mobile Number:</strong> <span id="mobile"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Customer Address:</strong> <span id="customer_address"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Shop Address:</strong> <span id="shop_address"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p> <strong>Memo No:</strong> <span id="memo_no"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Complain:</strong></p>
                        <p class="" style="text-align: justify;" id="complain"></p>
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal- Create App-->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        // Define the viewModel function
        function viewModel(element) {
            let allData = $(element).data('all');
            // let registrations_date = allData.registrations_date;
            let registrations_date = allData.registrations_date.split(' ')[0];
            
            // let registrations_date = '2023-11-06';
            let complain_date = allData.complain_date;
            let duretion = humanizeDate(registrations_date, complain_date);
            try {
                // Update modal content with data
                $('#complainModal').find('#complainModalLabel').text(allData.product_serial);
                $('#complainModal').find('#registrations_date').text(registrations_date);
                $('#complainModal').find('#complain_date').text(allData.complain_date);
                $('#complainModal').find('#duretion').text(duretion);
                $('#complainModal').find('#product_name').text(allData.product_name);
                $('#complainModal').find('#product_serial').text(allData.product_serial);
                $('#complainModal').find('#shop_address').text(allData.shop_address);
                $('#complainModal').find('#memo_no').text(allData.memo_no);
                $('#complainModal').find('#complain').text(allData.complain);
                $('#complainModal').find('#name').text(allData.name);
                $('#complainModal').find('#mobile').text(allData.mobile);
                $('#complainModal').find('#customer_address').text(allData.customer_address);

                // Show modal
                $('#complainModal').modal('show');
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        }
        function humanizeDate(dateString, complain_date) {
            const date = new Date(dateString);
            const now = new Date(complain_date);
            const diff = now - date;
            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            
            if (seconds < 60) {
                return "just now";
            } else if (minutes === 1) {
                return "a minute ago";
            } else if (minutes < 60) {
                return `${minutes} minutes ago`;
            } else if (hours === 1) {
                return "an hour ago";
            } else if (hours < 24) {
                return `${hours} hours ago`;
            } else if (days === 1) {
                return "yesterday";
            } else {
                return `${days} days ago`;
            }
        }
        function complaintSolve(element) {
            let allData = $(element).data('all');
            let id = allData.id;
            let url;
            url = '{{ route('complaint_solve', ['id' => ':id']) }}';
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'PUT', // or 'GET', depending on your route configuration
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}', // Include the CSRF token
                    status: 0 // Assuming '0' represents solved complain back status
                },
                success: function(response) {
                    console.log('Complaint solved successfully');
                    var table = $('#complain_list_table').DataTable();
                    var rowToRemove = $(element).closest('tr'); // Find the closest row to the element
                    table.row(rowToRemove).remove().draw(); // Remove the row from the DataTable
                    // You can replace this alert with your preferred success message display mechanism
                    alert('Complaint Back successfully');
                },
                error: function(error) {
                    console.error('Error solving complaint:', error);
                }
            });
        }

        // Data Table with category_id, subcategory_id, product_id Shorting
        $(document).ready(function() {
            let category_id;
            let subcategory_id;
            let product_id;

            $('select[name="category_id"]').on('change', function() {
                category_id = $(this).val();
                CallBack(category_id);
                if (category_id) {
                    $.ajax({
                        url: '{{ route('get-subcategories', ['category_id' => ':category_id']) }}'
                            .replace(':category_id', category_id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('select[name="subcategory_id"]').empty();
                            $('select[name="subcategory_id"]').append(
                                '<option value="0" >Sub Category</option>');
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
                subcategory_id = $(this).val();
                CallBack(category_id, subcategory_id);

                if (subcategory_id) {
                    $.ajax({
                        url: '{{ route('get-products', ['subcategory_id' => ':subcategory_id']) }}'
                            .replace(':subcategory_id', subcategory_id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('select[name="product_id"]').empty();
                            $('select[name="product_id"]').append(
                                '<option value="0" >All Product</option>');
                            $.each(data, function(key, value) {
                                $('select[name="product_id"]').append(
                                    '<option value="' +
                                    key + '">' + value + '</option>');
                            });
                        }
                    });
                } else {
                    $('select[name="product_id"]').empty();
                }
            });

            $('select[name="product_id"]').on('change', function() {
                product_id = $(this).val();
                CallBack(category_id, subcategory_id, product_id);
            });

            $('#downloadPdf').on('click', function() {
                CallBack(category_id, subcategory_id, product_id, "pdfurl");
            });

            var dataTableOptions = {
                dom: '<"top"lfB>rt<"bottom"ip>',
                buttons: [
                    // 'pageLength',
                    // 'colvis'
                ],
                paging: true,
                ordering: false,
                searching: true,
                responsive: false,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 25,
                language: {
                    lengthMenu: 'Show _MENU_',
                    search: 'Search:',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
               
            };
            var table = $('#complain_list_table').DataTable(dataTableOptions);

            function CallBack(category_id, subcategory_id, product_id, pdfdata) {
                if (category_id == null) {
                    category_id = $('#category_id').val()
                } else {
                    category_id = category_id;
                }
                if (subcategory_id == null) {
                    subcategory_id = $('#subcategory_id').val()
                } else {
                    subcategory_id = subcategory_id;
                }
                if (product_id == null) {
                    product_id = $('#product_id').val()
                } else {
                    product_id = product_id;
                }
                if (pdfdata == null) {
                    pdfdata = "list"
                } else {
                    pdfdata = pdfdata;
                }
                status = $('#status').val()
                console.log('status', status);

                let url;
                url =
                    '{{ route('product_complain_list_search', ['category_id' => ':category_id', 'subcategory_id' => ':subcategory_id', 'product_id' => ':product_id','status' => ':status', 'pdf' => ':pdf']) }}';

                url = url.replace(':category_id', category_id);
                url = url.replace(':subcategory_id', subcategory_id);
                url = url.replace(':product_id', product_id);
                url = url.replace(':pdf', pdfdata);
                url = url.replace(':status', status);

                console.log(url);
                if (pdfdata == 'list') {
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function(data) {
                            console.log('data:', data);
                            table.clear().draw();
                            if (data.length > 0) {
                                $('#jsdataerror').text('');
                                $.each(data, function(key, value) {
                                    let d = data[key];
                                    let alldata = JSON.stringify(d);
                                    // let complain_view = `<a href="#" class="btn btn-primary" data-all='${alldata}' onclick='viewModel(this)'>Open Details</a>`;
                                    let complain_view =
                                        `<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#complainModal"
                                                        data-all='${alldata}' onclick='viewModel(this)'>Complain Details</a>`;
                                    // Solve button
                                    let complain_solve = `<a href="#" class="btn btn-success" data-all='${alldata}' onclick='complaintSolve(this)'>Complain Back</a>`;

                                    table.row.add([d.category_name, d.sub_category_name, d
                                        .product_name, d.product_serial, d
                                        .complain_date, d.memo_no, d.name, d.mobile, d
                                        .shop_address, complain_view, complain_solve, 
                                    ]).draw();
                                });
                                table.buttons().enable();
                            } else {
                                $('#jsdataerror').text('No records found');
                                console.log('No records found');
                            }
                        }
                    });
                } else if (pdfdata == 'pdfurl') {
                    $('#downloadPdf').attr('href', url);
                } else {
                    $('#jsdataerror').text('Please select a date');
                }
            }
            CallBack();
            // $('#searchBtn').on('click', function() {
            //     CallBack("list");
            // });
        });
    </script>
@endsection
