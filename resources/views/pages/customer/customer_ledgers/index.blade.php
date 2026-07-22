<x-default-layout>
    <style>
        .dataTables_filter {
            float: right;
        }
        .dataTables_buttons {
            float: left;
        }
        .bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
    </style>

    <div class="col-xl-12 px-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-{{ session('alert-type') }}">
                {{ session('message') }}
            </div>
        @endif
    </div>

    {{-- Customer Select Card --}}
    <div class="col-xl-12 py-5">
        <div class="card card-flush h-lg-100" id="kt_contacts_main">
            <div class="card-header py-0" id="kt_chat_contacts_header">
                <div class="card-title">
                    <span class="svg-icon svg-icon-1 me-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="currentColor"></path>
                            <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <h3>Customer Ledger</h3>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row row-cols-1 row-cols-sm-4 rol-cols-md-1 row-cols-lg-4">
                    <div class="col">
                        <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label class="fs-6 fw-semibold form-label mt-0">
                                <span class="required">Customer Name</span>
                            </label>
                            <select id="changeCustomer" class="form-select form-select-sm" name="customer_id" data-control="select2">
                                <option selected disabled>Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->ac_id }}" data-name="{{ $customer->customer_name }}">
                                        {{ $customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-0">
                            <label class="fs-6 fw-semibold form-label mt-0">
                                <span>Mobile</span>
                            </label>
                            <input type="text" class="form-control form-control-sm form-control-solid"
                                id="customer_mobile" value="" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-7 fv-plugins-icon-container">
                            <label class="fs-6 fw-semibold form-label mt-0">
                                <span>Due Amount</span>
                            </label>
                            <input type="text" class="form-control form-control-sm form-control-solid"
                                id="is_previous_due_s" value="" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-7 fv-plugins-icon-container">
                            <label class="fs-6 fw-semibold form-label mt-0">
                                <span>Customer Address</span>
                            </label>
                            <input type="text" class="form-control form-control-sm form-control-solid"
                                id="customer_address" value="" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Received Voucher Form --}}
    @can('create customer ledger')
    <div class="col-xl-12 pb-5 d-none" id="received_voucher_section">
        <div class="card card-flush">
            <div class="card-header py-0">
                <div class="card-title">
                    <h3>Received Voucher Entry</h3>
                </div>
            </div>
            <div class="card-body py-3">
                <form id="receivedVoucherForm"
                    class="form fv-plugins-bootstrap5 fv-plugins-framework"
                    method="POST" action="{{ route('received_voucher_store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="voucher_received_from_id" name="received_from" value="">
                    <input type="hidden" id="voucher_customer_name" name="customer_name" value="">

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4">
                        {{-- Date --}}
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-0 required">Date</label>
                                <input type="date" name="date" class="form-control form-control-sm"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        {{-- Payment Type --}}
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-0 required">Payment Type</label>
                                <select data-control="select2" id="voucherPaymentType" class="form-select form-select-sm"
                                    name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                    <option value="mBank">Mobile Bank</option>
                                </select>
                            </div>
                        </div>

                        {{-- To Account Name --}}
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-0 required">To Account Name</label>
                                <select data-control="select2" id="voucherAccountsName" class="form-select form-select-sm"
                                    name="accounts_name" required>
                                    <option value="" selected>Select Account Name</option>
                                </select>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-0 required">Amount</label>
                                <input type="number" name="amount" class="form-control form-control-sm"
                                    placeholder="Amount" required>
                            </div>
                        </div>

                        {{-- Bank Details --}}
                        <div id="voucherBankDetails" class="col-12 row p-0 m-0 d-none">
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Select Type</label>
                                    <select class="form-select form-select-sm" name="cheque_type" data-control="select2">
                                        <option value="">Select Type</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Cash Deposit">Cash Deposit</option>
                                        <option value="Online">Online</option>
                                        <option value="CHT">CHT</option>
                                        <option value="RTGS">RTGS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Cheque No</label>
                                    <input type="text" class="form-control form-control-sm" name="cheque_no" value="0">
                                </div>
                            </div>
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Cheque Date</label>
                                    <input type="date" class="form-control form-control-sm" name="cheque_date">
                                </div>
                            </div>
                        </div>

                        {{-- Mobile Bank Details --}}
                        <div id="voucherMobileBankDetails" class="col-12 row p-0 m-0 d-none">
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Select Mobile Bank</label>
                                    <select class="form-select form-select-sm" name="mobile_bank_name" data-control="select2">
                                        <option value="">Select Mobile Bank</option>
                                        <option value="bKash">bKash</option>
                                        <option value="Nagad">Nagad</option>
                                        <option value="Rocket">Rocket</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Mobile Number</label>
                                    <input type="text" class="form-control form-control-sm" name="mobile_number" value="0">
                                </div>
                            </div>
                            <div class="col">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mt-0">Transaction ID</label>
                                    <input type="text" class="form-control form-control-sm" name="transaction_id" value="0">
                                </div>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-0">Remarks</label>
                                <input type="text" class="form-control form-control-sm" name="remarks" placeholder="Narration">
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="col">
                            <div class="fv-row mb-7 mt-8">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span class="indicator-label">Save Voucher</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Ledger Table --}}
    <div class="card card-flush d-none" id="ledger_list_div_s">
        <div class="card-header py-0">
            <div class="card-title">
                <i class="bi bi-card-list fs-1 pe-2"></i>
                <h3><span id="customer_ledger"></span> Ledger</h3>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <div class="card-body p-2">
                            <input id="start_date" type="date" name="start_date"
                                class="form-control form-control-sm ps-5 p-2"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div id="end_date_div">
                        <div class="card-body p-2">
                            <input id="end_date" type="date" name="end_date"
                                class="form-control form-control-sm ps-5 p-2"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="d-flex align-items-center position-relative my-1">
                        <div class="card-body p-2">
                            <button type="button" id="searchBtn"
                                class="btn btn-sm btn-primary px-5">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
                <a href="" id="downloadPdf" name="downloadPdf" type="button"
                    class="btn btn-sm btn-light-primary" target="_blank">
                    PDF Download
                </a>
            </div>
        </div>
        <div id="tableDiv" class="card-body pt-0">
            <table class="table table-striped table-bordered align-middle table-row-dashed fs-7 gy-5 mb-0"
                id="customer_ledger_table">
                <thead>
                    <tr class="text-start fs-7 text-uppercase gs-0">
                        <th class="min-w-50px">SL</th>
                        <th class="min-w-100px">Voucher No</th>
                        <th class="min-w-100px">Date</th>
                        <th class="min-w-100px">Remarks</th>
                        <th class="min-w-100px">Credit</th>
                        <th class="min-w-100px">Debit</th>
                        <th class="min-w-100px">Balance (BDT)</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-700"></tbody>
                <tfoot>
                    <tr class="fw-bold fs-6" style="background-color: #f5f5f5;">
                        <td colspan="4" class="text-end pe-3">Total :</td>
                        <td id="footer_credit">0.00</td>
                        <td id="footer_debit">0.00</td>
                        <td id="footer_balance">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</x-default-layout>

<script type="text/javascript">
    var startDate  = $('#start_date').val();
    var endDate    = $('#end_date').val();
    var reportTittle = 'Perfume PLC\n Transaction Date : ' + startDate + ' - ' + endDate;

    function balanceLabel(balance) {
        if (balance > 0)      return balance.toFixed(2) + ' Dr';
        else if (balance < 0) return Math.abs(balance).toFixed(2) + ' Cr';
        else                  return '0.00';
    }

    function resetFooter() {
        $('#footer_credit').text('0.00');
        $('#footer_debit').text('0.00');
        $('#footer_balance').text('0.00');
    }

    function updateFooter(totalCredit, totalDebit, balance) {
        $('#footer_credit').text(totalCredit.toFixed(2));
        $('#footer_debit').text(totalDebit.toFixed(2));
        $('#footer_balance').text(balanceLabel(balance));
    }

    // ---- Customer Select ----
    $('#changeCustomer').on('change', function () {
        var customerID   = $(this).val();
        var customerName = $(this).find('option:selected').data('name');

        $('#customer_ledger').text(customerName);
        $('#is_previous_due_s').val(0);
        $('#voucher_received_from_id').val(customerID);
        $('#voucher_customer_name').val(customerName);

        if (!customerID) return;

        $.ajax({
            url: 'customerDetails/' + customerID,
            type: 'GET',
            data: { '_token': '{{ csrf_token() }}', 'customer_name': customerName },
            dataType: 'json',
            success: function (response) {
                var data         = response.customerDetails;
                var customerData = response.customer;

                if (customerData) {
                    $('#customer_mobile').val(customerData.customer_mobile);
                    $('#customer_address').val(customerData.customer_address);
                } else {
                    $('#customer_mobile').val('');
                    $('#customer_address').val('');
                }

                $('#received_voucher_section').removeClass('d-none');
                $('#ledger_list_div_s').removeClass('d-none');

                table.clear().draw();
                resetFooter();

                if (data && data.length > 0) {
                    var balance     = 0;
                    var totalCredit = 0;
                    var totalDebit  = 0;

                    $.each(data, function (key, value) {
                        var d = data[key];
                        totalCredit += parseFloat(d.credit) || 0;
                        totalDebit  += parseFloat(d.debit)  || 0;
                        balance     += (parseFloat(d.debit) || 0) - (parseFloat(d.credit) || 0);
                        table.row.add([
                            key + 1,
                            d.voucher_no,
                            d.ledger_date,
                            d.remarks,
                            parseFloat(d.credit).toFixed(2),
                            parseFloat(d.debit).toFixed(2),
                            balanceLabel(balance)
                        ]).draw();
                    });

                    updateFooter(totalCredit , totalDebit, balance);
                    $('#is_previous_due_s').val(balanceLabel(balance));
                    table.buttons().enable();
                }
            }
        });
    });

    // ---- Payment Type change ----
    $('#voucherPaymentType').on('change', function () {
        var paymentType = $(this).val();
        var url;

        if (paymentType === 'Bank') {
            $('#voucherBankDetails').removeClass('d-none');
            $('#voucherMobileBankDetails').addClass('d-none');
            url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020004);
        } else if (paymentType === 'mBank') {
            $('#voucherBankDetails').addClass('d-none');
            $('#voucherMobileBankDetails').removeClass('d-none');
            url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020003);
        } else {
            $('#voucherBankDetails').addClass('d-none');
            $('#voucherMobileBankDetails').addClass('d-none');
            url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020002);
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: { '_token': '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (response) {
                var acListData = response.acList;
                $('#voucherAccountsName').empty().append('<option value="" selected>Select Account Name</option>');
                if (acListData && Object.keys(acListData).length > 0) {
                    $.each(acListData, function (id, accountName) {
                        $('#voucherAccountsName').append('<option value="' + id + '">' + accountName + '</option>');
                    });
                }
            }
        });
    });

    // Prevent Enter key submit
    document.getElementById('receivedVoucherForm') &&
    document.getElementById('receivedVoucherForm').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // ---- DataTable ----
    var dataTableOptions = {
        dom: '<"top"fB>rt<"bottom"lip>',
        buttons: [{
            extend: 'collection',
            text: 'Export',
            className: 'btn btn-sm btn-light-primary',
            buttons: [
                { extend: 'excel', text: 'Excel', title: reportTittle, className: 'btn btn-sm btn-light-primary' },
                { extend: 'copy',  text: 'Copy',  title: reportTittle, className: 'btn btn-sm btn-light-primary' },
                { extend: 'csv',   text: 'CSV',   title: reportTittle, className: 'btn btn-sm btn-light-primary' },
                { extend: 'pdf',   text: 'PDF',   title: reportTittle, className: 'btn btn-sm btn-light-primary' },
                { extend: 'print', text: 'Print', title: reportTittle, className: 'btn btn-sm btn-light-primary' }
            ]
        }],
        paging: true,
        ordering: false,
        searching: true,
        responsive: false,
        lengthMenu: [10, 25, 50, 100],
        pageLength: 25,
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
        }
    };
    var table = $('#customer_ledger_table').DataTable(dataTableOptions);

    // ---- Search / PDF ----
    function CallBack(pdfdata) {
        var customerID = $('#changeCustomer').val();
        var startDate  = $('#start_date').val();
        var endDate    = $('#end_date').val();

        var url = '{{ route('customer_ledger_search', ['startDate' => ':startDate', 'endDate' => ':endDate', 'customerID' => ':customerID', 'pdf' => ':pdf']) }}';
        url = url.replace(':startDate', startDate)
                 .replace(':endDate', endDate)
                 .replace(':customerID', customerID)
                 .replace(':pdf', pdfdata);

        if (pdfdata === 'list') {
            $.ajax({
                url: url,
                type: 'GET',
                data: { '_token': '{{ csrf_token() }}' },
                dataType: 'json',
                success: function (data) {
                    table.clear().draw();
                    resetFooter();

                    if (data && data.length > 0) {
                        var balance     = 0;
                        var totalCredit = 0;
                        var totalDebit  = 0;

                        $.each(data, function (key, value) {
                            var d = data[key];
                            totalCredit += parseFloat(d.credit) || 0;
                            totalDebit  += parseFloat(d.debit)  || 0;
                            balance     += (parseFloat(d.debit) || 0) - (parseFloat(d.credit) || 0);
                            table.row.add([
                                key + 1,
                                d.voucher_no,
                                d.ledger_date,
                                d.remarks,
                                parseFloat(d.credit).toFixed(2),
                                parseFloat(d.debit).toFixed(2),
                                balanceLabel(balance)
                            ]).draw();
                        });

                        updateFooter(totalCredit, totalDebit, balance);
                        $('#is_previous_due_s').val(balanceLabel(balance));
                        table.buttons().enable();
                    }
                }
            });
        } else if (pdfdata === 'pdfurl') {
            $('#downloadPdf').attr('href', url);
        }
    }

    $('#searchBtn').on('click', function () { CallBack('list'); });
    $('#downloadPdf').on('click', function () { CallBack('pdfurl'); });
</script>