<x-default-layout>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container">

                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
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

                <form method="POST" action="{{ route('payment.store') }}">
                    @csrf

                    <div class="row g-5">

                        {{-- LEFT SIDE – SUMMARY + PAYMENT FORM --}}
                        <div class="col-lg-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Payment Summary</h4>
                                </div>

                                <div class="card-body fs-6">

                                    <div class="mb-3">
                                        <strong>Customer Name:</strong><br>
                                        <span class="text-dark fw-semibold">
                                            {{ $firstBooking->customer->customer_name ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <strong>{{ number_format($bookingTotalSum, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <strong class="text-danger">- {{ number_format($discount, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>After Discount:</span>
                                        <strong>{{ number_format($bookingTotalSum - $discount, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Received:</span>
                                        <strong class="text-success">{{ number_format($totalReceivedSum, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                                        <span class="fw-bold">Due Amount:</span>
                                        <strong class="text-danger fs-5">
                                            {{ number_format($bookingTotalSum - $totalReceivedSum - $discount, 2) }}
                                        </strong>
                                    </div>

                                    <hr class="my-4">

                                    {{-- Payment Type --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold required">Payment Type</label>
                                        <select  data-control="select2" name="payment_type" id="paymentType"
                                            class="form-select form-select-sm" required
                                           >
                                            <option value="">Select Payment Type</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                            <option value="mBank">Mobile Bank</option>
                                        </select>
                                    </div>

                                    {{-- To Account Name --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold required">To Account</label>
                                        <select  data-control="select2" name="accounts_name" id="accountsName"
                                            class="form-select form-select-sm" required
                                            >
                                            <option value="">Select Account</option>
                                        </select>
                                    </div>

                                    {{-- Bank Details --}}
                                    <div id="bankDetails" class="d-none">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Cheque Type</label>
                                            <select  data-control="select2" name="cheque_type" class="form-select form-select-sm"
                                                >
                                                <option value="">Select Type</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Cash Deposit">Cash Deposit</option>
                                                <option value="Online">Online</option>
                                                <option value="CHT">CHT</option>
                                                <option value="RTGS">RTGS</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Cheque No</label>
                                            <input type="text" name="cheque_no"
                                                class="form-control form-control-sm" value="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Cheque Date</label>
                                            <input type="date" name="cheque_date"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>

                                    {{-- Mobile Bank Details --}}
                                    <div id="mobileBankDetails" class="d-none">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Mobile Bank</label>
                                            <select name="mobile_bank_name" class="form-select form-select-sm"
                                               >
                                                <option value="">Select Mobile Bank</option>
                                                <option value="bKash">bKash</option>
                                                <option value="Nagad">Nagad</option>
                                                <option value="Rocket">Rocket</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Mobile Number</label>
                                            <input type="text" name="mobile_number"
                                                class="form-control form-control-sm" value="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Transaction ID</label>
                                            <input type="text" name="transaction_id"
                                                class="form-control form-control-sm" value="0">
                                        </div>
                                    </div>

                                    {{-- Amount --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold required">Receive Amount</label>
                                        <input type="number" name="amount"
                                            class="form-control form-control-sm"
                                            placeholder="Enter amount" required>
                                        <input type="hidden" name="booking_no" value="{{ $bookingNo }}">
                                    </div>

                                    {{-- Remarks --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Remarks</label>
                                        <input type="text" name="remarks"
                                            class="form-control form-control-sm"
                                            placeholder="Optional remarks">
                                    </div>

                                    <div class="d-flex justify-content-end mt-4">
                                        <a href="{{ route('booking.index') }}"
                                            class="btn btn-light me-3">Cancel</a>
                                        <button type="submit" class="btn btn-success">
                                            Save Payment
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- RIGHT SIDE – PAYMENT HISTORY --}}
                        <div class="col-lg-8">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Payment History</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover align-middle text-center">
                                        <thead class="table-light">
                                            <tr class="fw-bold text-uppercase">
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalAmount = 0; @endphp
                                            @foreach ($totalReceived as $payment)
                                                @php $totalAmount += $payment->amount; @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                                                    <td class="text-success fw-semibold">
                                                        {{ number_format($payment->amount, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2" class="text-end">Total Received:</th>
                                                <th class="text-success fs-6">
                                                    {{ number_format($totalAmount, 2) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function () {
        $('#paymentType').on('change', function () {
            var paymentType = $(this).val();
            var url;

            if (paymentType === 'Bank') {
                $('#bankDetails').removeClass('d-none');
                $('#mobileBankDetails').addClass('d-none');
                url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020004);
            } else if (paymentType === 'mBank') {
                $('#bankDetails').addClass('d-none');
                $('#mobileBankDetails').removeClass('d-none');
                url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020003);
            } else {
                $('#bankDetails').addClass('d-none');
                $('#mobileBankDetails').addClass('d-none');
                url = "{{ route('account_list', ['code' => ':code']) }}".replace(':code', 100020002);
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: { '_token': '{{ csrf_token() }}' },
                dataType: 'json',
                success: function (response) {
                    var acListData = response.acList;
                    $('#accountsName').empty().append('<option value="">Select Account</option>');
                    if (acListData && Object.keys(acListData).length > 0) {
                        $.each(acListData, function (id, accountName) {
                            $('#accountsName').append('<option value="' + id + '">' + accountName + '</option>');
                        });
                    }
                }
            });
        });
    }, 500);
</script>
</x-default-layout>