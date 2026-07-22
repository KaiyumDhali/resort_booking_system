<x-default-layout>

<style>
    .report-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        padding: 24px;
    }

    .page-title {
        font-size: 17px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 4px;
    }

    .page-sub {
        font-size: 13px;
        color: #888;
        margin-bottom: 20px;
    }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #f8f9fb;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .filter-bar .row {
        row-gap: 10px;
    }

    .filter-bar label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        margin-bottom: 4px;
        display: block;
    }

    .filter-bar .form-control,
    .filter-bar .form-select {
        font-size: 13px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        height: 36px;
        padding: 4px 10px;
    }

    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,.12);
    }

    .btn-filter {
        font-size: 13px;
        height: 36px;
        padding: 0 16px;
        border-radius: 6px;
    }

    /* ── Active filter tags ── */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e7f1ff;
        color: #0d6efd;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .filter-tag a {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        line-height: 1;
    }

    /* ── Summary Strip ── */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    @media (max-width: 1199px) {
        .summary-strip { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 767px) {
        .summary-strip { grid-template-columns: repeat(2, 1fr); }
    }

    .stat-card {
        background: #f8f9fb;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .stat-card .stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        margin-bottom: 4px;
    }

    .stat-card .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
    }

    .stat-card.stat-paid .stat-value { color: #198754; }
    .stat-card.stat-due  .stat-value { color: #dc3545; }
    .stat-card.stat-disc .stat-value { color: #fd7e14; }

    /* ── Table ── */
    .report-table th {
        background: #f1f3f5;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #555;
        white-space: nowrap;
        vertical-align: middle;
    }

    .report-table td {
        font-size: 13px;
        vertical-align: middle;
    }

    .report-table tbody tr:hover {
        background: #f8f9fb;
    }

    .booking-no {
        font-weight: 600;
        color: #0d6efd;
        font-size: 13px;
    }

    .text-green { color: #198754; font-weight: 600; }
    .text-red   { color: #dc3545; font-weight: 600; }
    .text-orange{ color: #fd7e14; font-weight: 600; }

    /* ── Status select ── */
    .status-select {
        font-size: 12px;
        border-radius: 20px;
        font-weight: 600;
        text-align: center;
        text-align-last: center;
        border: 1px solid #dee2e6;
        padding: 4px 10px;
        height: auto;
    }

    .status-pending  { background-color: #fff3cd; color: #664d03; }
    .status-approved { background-color: #d1e7dd; color: #0a3622; }
    .status-cancelled{ background-color: #f8d7da; color: #58151c; }

    /* ── Action buttons ── */
    .action-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        justify-content: center;
    }

    .action-btns .btn {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
    }

    .pagination-info {
        font-size: 13px;
        color: #888;
    }

    @media (max-width: 768px) {
        table td, table th {
            white-space: nowrap;
        }
    }
</style>

{{-- Alerts --}}
<div class="container-fluid mt-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-{{ session('alert-type', 'success') }}">
            {{ session('message') }}
        </div>
    @endif
</div>

<div class="container-fluid">
<div class="report-card">

    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <div class="page-title mb-0">Spot Bookings</div>
            <div class="page-sub mb-0">List of all spot package bookings</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('spot-bookings.report.pdf') . '?' . http_build_query(request()->all()) }}"
               target="_blank" class="btn btn-light-primary btn-filter">
                <i class="ti ti-file-type-pdf me-1"></i> Download PDF
            </a>
            <a href="{{ route('spot-bookings.create') }}" class="btn btn-primary btn-filter">
                <i class="ti ti-plus me-1"></i> New Booking
            </a>
        </div>
    </div>

    {{-- ── FILTER BAR ── --}}
    {{-- Date From/To এখন booking creation date (created_at) অনুযায়ী filter করে,
         booking_date অনুযায়ী না। কিছুই filter না করলে page load-এ ডিফল্ট আজকের তারিখ দেখায়। --}}
    <form method="GET" action="{{ route('spot-bookings.index') }}">
    <div class="filter-bar">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-sm-6 col-md-2">
                <label>Invoice</label>
                <input type="text" name="invoice" value="{{ request('invoice') }}" class="form-control" placeholder="INV0000…">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Mobile</label>
                <input type="text" name="customer_mobile" value="{{ request('customer_mobile') }}" class="form-control" placeholder="01XXXXXXXXX">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>NID</label>
                <input type="text" name="nid" value="{{ request('nid') }}" class="form-control" placeholder="NID number…">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Created Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Created Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control">
            </div>

            <div class="col-12 col-sm-6 col-md-1">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Pending</option>
                    <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Approved</option>
                    <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-filter w-100">
                    Filter
                </button>
             <a href="{{ route('spot-bookings.index') }}" class="btn btn-outline-secondary btn-filter px-3">
    &#x21BA;
</a>
            </div>

        </div>
    </div>
    </form>

    {{-- ── ACTIVE FILTER TAGS ── --}}
    @php
        $activeFilters = array_filter([
            'invoice'         => request('invoice'),
            'customer_mobile' => request('customer_mobile'),
            'nid'             => request('nid'),
            'date_from'       => request('date_from'),
            'date_to'         => request('date_to'),
            'status'          => match(request('status')) { '0'=>'Pending','1'=>'Approved','2'=>'Cancelled', default=>null },
        ]);
    @endphp

    @if(count($activeFilters))
    <div class="active-filters">
        @foreach($activeFilters as $key => $val)
            @php
                $removeParams = request()->except($key);
                $removeUrl    = route('spot-bookings.index') . '?' . http_build_query($removeParams);
            @endphp
            <span class="filter-tag">
                {{ ucfirst(str_replace('_', ' ', $key)) }}: <b>{{ $val }}</b>
                <a href="{{ $removeUrl }}">×</a>
            </span>
        @endforeach
    </div>
    @endif

    {{-- ── TABLE ── --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped report-table mb-0">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="text-center">Persons</th>
                    <th class="text-end">Total (৳)</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Net Total (৳)</th>
                    <th class="text-end">Paid (৳)</th>
                    <th class="text-end">Refundable</th>
                    <th class="text-center">Days Left</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $statusClass = match((int) $booking->status) {
                        1 => 'status-approved',
                        2 => 'status-cancelled',
                        default => 'status-pending',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td><span class="booking-no">{{ $booking->invoice_number }}</span></td>

                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>

                    <td>
                        <div style="font-weight:500;font-size:13px">{{ $booking->customer_name ?? '—' }}</div>
                        @if($booking->customer_mobile)
                            <div style="font-size:11px;color:#888">{{ $booking->customer_mobile }}</div>
                        @endif
                    </td>

                    <td class="text-center">{{ $booking->total_persons }}</td>

                    <td class="text-end">{{ number_format($booking->invoice_amount + $booking->manual_discount_amount, 2) }}</td>

                    <td class="text-end text-orange">
                        {{ $booking->manual_discount_amount > 0 ? '-'.number_format($booking->manual_discount_amount, 2) : '—' }}
                    </td>

                    <td class="text-end text-green fw-bold">{{ number_format($booking->invoice_amount, 2) }}</td>

                    <td class="text-end text-green">{{ number_format($booking->paid_amount, 2) }}</td>

                    <td class="text-end text-orange">{{ number_format($booking->refundable_amount, 2) }}</td>

                    <td class="text-center">{{ $booking->daysBeforeBooking ?? 0 }}</td>

                    {{-- Status dropdown --}}
                    <td class="text-center">
                        <select class="form-select status-select status-dropdown {{ $statusClass }}"
                            data-invoice="{{ $booking->invoice_number }}">
                            <option value="0" {{ $booking->status == 0 ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ $booking->status == 1 ? 'selected' : '' }}>Approved</option>
                            <option value="2" {{ $booking->status == 2 ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('spot-bookings.edit1', $booking->invoice_number) }}" class="btn btn-light-warning">
                                <i class="ti ti-edit"></i> Edit
                            </a>
                            <a href="{{ route('spot-bookings.show.invoice', $booking->invoice_number) }}" class="btn btn-light-primary">
                                <i class="ti ti-eye"></i> View
                            </a>
                            <a href="{{ route('spot-bookings.invoice.pdf', $booking->invoice_number) }}" class="btn btn-light-primary">
                                <i class="ti ti-file-type-pdf"></i> PDF
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center py-4 text-muted">
                        <i class="ti ti-search" style="font-size:22px"></i><br>
                        No bookings found for the selected filters.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── PAGINATION ── --}}
    @if(method_exists($bookings, 'hasPages') && $bookings->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="pagination-info">
            Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
        </div>
        <div>
            {{ $bookings->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif

</div>
</div>

{{-- jQuery + status update --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    function updateDropdownColor(dropdown) {
        let val = dropdown.val();
        dropdown.removeClass('status-pending status-approved status-cancelled');
        if(val == 0) dropdown.addClass('status-pending');
        else if(val == 1) dropdown.addClass('status-approved');
        else if(val == 2) dropdown.addClass('status-cancelled');
    }

    $('.status-dropdown').each(function() {
        updateDropdownColor($(this));
    });

    $('.status-dropdown').change(function() {
        let dropdown = $(this);
        let invoice = dropdown.data('invoice');
        let status = dropdown.val();
        let token = '{{ csrf_token() }}';

        updateDropdownColor(dropdown);

        $.ajax({
            url: '{{ route("spot-bookings.updateStatus") }}',
            type: 'POST',
            data: {
                _token: token,
                invoice_number: invoice,
                status: status
            },
            success: function(response) {
                Swal.fire('Success', response.message, 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });
});
</script>

</x-default-layout>