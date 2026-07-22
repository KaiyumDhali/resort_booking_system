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

    .comp-tag {
        display: inline-block;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 5px;
        font-weight: 600;
    }
    .comp-spot    { background: #eef2ff; color: #4f46e5; }
    .comp-package { background: #fef3e7; color: #b45309; }
    .comp-service { background: #eafaf1; color: #0a7d4c; }
    .comp-room    { background: #fde8ee; color: #b0245b; }

    .badge-pay, .badge-status {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-paid       { background: #d1e7dd; color: #0a3622; }
    .badge-partial    { background: #fff3cd; color: #664d03; }
    .badge-unpaid     { background: #f8d7da; color: #58151c; }
    .badge-approved   { background: #d1e7dd; color: #0a3622; }
    .badge-pending    { background: #fff3cd; color: #664d03; }
    .badge-cancelled  { background: #f8d7da; color: #58151c; }

    /* ── Summary Strip ── */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    @media (max-width: 1399px) { .summary-strip { grid-template-columns: repeat(4, 1fr); } }
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
    .stat-card.stat-refund .stat-value { color: #854F0B; }

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
                        <h4 class="mb-0">Spot Bookings Details Report</h4>
                        <div style="font-size:13px;color:#888;">Component-wise breakdown of every spot booking, grouped by date</div>
                    </div>
                    <a href="{{ route('spot-bookings.details.report.pdf') . '?' . http_build_query(request()->all()) }}"
                       target="_blank" class="btn btn-sm btn-light-primary">
                        <i class="fa fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- ── FILTER BAR ── --}}
                <form method="GET" action="{{ route('spot-bookings.details.report') }}" id="filterForm" class="row g-3 mb-4">

                    <div class="col-md-2">
                        <label>Invoice</label>
                        <input type="text" name="invoice" class="form-control form-control-sm"
                               placeholder="INV0000…" value="{{ request('invoice') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Mobile No</label>
                        <input type="text" name="customer_mobile" class="form-control form-control-sm"
                               placeholder="01XXXXXXXXX" value="{{ request('customer_mobile') }}">
                    </div>

                    <div class="col-md-2">
                        <label>NID No</label>
                        <input type="text" name="nid" class="form-control form-control-sm"
                               placeholder="NID number…" value="{{ request('nid') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Created Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="{{ request('date_from') ?? Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2">
                        <label>Created Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="{{ request('date_to') ?? Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('spot-bookings.details.report') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-rotate"></i> Reset
                        </a>
                    </div>

                </form>

                {{-- ── ACTIVE FILTER TAGS ── --}}
                @php
                    $activeFilters = array_filter([
                        'invoice'   => request('invoice'),
                        'mobile'    => request('customer_mobile'),
                        'nid'       => request('nid'),
                        'date_from' => request('date_from'),
                        'date_to'   => request('date_to'),
                        'status'    => match(request('status')) { '0'=>'Pending','1'=>'Approved','2'=>'Cancelled', default=>null },
                    ]);
                @endphp

                @if(count($activeFilters))
                <div class="active-filters">
                    @foreach($activeFilters as $key => $val)
                        @php
                            $removeParams = request()->except($key);
                            $removeUrl    = route('spot-bookings.details.report') . '?' . http_build_query($removeParams);
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
                    <div class="stat-card stat-refund">
                        <div class="stat-label">Refundable</div>
                        <div class="stat-value">৳{{ number_format($summary['total_refundable'], 0) }}</div>
                    </div>
                </div>

                {{-- ── TABLE (grouped by created_at date) ── --}}
                <div class="table-responsive py-3">
                    <table class="table table-bordered align-middle fs-7 mb-0" style="min-width:1200px;">
                        <thead class="text-center">
                            <tr class="table-secondary">
                                <th class="min-w-90px">Invoice No</th>
                                <th class="min-w-220px text-start">Customer / Component</th>
                                <th class="min-w-90px">Booking Date</th>
                                <th class="min-w-60px">Persons</th>
                                <th class="min-w-100px">Amount</th>
                                <th class="min-w-90px">Discount</th>
                                <th class="min-w-90px">Net</th>
                                <th class="min-w-90px">Paid</th>
                                <th class="min-w-90px">Due</th>
                                <th class="min-w-90px">Refundable</th>
                                <th class="min-w-90px">Payment</th>
                                <th class="min-w-90px">Status</th>
                            </tr>
                        </thead>
                        <tbody>

@php
    $grouped = $bookings->groupBy(function ($item) {
        return Carbon::parse($item->created_at)->format('Y-m-d');
    });

    $grandAmount     = 0;
    $grandDiscount   = 0;
    $grandNet        = 0;
    $grandPaid       = 0;
    $grandDue        = 0;
    $grandRefundable = 0;
@endphp

@forelse($grouped as $date => $dateRows)

    {{-- DATE HEADER --}}
    <tr class="row-date-header">
        <td colspan="12" class="ps-3">Date : {{ Carbon::parse($date)->format('d M Y') }}</td>
    </tr>

    @php
        $dateAmount     = 0;
        $dateDiscount   = 0;
        $dateNet        = 0;
        $datePaid       = 0;
        $dateDue        = 0;
        $dateRefundable = 0;
    @endphp

    @foreach($dateRows as $booking)

        @php
            $bkAmount     = $booking->invoice_amount + $booking->manual_discount_amount;
            $bkDiscount   = $booking->manual_discount_amount ?? 0;
            $bkNet        = $booking->invoice_amount;
            $bkPaid       = $booking->paid_amount;
            $bkDue        = max(0, $bkNet - $bkPaid);
            $bkRefundable = $booking->refundable_amount;

            $payStatus  = $bkPaid >= $bkNet ? 'paid' : ($bkPaid > 0 ? 'partial' : 'unpaid');
            $payBadge   = match($payStatus) {
                'paid'    => ['class' => 'badge-paid',    'label' => 'Paid'],
                'partial' => ['class' => 'badge-partial', 'label' => 'Partial'],
                default   => ['class' => 'badge-unpaid',  'label' => 'Unpaid'],
            };

            $statusBadge = match((int) $booking->status) {
                1       => ['class' => 'badge-approved',  'label' => 'Approved'],
                2       => ['class' => 'badge-cancelled', 'label' => 'Cancelled'],
                default => ['class' => 'badge-pending',   'label' => 'Pending'],
            };

            $components = collect([
                ['label' => 'Spot',    'class' => 'comp-spot',    'amount' => $booking->spot_total - $booking->spot_discount_amount],
                ['label' => 'Package', 'class' => 'comp-package', 'amount' => $booking->package_total],
                ['label' => 'Service', 'class' => 'comp-service', 'amount' => $booking->service_total],
                ['label' => 'Room',    'class' => 'comp-room',    'amount' => $booking->room_total],
            ])->filter(fn($c) => $c['amount'] > 0);
        @endphp

        {{-- INVOICE HEADER --}}
        <tr class="row-invoice-header">
            <td><strong>{{ $booking->invoice_number }}</strong></td>
            <td><strong>{{ $booking->customer_name ?? '—' }}</strong></td>
            <td colspan="10" class="dim" style="font-size:10px;">
                {{ $components->count() }} component(s) &middot; {{ $booking->total_persons }} person(s)
            </td>
        </tr>

        {{-- COMPONENT ROWS --}}
        @forelse($components as $comp)
            <tr>
                <td class="dim text-center" style="font-size:10px;">—</td>
                <td style="padding-left:16px !important;">
                    <span class="comp-tag {{ $comp['class'] }}">{{ $comp['label'] }}</span>
                </td>
                <td class="text-center">{{ Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                <td class="text-center dim">—</td>
                <td class="text-end text-final">{{ number_format($comp['amount'], 2) }}</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
                <td class="text-end dim">—</td>
            </tr>
        @empty
            <tr>
                <td class="dim text-center" style="font-size:10px;">—</td>
                <td colspan="11" class="dim" style="padding-left:16px !important;">No components recorded</td>
            </tr>
        @endforelse

        {{-- INVOICE TOTAL --}}
        <tr class="row-inv-total">
            <td colspan="3" class="text-end" style="font-size:11px; color:#27500A;">
                Invoice Total :
            </td>
            <td class="text-center">{{ $booking->total_persons }}</td>
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
            <td class="text-end" style="color:#854F0B;">
                {{ $bkRefundable > 0 ? number_format($bkRefundable, 2) : '—' }}
            </td>
            <td class="text-center">
                <span class="badge-pay {{ $payBadge['class'] }}" style="font-size:10px;">{{ $payBadge['label'] }}</span>
            </td>
            <td class="text-center">
                <span class="badge-status {{ $statusBadge['class'] }}" style="font-size:10px;">{{ $statusBadge['label'] }}</span>
            </td>
        </tr>

        <tr class="row-spacer"><td colspan="12"></td></tr>

        @php
            $dateAmount     += $bkAmount;
            $dateDiscount   += $bkDiscount;
            $dateNet        += $bkNet;
            $datePaid       += $bkPaid;
            $dateDue        += $bkDue;
            $dateRefundable += $bkRefundable;
        @endphp

    @endforeach

    {{-- DATE TOTAL --}}
    <tr class="row-date-total">
        <td colspan="4" class="text-end" style="font-size:11px;">Date Total :</td>
        <td class="text-end">{{ number_format($dateAmount, 2) }}</td>
        <td class="text-end">{{ $dateDiscount > 0 ? number_format($dateDiscount, 2) : '—' }}</td>
        <td class="text-end">{{ number_format($dateNet, 2) }}</td>
        <td class="text-end">{{ number_format($datePaid, 2) }}</td>
        <td class="text-end">{{ $dateDue > 0 ? number_format($dateDue, 2) : '—' }}</td>
        <td class="text-end">{{ $dateRefundable > 0 ? number_format($dateRefundable, 2) : '—' }}</td>
        <td class="text-end">—</td>
        <td class="text-end">—</td>
    </tr>

    <tr class="row-spacer"><td colspan="12"></td></tr>

    @php
        $grandAmount     += $dateAmount;
        $grandDiscount   += $dateDiscount;
        $grandNet        += $dateNet;
        $grandPaid       += $datePaid;
        $grandDue        += $dateDue;
        $grandRefundable += $dateRefundable;
    @endphp

@empty
    <tr>
        <td colspan="12" class="text-center text-danger py-4">
            <i class="ti ti-search" style="font-size:22px"></i><br>
            No spot bookings found for the selected filters.
        </td>
    </tr>
@endforelse

                            {{-- GRAND TOTAL --}}
                            <tr class="row-grand-total">
                                <td colspan="4" class="text-end">Grand Total :</td>
                                <td class="text-end text-final" style="font-size:13px;">{{ number_format($grandAmount, 2) }}</td>
                                <td class="text-end">{{ $grandDiscount > 0 ? number_format($grandDiscount, 2) : '—' }}</td>
                                <td class="text-end">{{ number_format($grandNet, 2) }}</td>
                                <td class="text-end text-paid">{{ number_format($grandPaid, 2) }}</td>
                                <td class="text-end text-due">{{ $grandDue > 0 ? number_format($grandDue, 2) : '—' }}</td>
                                <td class="text-end">{{ $grandRefundable > 0 ? number_format($grandRefundable, 2) : '—' }}</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

</x-default-layout>