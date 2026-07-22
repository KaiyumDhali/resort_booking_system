@extends('layouts.admin')

@section('content')
    <main class="page-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-header__title">Products Feedback List</h1>
            </div>

            <div class="toolbox">
                <div class="toolbox__row row gutter-bottom-xs">
                    <div class="toolbox__left col-12 col-lg">
                        <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                            <input type="number" hidden name="status" id="status" value="0">
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

                            <table class="table table-striped table-hover display table--lines" id="feedback_list_table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 100px;">Category</th>
                                        <th style="min-width: 100px;">Subcategory</th>
                                        <th style="min-width: 100px;">Product</th>
                                        <th style="min-width: 200px;">Serial</th>
                                        <th style="min-width: 50px;">Feedback Date</th>
                                        <th style="min-width: 100px;">Email</th>
                                        <th style="min-width: 100px;">Mobile</th>
                                        <th style="min-width: 80px;">Feedback</th>
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

    <!--begin::Modal feedback - Create App-->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px ">
            <!--begin::Modal content-->
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Feedback Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body scroll-y px-5">
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Product:</strong> <span id="product_name"></span>&nbsp; (<strong>Serial:</strong> <span
                                id="product_serial"></span>)</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Customer Email:</strong> <span id="customer_email"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Mobile Number:</strong> <span id="customer_phone"></span>&nbsp;</p>
                    </div>
                   
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p> <strong>Feedback Date:</strong> <span id="feedback_date"></span>&nbsp;</p>
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <p><strong>Feedback:</strong></p>
                        <p class="" style="text-align: justify;" id="feedback"></p>
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
            try {
                $('#feedbackModal').find('#feedbackModalLabel').text(allData.product_serial);

                $('#feedbackModal').find('#product_name').text(allData.product_name);
                $('#feedbackModal').find('#product_serial').text(allData.product_serial);
                $('#feedbackModal').find('#customer_email').text(allData.customer_email);
                $('#feedbackModal').find('#customer_phone').text(allData.customer_phone);
                $('#feedbackModal').find('#feedback_date').text(allData.feedback_date);
                $('#feedbackModal').find('#feedback').text(allData.feedback);
                // Show modal
                $('#feedbackModal').modal('show');
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
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
            var table = $('#feedback_list_table').DataTable(dataTableOptions);

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
                let url;
                url =
                    '{{ route('product_feedback_list_search', ['category_id' => ':category_id', 'subcategory_id' => ':subcategory_id', 'product_id' => ':product_id', 'pdf' => ':pdf']) }}';

                url = url.replace(':category_id', category_id);
                url = url.replace(':subcategory_id', subcategory_id);
                url = url.replace(':product_id', product_id);
                url = url.replace(':pdf', pdfdata);

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
                                    // let feedback_view = `<a href="#" class="btn btn-primary" data-all='${alldata}' onclick='viewModel(this)'>Open Details</a>`;
                                    let feedback_view =
                                        `<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#feedbackModal"
                                                        data-all='${alldata}' onclick='viewModel(this)'>Feedback Details</a>`;
                                    table.row.add([d.category_name, d.sub_category_name, d.product_name, d.product_serial,
                                        d.feedback_date, d.customer_email, d.customer_phone, feedback_view,
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
