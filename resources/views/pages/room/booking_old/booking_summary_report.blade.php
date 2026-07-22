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

    .stat-card.stat-paid .stat-value  { color: #198754; }
    .stat-card.stat-due  .stat-value  { color: #dc3545; }
    .stat-card.stat-disc .stat-value  { color: #fd7e14; }

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

    .room-tag {
        display: inline-block;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 5px;
        font-weight: 600;
    }

    .text-green { color: #198754; font-weight: 600; }
    .text-red   { color: #dc3545; font-weight: 600; }
    .text-orange{ color: #fd7e14; font-weight: 600; }

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

    .pagination-info {
        font-size: 13px;
        color: #888;
    }
</style>

<div class="container-fluid mt-4">
<div class="report-card">

    {{-- Page Title --}}
    <div class="page-title">Booking Summary Report</div>
    <div class="page-sub">Overview of bookings with payment and due summary</div>

    {{-- ── FILTER BAR ── --}}
    <form method="GET" action="{{ route('booking.summary.report') }}" id="filterForm">
    <div class="filter-bar">
        <div class="row g-2 align-items-end">

            {{-- Invoice --}}
            <div class="col-md-2">
                <label>Invoice</label>
                <input type="text" name="invoice" class="form-control"
                       placeholder="Booking no…"
                       value="{{ request('invoice') }}">
            </div>

            {{-- Customer (Select2) --}}
            <div class="col-md-3">
                <label>Customer</label>
                <select name="customer_id" id="customerSelect" class="form-select" style="width:100%">
                    <option value="">All Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}"
                            {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->customer_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Mobile --}}
            <div class="col-md-2">
                <label>Mobile</label>
                <input type="text" name="customer_mobile" class="form-control"
                       placeholder="01XXXXXXXXX"
                       value="{{ request('customer_mobile') }}">
            </div>

            {{-- NID --}}
            <div class="col-md-2">
                <label>NID</label>
                <input type="text" name="nid_number" class="form-control"
                       placeholder="NID number…"
                       value="{{ request('nid_number') }}">
            </div>

            {{-- Room --}}
            <div class="col-md-3">
                <label>Room</label>
                <select name="room_id" class="form-select" data-control="select2">
                    <option value="">All Room</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}"
                            {{ request('room_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->room_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date --}}
            <div class="col-md-2">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ request('start_date') }}">
            </div>

            {{-- End Date --}}
            <div class="col-md-2">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ request('end_date') }}">
            </div>

            {{-- Buttons --}}
            <div class="col-md-8 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary btn-filter">
                    <i class="ti ti-filter me-1"></i> Filter
                </button>
                <a href="{{ route('booking.summary.report') }}" class="btn btn-outline-secondary btn-filter">
                    <i class="ti ti-refresh me-1"></i> Reset
                </a>
                <a href="{{ route('booking.summary.report.pdf') . '?' . http_build_query(request()->all()) }}"
                   target="_blank"
                   class="btn btn-outline-danger btn-filter">
                    <i class="ti ti-file-type-pdf me-1"></i> PDF
                </a>
            </div>

        </div>
    </div>
    </form>

    {{-- ── ACTIVE FILTER TAGS ── --}}
    @php
        $activeFilters = array_filter([
            'invoice'    => request('invoice'),
            'customer'   => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
            'mobile'     => request('customer_mobile'),
            'nid'        => request('nid_number'),
            'room'       => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))?->room_name ?? null) : null,
            'start_date' => request('start_date'),
            'end_date'   => request('end_date'),
        ]);
    @endphp

    @if(count($activeFilters))
    <div class="active-filters">
        @foreach($activeFilters as $key => $val)
            @php
                $removeParams = request()->except($key);
                $removeUrl    = route('booking.summary.report') . '?' . http_build_query($removeParams);
            @endphp
            <span class="filter-tag">
                {{ ucfirst(str_replace('_', ' ', $key)) }}: <b>{{ $val }}</b>
                <a href="{{ $removeUrl }}">×</a>
            </span>
        @endforeach
    </div>
    @endif

    {{-- ── SUMMARY STRIP ── --}}
    @php
        $totalBookings = $report->count();
        $totalAmount   = $report->sum('total_amount');
        $totalDiscount = $report->sum('discount');
        $totalPaid     = $report->sum('paid_amount');
        $totalDue      = $report->sum('due_amount');
    @endphp
    <div class="summary-strip">
        <div class="stat-card">
            <div class="stat-label">Bookings</div>
            <div class="stat-value">{{ $totalBookings }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value">৳{{ number_format($totalAmount, 0) }}</div>
        </div>
        <div class="stat-card stat-disc">
            <div class="stat-label">Discount</div>
            <div class="stat-value">৳{{ number_format($totalDiscount, 0) }}</div>
        </div>
        <div class="stat-card stat-paid">
            <div class="stat-label">Paid</div>
            <div class="stat-value">৳{{ number_format($totalPaid, 0) }}</div>
        </div>
        <div class="stat-card stat-due">
            <div class="stat-label">Due</div>
            <div class="stat-value">৳{{ number_format($totalDue, 0) }}</div>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-responsive">
    <table class="table table-bordered table-striped report-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Mobile</th>
                <th>Room</th>
                <th class="text-center">Check In</th>
                <th class="text-center">Check Out</th>
                <th class="text-center">Days</th>
                <th class="text-end">Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
            </tr>
        </thead>
        <tbody>
        @forelse($report as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><span class="booking-no">{{ $r->booking_no }}</span></td>
                <td>
                    <div style="font-weight:500;font-size:13px">{{ $r->customer_name ?? '—' }}</div>
                </td>
                <td>{{ $r->customer_mobile ?? '—' }}</td>
                <td><span class="room-tag">{{ $r->room_number ?? '—' }}</span></td>
                <td class="text-center">{{ \Carbon\Carbon::parse($r->check_in_date)->format('d M Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($r->check_out_date)->format('d M Y') }}</td>
                <td class="text-center">{{ $r->total_days }}</td>
                <td class="text-end">{{ number_format($r->total_amount, 2) }}</td>
                <td class="text-end text-orange">{{ $r->discount > 0 ? number_format($r->discount, 2) : '—' }}</td>
                <td class="text-end text-green">{{ number_format($r->paid_amount, 2) }}</td>
                <td class="text-end {{ $r->due_amount > 0 ? 'text-red' : 'text-green' }}">{{ $r->due_amount > 0 ? number_format($r->due_amount, 2) : '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center py-4 text-muted">
                    <i class="ti ti-search" style="font-size:22px"></i><br>
                    No bookings found for the selected filters.
                </td>
            </tr>
        @endforelse
        </tbody>
        @if($report->count())
        <tfoot>
            <tr class="fw-bold" style="background:#f1f3f5">
                <td colspan="8" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
                <td class="text-end text-orange">{{ number_format($totalDiscount, 2) }}</td>
                <td class="text-end text-green">{{ number_format($totalPaid, 2) }}</td>
                <td class="text-end text-red">{{ number_format($totalDue, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    </div>

    <div class="pagination-info mt-3">
        Total {{ $totalBookings }} booking(s)
    </div>

</div>
</div>

{{-- Select2 --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container--default .select2-selection--single {
        height: 36px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: 10px;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
        right: 6px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,.12);
        outline: none;
    }
    .select2-dropdown {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 5px 8px;
        font-size: 13px;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('#customerSelect').select2({
        placeholder: 'All Customer',
        allowClear: true,
        width: '100%',
    });
});
</script>
@endpush

</x-default-layout>