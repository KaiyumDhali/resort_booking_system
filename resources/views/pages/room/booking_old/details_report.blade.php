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
        grid-template-columns: repeat(6, 1fr);
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

    .badge-paid     { background: #d1e7dd; color: #0a3622; }
    .badge-partial  { background: #fff3cd; color: #664d03; }
    .badge-unpaid   { background: #f8d7da; color: #58151c; }
    .badge-checkin  { background: #cfe2ff; color: #084298; }
    .badge-checkout { background: #e2e3e5; color: #41464b; }
    .badge-cancel   { background: #f8d7da; color: #58151c; }

    .badge-pay, .badge-status {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .text-green { color: #198754; font-weight: 600; }
    .text-red   { color: #dc3545; font-weight: 600; }
    .text-orange{ color: #fd7e14; font-weight: 600; }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-info {
        font-size: 13px;
        color: #888;
    }

    .pagination {
        margin: 0;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .pagination .page-item .page-link {
        font-size: 13px;
        padding: 5px 11px;
        border-radius: 6px !important;
        border: 1px solid #dee2e6;
        color: #495057;
        background: #fff;
        line-height: 1.5;
        transition: background 0.15s, border-color 0.15s;
    }

    .pagination .page-item .page-link:hover {
        background: #f1f3f5;
        border-color: #adb5bd;
        color: #212529;
    }

    .pagination .page-item.active .page-link {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #adb5bd;
        pointer-events: none;
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
</style>

<div class="container-fluid mt-4">
<div class="report-card">

    {{-- Page Title --}}
    <div class="page-title">Booking Details Report</div>
    <div class="page-sub">Filter and analyze all room bookings</div>

    {{-- ── FILTER BAR ── --}}
    <form method="GET" action="{{ route('booking.details.report') }}" id="filterForm">
    <div class="filter-bar">
        <div class="row g-2 align-items-end">

            {{-- Customer (Select2) --}}
            <div class="col-md-2">
                <label>Customer</label>
                <select name="customer_id" id="customerSelect" class="form-select" style="width:100%">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->customer_name }}
                            @if($customer->customer_mobile) ({{ $customer->customer_mobile }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Customer Mobile --}}
            <div class="col-md-2">
                <label>Mobile No</label>
                <input type="text" name="customer_mobile" class="form-control"
                       placeholder="01XXXXXXXXX"
                       value="{{ request('customer_mobile') }}">
            </div>

            {{-- NID --}}
            <div class="col-md-2">
                <label>NID No</label>
                <input type="text" name="nid_number" class="form-control"
                       placeholder="NID number…"
                       value="{{ request('nid_number') }}">
            </div>
 {{-- Room --}}
            <div class="col-md-2">
                <label>Room</label>
                <select name="room_id" class="form-select" data-control="select2">
                    <option value="">All rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Status --}}
            <div class="col-md-1">
                <label>Payment</label>
                <select name="pay_status" class="form-select">
                    <option value="">All</option>
                    <option value="paid"    {{ request('pay_status') == 'paid'    ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('pay_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid"  {{ request('pay_status') == 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            {{-- Booking Status --}}
            <div class="col-md-1">
                <label>Status</label>
                <select name="booking_status" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('booking_status') == '1' ? 'selected' : '' }}>Checked In</option>
                    <option value="2" {{ request('booking_status') == '2' ? 'selected' : '' }}>Checked Out</option>
                    <option value="3" {{ request('booking_status') == '3' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Date From --}}
            <div class="col-md-2">
                <label>Check-in from</label>
                <input type="date" name="date_from" class="form-control"
                       value="{{ request('date_from') }}">
            </div>

            {{-- Date To --}}
            <div class="col-md-2">
                <label>Check-in to</label>
                <input type="date" name="date_to" class="form-control"
                       value="{{ request('date_to') }}">
            </div>

           
            {{-- Buttons --}}
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-filter">
                    <i class="ti ti-filter me-1"></i> Filter
                </button>
                <a href="{{ route('booking.details.report') }}" class="btn btn-outline-secondary btn-filter">
                    <i class="ti ti-refresh me-1"></i> Reset
                </a>
                <a href="{{ route('booking.details.report.pdf') . '?' . http_build_query(request()->all()) }}"
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
            'customer'        => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
            'mobile'          => request('customer_mobile'),
            'nid'             => request('nid_number'),
            'date_from'       => request('date_from'),
            'date_to'         => request('date_to'),
            'room_id'         => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))->room_number ?? null) : null,
            'pay_status'      => request('pay_status'),
            'booking_status'  => match(request('booking_status')) { '1'=>'Checked In','2'=>'Checked Out','3'=>'Cancelled', default=>null },
        ]);
    @endphp

    @if(count($activeFilters))
    <div class="active-filters mb-3">
        @foreach($activeFilters as $key => $val)
            @php
                $removeParams = request()->except($key);
                $removeUrl    = route('booking.details.report') . '?' . http_build_query($removeParams);
            @endphp
            <span class="filter-tag">
                {{ ucfirst(str_replace('_', ' ', $key)) }}: <b>{{ $val }}</b>
                <a href="{{ $removeUrl }}">×</a>
            </span>
        @endforeach
    </div>
    @endif

    {{-- ── SUMMARY STRIP ── --}}
    <div class="summary-strip">
        <div class="stat-card">
            <div class="stat-label">Bookings</div>
            <div class="stat-value">{{ $summary['total_bookings'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value">৳{{ number_format($summary['total_amount'], 0) }}</div>
        </div>
        <div class="stat-card stat-disc">
            <div class="stat-label">Discount</div>
            <div class="stat-value">৳{{ number_format($summary['total_discount'], 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Net Total</div>
            <div class="stat-value">৳{{ number_format($summary['net_total'], 0) }}</div>
        </div>
        <div class="stat-card stat-paid">
            <div class="stat-label">Collected</div>
            <div class="stat-value">৳{{ number_format($summary['total_paid'], 0) }}</div>
        </div>
        <div class="stat-card stat-due">
            <div class="stat-label">Due</div>
            <div class="stat-value">৳{{ number_format($summary['total_due'], 0) }}</div>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-responsive">
    <table class="table table-bordered report-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Booking No</th>
                <th>Customer</th>
                <th>Room</th>
                <th class="text-center">Check In</th>
                <th class="text-center">Check Out</th>
                <th class="text-center">Days</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Net</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
                <th class="text-center">Payment</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bookings as $i => $booking)
            @php
                $days       = $booking->total_days ?: 1;
                $net        = $booking->total_amount - $booking->discount;
                $paid       = $payments[$booking->booking_no] ?? 0;
                $due        = max(0, $net - $paid);

                $payStatus  = $paid >= $net ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                $payBadge   = match($payStatus) {
                    'paid'    => ['class' => 'badge-paid',    'label' => 'Paid'],
                    'partial' => ['class' => 'badge-partial', 'label' => 'Partial'],
                    default   => ['class' => 'badge-unpaid',  'label' => 'Unpaid'],
                };

                $statusBadge = match($booking->Booking_status) {
                    2       => ['class' => 'badge-checkout', 'label' => 'Checked Out'],
                    3       => ['class' => 'badge-cancel',   'label' => 'Cancelled'],
                    default => ['class' => 'badge-checkin',  'label' => 'Checked In'],
                };
            @endphp
            <tr>
                <td>{{ $bookings->firstItem() + $i }}</td>
                <td><span class="booking-no">{{ $booking->booking_no }}</span></td>
                <td>
                    <div style="font-weight:500;font-size:13px">{{ $booking->customer->customer_name ?? '—' }}</div>
                    @if($booking->customer?->customer_mobile)
                        <div style="font-size:11px;color:#888">{{ $booking->customer->customer_mobile }}</div>
                    @endif
                </td>
                <td><span class="room-tag">{{ $booking->room->room_number ?? 'Room '.$booking->room_id }}</span></td>
                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                <td class="text-center">{{ $days }}</td>
                <td class="text-end">{{ number_format($booking->total_amount, 0) }}</td>
                <td class="text-end text-orange">{{ $booking->discount > 0 ? number_format($booking->discount, 0) : '—' }}</td>
                <td class="text-end fw-semibold">{{ number_format($net, 0) }}</td>
                <td class="text-end text-green">{{ number_format($paid, 0) }}</td>
                <td class="text-end {{ $due > 0 ? 'text-red' : 'text-green' }}">{{ $due > 0 ? number_format($due, 0) : '—' }}</td>
                <td class="text-center">
                    <span class="badge-pay {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge-status {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center py-4 text-muted">
                    <i class="ti ti-search" style="font-size:22px"></i><br>
                    No bookings found for the selected filters.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>

    {{-- ── PAGINATION ── --}}
    @if($bookings->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
        </div>
        <div>
            {{ $bookings->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
    <div class="pagination-info mt-3">
        Total {{ $bookings->total() }} booking(s)
    </div>
    @endif

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
        placeholder: '-- Select Customer --',
        allowClear: true,
        width: '100%',
    });
});
</script>
@endpush

</x-default-layout>