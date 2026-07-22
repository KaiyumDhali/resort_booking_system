{{-- Top of your Blade view --}}
@php
    use Carbon\Carbon;
@endphp

<x-default-layout>

<style>
    .card .card-header { min-height: 40px; }
    .table > :not(caption) > * > * { padding: 0.3rem 0.4rem !important; }
    .sub-label { font-size: 10px; color: #888; display: block; font-weight: 400; }

    .row-date-header    td { background: #2c2c2a !important; color: #d3d1c7 !important; font-weight: 600; }
    .row-invoice-header  td { background: #E6F1FB !important; color: #0C447C !important; font-weight: 600; }
    .row-inv-total       td { background: #EAF3DE !important; font-weight: 600; }
    .row-date-total      td { background: #FAEEDA !important; font-weight: 600; color: #633806 !important; }
    .row-grand-total     td { background: #D3D1C7 !important; font-weight: 700; color: #2C2C2A !important; }
    .row-spacer          td { padding: 3px !important; border: none !important; background: transparent !important; }

    .text-disc   { color: #A32D2D !important; }
    .text-profit { color: #27500A !important; }
    .text-due    { color: #854F0B !important; }
    .text-paid   { color: #185FA5 !important; }
    .text-final  { color: #27500A !important; font-weight: 600; }
    .dim         { color: #aaa !important; }

    .room-tag {
        display: inline-block;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 5px;
        font-weight: 600;
    }

    .badge-pay, .badge-status {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-paid     { background: #d1e7dd; color: #0a3622; }
    .badge-partial  { background: #fff3cd; color: #664d03; }
    .badge-unpaid   { background: #f8d7da; color: #58151c; }
    .badge-checkin  { background: #cfe2ff; color: #084298; }
    .badge-checkout { background: #e2e3e5; color: #41464b; }
    .badge-cancel   { background: #f8d7da; color: #58151c; }

    /* ── Summary Strip ── */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    @media (max-width: 1199px) { .summary-strip { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 767px)  { .summary-strip { grid-template-columns: repeat(2, 1fr); } }

    .stat-card {
        background: #f8f9fb;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 14px;
    }
    .stat-card .stat-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.5px; color: #888; margin-bottom: 4px;
    }
    .stat-card .stat-value { font-size: 18px; font-weight: 700; color: #1a1a2e; }
    .stat-card.stat-paid .stat-value { color: #198754; }
    .stat-card.stat-due  .stat-value { color: #dc3545; }
    .stat-card.stat-disc .stat-value { color: #fd7e14; }

    /* ── Active filter tags ── */
    .active-filters { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
    .filter-tag {
        display: inline-flex; align-items: center; gap: 5px;
        background: #e7f1ff; color: #0d6efd; font-size: 12px;
        padding: 3px 10px; border-radius: 20px; font-weight: 500;
    }
    .filter-tag a { color: #0d6efd; text-decoration: none; font-size: 14px; line-height: 1; }
</style>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h4 class="mb-0">Booking Details Report</h4>
                        <div style="font-size:13px;color:#888;">Filter and analyze all room bookings</div>
                    </div>
                    <a href="{{ route('booking.details.report.pdf') . '?' . http_build_query(request()->all()) }}"
                       target="_blank" class="btn btn-sm btn-light-primary">
                        <i class="fa fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- ── FILTER BAR ── --}}
                <form method="GET" action="{{ route('booking.details.report') }}" id="filterForm" class="row g-3 mb-4">

                    <div class="col-md-3">
                        <label>Customer</label>
                        <select name="customer_id" id="customerSelect" class="form-select form-select-sm" style="width:100%">
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

                    <div class="col-md-2">
                        <label>Mobile No</label>
                        <input type="text" name="customer_mobile" class="form-control form-control-sm"
                               placeholder="01XXXXXXXXX"
                               value="{{ request('customer_mobile') }}">
                    </div>

                    <div class="col-md-2">
                        <label>NID No</label>
                        <input type="text" name="nid_number" class="form-control form-control-sm"
                               placeholder="NID number…"
                               value="{{ request('nid_number') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Room</label>
                        <select name="room_id" class="form-select form-select-sm" data-control="select2">
                            <option value="">All rooms</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Payment</label>
                        <select name="pay_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="paid"    {{ request('pay_status') == 'paid'    ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ request('pay_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid"  {{ request('pay_status') == 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="booking_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1" {{ request('booking_status') == '1' ? 'selected' : '' }}>Checked In</option>
                            <option value="2" {{ request('booking_status') == '2' ? 'selected' : '' }}>Checked Out</option>
                            <option value="3" {{ request('booking_status') == '3' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    {{-- Date filters now represent BOOKING/INVOICE CREATION DATE (created_at), defaulting to today --}}
                    <div class="col-md-2">
                        <label>Booking Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="{{ request('date_from') ?? Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2">
                        <label>Booking Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="{{ request('date_to') ?? Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('booking.details.report') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-rotate"></i> Reset
                        </a>
                    </div>

                </form>

                {{-- ── ACTIVE FILTER TAGS ── --}}
                @php
                    $activeFilters = array_filter([
                        'customer'       => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
                        'mobile'         => request('customer_mobile'),
                        'nid'            => request('nid_number'),
                        'date_from'      => request('date_from'),
                        'date_to'        => request('date_to'),
                        'room_id'        => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))->room_number ?? null) : null,
                        'pay_status'     => request('pay_status'),
                        'booking_status' => match(request('booking_status')) { '1'=>'Checked In','2'=>'Checked Out','3'=>'Cancelled', default=>null },
                    ]);
                @endphp

                @if(count($activeFilters))
                <div class="active-filters">
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

                {{-- ── TABLE (grouped by created_at date, like Sales Details Report) ── --}}
                <div class="table-responsive py-3">
                    <table class="table table-bordered align-middle fs-7 mb-0" style="min-width:1100px;">
                        <thead class="text-center">
                            <tr class="table-secondary">
                                <th class="min-w-90px">Booking No</th>
                                <th class="min-w-220px text-start">Customer / Room</th>
                                <th class="min-w-90px">Check-in</th>
                                <th class="min-w-90px">Check-out</th>
                                <th class="min-w-60px">Days</th>
                                <th class="min-w-100px">Amount</th>
                                <th class="min-w-90px">Discount</th>
                                <th class="min-w-90px">Net</th>
                                <th class="min-w-90px">Paid</th>
                                <th class="min-w-90px">Due</th>
                                <th class="min-w-100px">Payment</th>
                                <!-- <th class="min-w-90px">Status</th> -->
                            </tr>
                        </thead>
                        <tbody>

@php
    // Group all rows by the DATE the booking was CREATED (created_at) —
    // mirrors the Sales Details Report's date grouping.
    $grouped = collect($bookings)->groupBy(function ($item) {
        return Carbon::parse($item->created_at)->format('Y-m-d');
    });

    $grandAmount   = 0;
    $grandDiscount = 0;
    $grandNet      = 0;
    $grandPaid     = 0;
    $grandDue      = 0;
@endphp

@forelse($grouped as $date => $dateRows)

    {{-- DATE HEADER --}}
    <tr class="row-date-header">
        <td colspan="11" class="ps-3">Date : {{ Carbon::parse($date)->format('d M Y') }}</td>
    </tr>

    @php
        $bookingGroups = $dateRows->groupBy('booking_no');
        $dateAmount    = 0;
        $dateDiscount  = 0;
        $dateNet       = 0;
        $datePaid      = 0;
        $dateDue       = 0;
    @endphp

    @foreach($bookingGroups as $bookingNo => $rows)

        @php
            $first      = $rows->first();
            $isInv      = str_starts_with($bookingNo, 'INV');

            $bkAmount   = $rows->sum('total_amount');
            $bkDiscount = $first->discount ?? 0;
            $bkNet      = $bkAmount - $bkDiscount;
            $bkPaid     = $isInv ? $bkNet : ($payments[$bookingNo] ?? 0);
            $bkDue      = $isInv ? 0 : max(0, $bkNet - $bkPaid);

            $payStatus  = $bkPaid >= $bkNet ? 'paid' : ($bkPaid > 0 ? 'partial' : 'unpaid');
            $payBadge   = match($payStatus) {
                'paid'    => ['class' => 'badge-paid',    'label' => 'Paid'],
                'partial' => ['class' => 'badge-partial', 'label' => 'Partial'],
                default   => ['class' => 'badge-unpaid',  'label' => 'Unpaid'],
            };

            $statusBadge = match($first->Booking_status) {
                2       => ['class' => 'badge-checkout', 'label' => 'Checked Out'],
                3       => ['class' => 'badge-cancel',   'label' => 'Cancelled'],
                default => ['class' => 'badge-checkin',  'label' => 'Checked In'],
            };
        @endphp

        {{-- BOOKING HEADER --}}
        <tr class="row-invoice-header">
            <td><strong>{{ $bookingNo }}</strong></td>
            <td><strong>{{ $first->customer_name ?? '—' }}</strong></td>
            <td colspan="9" class="dim" style="font-size:10px;">
                {{ $rows->count() }} room(s)
            </td>
        </tr>

        {{-- ROOM ROWS --}}
        @foreach($rows as $row)
            <tr>
                <td class="dim text-center" style="font-size:10px;">—</td>
                <td style="padding-left:16px !important;">
                    <span class="room-tag">{{ $row->room->room_number ?? 'Room '.$row->room_id }}</span>
                </td>
                <td class="text-center">{{ $row->check_in_date ? Carbon::parse($row->check_in_date)->format('d M Y') : '—' }}</td>
                <td class="text-center">{{ $row->check_out_date ? Carbon::parse($row->check_out_date)->format('d M Y') : '—' }}</td>
                <td class="text-center">{{ $row->total_days ?: 1 }}</td>
                <td class="text-end text-final">{{ number_format($row->total_amount, 2) }}</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
            </tr>
        @endforeach

        {{-- BOOKING TOTAL --}}
        <tr class="row-inv-total">
            <td colspan="5" class="text-end" style="font-size:11px; color:#27500A;">
                Booking Total :
            </td>
            <td class="text-end text-final" style="font-size:13px;">
                {{ number_format($bkAmount, 2) }}
            </td>
            <td class="text-end text-disc">
                {{ $bkDiscount > 0 ? number_format($bkDiscount, 2) : '—' }}
            </td>
            <td class="text-end fw-semibold">
                {{ number_format($bkNet, 2) }}
            </td>
            <td class="text-end text-paid">
                {{ number_format($bkPaid, 2) }}
                <span class="sub-label">Paid</span>
            </td>
            <td class="text-end text-due">
                {{ $bkDue > 0 ? number_format($bkDue, 2) : '—' }}
                <span class="sub-label">Due</span>
            </td>
            <td class="text-center">
                @if($isInv)
                    <span class="badge-pay badge-paid" style="font-size:10px;">Spot Booking</span>
                @else
                    <span class="badge-pay {{ $payBadge['class'] }}" style="font-size:10px;">{{ $payBadge['label'] }}</span>
                @endif
            </td>
            <!-- <td class="text-center">
                <span class="badge-status {{ $statusBadge['class'] }}" style="font-size:10px;">{{ $statusBadge['label'] }}</span>
            </td> -->
        </tr>

        <tr class="row-spacer"><td colspan="11"></td></tr>

        @php
            $dateAmount   += $bkAmount;
            $dateDiscount += $bkDiscount;
            $dateNet      += $bkNet;
            $datePaid     += $bkPaid;
            $dateDue      += $bkDue;
        @endphp

    @endforeach

    {{-- DATE TOTAL --}}
    <tr class="row-date-total">
        <td colspan="5" class="text-end" style="font-size:11px;">Date Total :</td>
        <td class="text-end">{{ number_format($dateAmount, 2) }}</td>
        <td class="text-end">{{ $dateDiscount > 0 ? number_format($dateDiscount, 2) : '—' }}</td>
        <td class="text-end">{{ number_format($dateNet, 2) }}</td>
        <td class="text-end">{{ number_format($datePaid, 2) }}</td>
        <td class="text-end">{{ $dateDue > 0 ? number_format($dateDue, 2) : '—' }}</td>
        <td class="text-end">—</td>
    </tr>

    <tr class="row-spacer"><td colspan="11"></td></tr>

    @php
        $grandAmount   += $dateAmount;
        $grandDiscount += $dateDiscount;
        $grandNet      += $dateNet;
        $grandPaid     += $datePaid;
        $grandDue      += $dateDue;
    @endphp

@empty
    <tr>
        <td colspan="11" class="text-center text-danger py-4">
            <i class="ti ti-search" style="font-size:22px"></i><br>
            No bookings found for the selected filters.
        </td>
    </tr>
@endforelse

                            {{-- GRAND TOTAL --}}
                            <tr class="row-grand-total">
                                <td colspan="5" class="text-end">Grand Total :</td>

                                <td class="text-end text-final" style="font-size:13px;">{{ number_format($grandAmount, 2) }}</td>
                                <td class="text-end">{{ $grandDiscount > 0 ? number_format($grandDiscount, 2) : '—' }}</td>
                                <td class="text-end">{{ number_format($grandNet, 2) }}</td>
                                <td class="text-end text-paid">{{ number_format($grandPaid, 2) }}</td>
                                <td class="text-end text-due">{{ $grandDue > 0 ? number_format($grandDue, 2) : '—' }}</td>
                                <td class="text-end">—</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Select2 --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container--default .select2-selection--single {
        height: 32px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        padding-left: 10px;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px;
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