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

    .badge-pay {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-paid    { background: #d1e7dd; color: #0a3622; }
    .badge-partial { background: #fff3cd; color: #664d03; }
    .badge-unpaid  { background: #f8d7da; color: #58151c; }

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

    .status-select.status-0 { background: #fff3cd; color: #664d03; }
    .status-select.status-1 { background: #d1e7dd; color: #0a3622; }
    .status-select.status-2 { background: #f8d7da; color: #58151c; }

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

<div class="container-fluid mt-4">
<div class="report-card">

    {{-- ERROR / SUCCESS --}}
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
        <div class="alert alert-{{ session('alert-type') }}">
            {{ session('message') }}
        </div>
    @endif

    {{-- Page Title --}}
    <div class="page-title">Booking List</div>
    <div class="page-sub">View, filter and manage all room bookings</div>

    {{-- ── FILTER BAR ── --}}
    <form method="GET" action="{{ route('booking.index') }}">
    <div class="filter-bar">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-sm-6 col-md-2">
                <label>Start Date</label>
                <input type="date" name="start_date"
                    value="{{ request('start_date', now()->format('Y-m-d')) }}"
                    class="form-control">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>End Date</label>
                <input type="date" name="end_date"
                    value="{{ request('end_date', now()->format('Y-m-d')) }}"
                    class="form-control">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Booking No</label>
                <input type="text" name="booking_no" value="{{ request('booking_no') }}" class="form-control" placeholder="Booking no…">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Customer NID</label>
                <input type="text" name="nid" value="{{ request('nid') }}" class="form-control" placeholder="NID number…">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Pending</option>
                    <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Approved</option>
                    <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-filter w-100">
                    <i class="ti ti-filter me-1"></i> Filter
                </button>
                <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary btn-filter">
                    <i class="ti ti-refresh">reset</i>
                </a>
                <a href="{{ route('booking.list.pdf') }}" class="btn btn-outline-secondary btn-filter ">
                    <i class="ti ti-refresh">PDF</i>
                </a>


            </div>

        </div>
    </div>
    </form>

    {{-- ── ACTIVE FILTER TAGS ── --}}
    @php
        $activeFilters = array_filter([
            'start_date' => request('start_date'),
            'end_date'   => request('end_date'),
            'booking_no' => request('booking_no'),
            'nid'        => request('nid'),
            'status'     => match(request('status')) { '0'=>'Pending','1'=>'Approved','2'=>'Canceled', default=>null },
        ]);
    @endphp

    @if(count($activeFilters))
    <div class="active-filters">
        @foreach($activeFilters as $key => $val)
            @php
                $removeParams = request()->except($key);
                $removeUrl    = route('booking.index') . '?' . http_build_query($removeParams);
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
                    <th>Booking No</th>
                    <th>Customer</th>
                    <th class="text-center">Rooms</th>
                    <th class="text-center">Check-in</th>
                    <th class="text-center">Check-out</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($bookings as $bookingNo => $group)

                @php
                    $first     = $group->first();
                    $roomCount = $group->count();
                    $total     = $group->sum('total_amount');
                    $paid      = $payments[$bookingNo] ?? 0;
                    $net       = $total - $first->discount;
                    $due       = max(0, $net - $paid);

                    // পুরো booking group এর overall check-in/check-out রেঞ্জ
                    $checkIn  = $group->min('check_in_date');
                    $checkOut = $group->max('check_out_date');

                    $payStatus = $paid >= $net ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                    $payBadge  = match($payStatus) {
                        'paid'    => ['class' => 'badge-paid',    'label' => 'Paid'],
                        'partial' => ['class' => 'badge-partial', 'label' => 'Partial'],
                        default   => ['class' => 'badge-unpaid',  'label' => 'Unpaid'],
                    };
                @endphp

                @php
    $isInv = str_starts_with($bookingNo, 'INV');
@endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        <span class="booking-no">{{ $bookingNo }}</span>
                        <div style="font-size:11px; color:#888; margin-top:2px;">
                            {{ \Carbon\Carbon::parse($first->created_at)->format('Y-m-d') }}
                        </div>
                    </td>

                    <td>
                        <div style="font-weight:500;font-size:13px">{{ $first->customer_name ?? '—' }}</div>
                        @if($first->customer?->customer_mobile)
                            <div style="font-size:11px;color:#888">{{ $first->customer_mobile }}</div>
                        @endif
                    </td>

                    <td class="text-center"><span class="room-tag">{{ $roomCount }} Room(s)</span></td>
                      <td class="text-center">
                        @if($checkIn)
                            <div style="font-size:12.5px;font-weight:600;color:#1a1a2e;">
                                {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($checkOut)
                            <div style="font-size:12.5px;font-weight:600;color:#1a1a2e;">
                                {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($total, 2) }}</td>

                    <td class="text-end text-orange">{{ $first->discount > 0 ? number_format($first->discount, 2) : '—' }}</td>

                    <td class="text-end">
                        @if($isInv)
                            <span class="badge-pay badge-paid" style="font-size:11px;">Paid by Spot Booking</span>
                        @else
                            <span class="text-green">{{ number_format($paid, 2) }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($isInv)
                            <span class="badge-pay badge-paid" style="font-size:11px;">0.00</span>
                        @else
                            <span class="text-end {{ $due > 0 ? 'text-red' : 'text-green' }}">{{ $due > 0 ? number_format($due, 2) : '—' }}</span>
                        @endif
                    </td>

                    

                    {{-- STATUS --}}
                    <td class="text-center">
                       <select class="form-select status-select booking-status status-{{ $first->Booking_status }}"
                       data-booking-no="{{ $bookingNo }}">
                            <option value="0" {{ $first->Booking_status == 0 ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ $first->Booking_status == 1 ? 'selected' : '' }}>Approved</option>
                            <option value="2" {{ $first->Booking_status == 2 ? 'selected' : '' }}>Canceled</option>
                        </select>
                        <!-- <div class="mt-1">
                            <span class="badge-pay {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
                        </div> -->
                    </td>

                    {{-- ACTION --}}
                    <td>
    <div class="action-btns">

        <a class="btn btn-info text-white {{ $isInv ? 'disabled opacity-50 pointer-events-none' : '' }}"
           href="{{ route('booking.invoice.details', $bookingNo) }}">
            <i class="ti ti-eye"></i> Details
        </a>

        <a class="btn btn-primary {{ $isInv ? 'disabled opacity-50 pointer-events-none' : '' }}"
           href="{{ route('booking.edit1', $bookingNo) }}">
            <i class="ti ti-edit"></i> Edit
        </a>

        <a class="btn btn-success {{ $isInv ? 'disabled opacity-50 pointer-events-none' : '' }}"
           href="{{ route('payment.edit', $bookingNo) }}">
            <i class="ti ti-cash"></i> Payment
        </a>

        <a class="btn btn-dark {{ $isInv ? 'disabled opacity-50 pointer-events-none' : '' }}"
           href="{{ route('booking.invoice.pdf', $bookingNo) }}">
            <i class="ti ti-file-type-pdf"></i> Invoice
        </a>

    </div>
</td>

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

{{-- STATUS UPDATE SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.booking-status').forEach(function (dropdown) {
    dropdown.addEventListener('change', function () {

        let bookingNo = this.getAttribute('data-booking-no'); // ✅ id → no
        let status = this.value;
        let select = this;

        select.classList.remove('status-0', 'status-1', 'status-2');
        select.classList.add('status-' + status);

        fetch("{{ route('update.booking.status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                booking_no: bookingNo,  // ✅ id → booking_no
                booking_status: status
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', 'Status updated', 'success');
            } else {
                Swal.fire('Error', 'Update failed', 'error');
            }
        });
    });
});

});
</script>

</x-default-layout>