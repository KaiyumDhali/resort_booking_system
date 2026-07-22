@php
    $imagePath = public_path(Storage::url($data['company_logo_one']));
    $imageDataUri = file_exists($imagePath)
        ? 'data:' . mime_content_type($imagePath) . ';base64,' . base64_encode(file_get_contents($imagePath))
        : null;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ $data['company_name'] }}</title>

    <style>
        @page {
            margin: 10px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #2c2c2c;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .report-title {
            text-align: center;
        }

        .report-title h3 {
            margin: 0;
            font-size: 18px;
        }

        .report-title h5 {
            margin: 2px 0;
            font-size: 12px;
            font-weight: 600;
        }

        .filter-section {
            background: #f0f4ff;
            border: 1px solid #c7d7f9;
            border-radius: 5px;
            padding: 5px 8px;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .filter-section strong {
            color: #0d6efd;
        }

        .filter-item {
            display: inline-block;
            background: #fff;
            border: 1px solid #c7d7f9;
            border-radius: 3px;
            padding: 2px 6px;
            margin: 2px;
        }

        /* ── Report table: fixed layout so all columns always fit the page width ── */
        .report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 9px;
        }

        .report-table th {
            background: #f2f2f2;
            border: 1px solid #cfcfcf;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 8.5px;
            word-wrap: break-word;
        }

        .report-table td {
            border: 1px solid #d9d9d9;
            padding: 5px 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fcfcfc;
        }

        /* Column widths — sum to 100% */
        .col-sl        { width: 3%; }
        .col-booking   { width: 12%; }
        .col-customer  { width: 17%; }
        .col-rooms     { width: 8%; }
        .col-checkin   { width: 10%; }
        .col-checkout  { width: 10%; }
        .col-total     { width: 10%; }
        .col-discount  { width: 9%; }
        .col-paid      { width: 9%; }
        .col-due       { width: 9%; }
        .col-status    { width: 8%; }

        .booking-no {
            font-weight: bold;
            color: #0d6efd;
        }

        .booking-date {
            display: block;
            font-size: 7.5px;
            color: #888;
            font-weight: normal;
            margin-top: 1px;
        }

        .customer-mobile {
            display: block;
            font-size: 7.5px;
            color: #888;
            font-weight: normal;
            margin-top: 1px;
        }

        .text-center { text-align: center !important; }
        .text-right  { text-align: right !important; }

        .text-green  { color: #198754; font-weight: bold; }
        .text-red    { color: #dc3545; font-weight: bold; }
        .text-orange { color: #fd7e14; font-weight: bold; }

        .room-tag {
            display: inline-block;
            background: #eef2ff;
            color: #4f46e5;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .spot-tag {
            font-size: 7.5px;
            color: #6f42c1;
            font-weight: bold;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-0 { background: #fff3cd; color: #664d03; }
        .status-1 { background: #d1e7dd; color: #0a3622; }
        .status-2 { background: #f8d7da; color: #58151c; }

        .footer-note {
            margin-top: 10px;
            font-size: 9px;
            color: #888;
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <table class="header-table" style="margin-bottom: 10px;">
        <tr>
            <td style="width:90px;">
                @if($imageDataUri)
                    <img src="{{ $imageDataUri }}" height="50">
                @endif
            </td>

            <td class="report-title">
                <h3>{{ $data['company_name'] }}</h3>
                <h5>Booking List</h5>
                <h5>
                    Date:
                    {{ $data['start_date'] }} to {{ $data['end_date'] }}
                </h5>
            </td>

            <td style="width:90px;"></td>
        </tr>
    </table>

    {{-- Filters --}}
    @php
        $statusLabel = match(request('status')) {
            '0' => 'Pending', '1' => 'Approved', '2' => 'Canceled', default => null
        };

        $appliedFilters = array_filter([
            'Booking No' => request('booking_no'),
            'NID'        => request('nid'),
            'Status'     => $statusLabel,
            'Start Date' => request('start_date'),
            'End Date'   => request('end_date'),
        ]);
    @endphp

    @if(count($appliedFilters))
        <div class="filter-section">
            <strong>Filters Applied:</strong>

            @foreach($appliedFilters as $label => $val)
                <span class="filter-item">
                    <b>{{ $label }}:</b> {{ $val }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <table class="report-table">

        <colgroup>
            <col class="col-sl">
            <col class="col-booking">
            <col class="col-customer">
            <col class="col-rooms">
            <col class="col-checkin">
            <col class="col-checkout">
            <col class="col-total">
            <col class="col-discount">
            <col class="col-paid">
            <col class="col-due">
            <col class="col-status">
        </colgroup>

        <thead>
            <tr>
                <th>#</th>
                <th>Booking No</th>
                <th>Customer</th>
                <th>Rooms</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total</th>
                <th>Discount</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @php
                $grandTotal    = 0;
                $grandDiscount = 0;
                $grandPaid     = 0;
                $grandDue      = 0;
            @endphp

            @forelse($bookings as $bookingNo => $group)

                @php
                    $first     = $group->first();
                    $roomCount = $group->count();
                    $total     = $group->sum('total_amount');
                    $paid      = $payments[$bookingNo] ?? 0;
                    $net       = $total - $first->discount;
                    $due       = max(0, $net - $paid);

                    $checkIn  = $group->min('check_in_date');
                    $checkOut = $group->max('check_out_date');

                    $isInv = str_starts_with($bookingNo, 'INV');

                    $statusLabelMap = [0 => 'Pending', 1 => 'Approved', 2 => 'Canceled'];
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        <span class="booking-no">{{ $bookingNo }}</span>
                        <span class="booking-date">{{ \Carbon\Carbon::parse($first->created_at)->format('d M Y') }}</span>
                    </td>

                    <td>
                        {{ $first->customer_name ?? '—' }}
                        @if($first->customer_mobile)
                            <span class="customer-mobile">{{ $first->customer_mobile }}</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <span class="room-tag">{{ $roomCount }} Room(s)</span>
                    </td>

                    <td class="text-center">
                        {{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d M Y') : '—' }}
                    </td>

                    <td class="text-center">
                        {{ $checkOut ? \Carbon\Carbon::parse($checkOut)->format('d M Y') : '—' }}
                    </td>

                    <td class="text-right">{{ number_format($total, 2) }}</td>

                    <td class="text-right text-orange">
                        {{ $first->discount > 0 ? number_format($first->discount, 2) : '—' }}
                    </td>

                    <td class="text-right">
                        @if($isInv)
                            <span class="spot-tag">Spot Booking</span>
                        @else
                            <span class="text-green">{{ number_format($paid, 2) }}</span>
                        @endif
                    </td>

                    <td class="text-right">
                        @if($isInv)
                            <span class="spot-tag">0.00</span>
                        @else
                            <span class="{{ $due > 0 ? 'text-red' : 'text-green' }}">
                                {{ $due > 0 ? number_format($due, 2) : '—' }}
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        <span class="badge-status status-{{ $first->Booking_status }}">
                            {{ $statusLabelMap[$first->Booking_status] ?? '—' }}
                        </span>
                    </td>
                </tr>

                @php
                    $grandTotal    += $total;
                    $grandDiscount += $first->discount;
                    $grandPaid     += $isInv ? 0 : $paid;
                    $grandDue      += $isInv ? 0 : $due;
                @endphp

            @empty
                <tr>
                    <td colspan="11" class="text-center">
                        No bookings found for the selected filters !!!
                    </td>
                </tr>
            @endforelse

        </tbody>

        @if(count($bookings))
        <tfoot>
            <tr style="background:#f5f5f5; font-weight:bold;">
                <td colspan="6" class="text-right">Total :</td>
                <td class="text-right">{{ number_format($grandTotal, 2) }}</td>
                <td class="text-right text-orange">{{ $grandDiscount > 0 ? number_format($grandDiscount, 2) : '—' }}</td>
                <td class="text-right text-green">{{ number_format($grandPaid, 2) }}</td>
                <td class="text-right text-red">{{ $grandDue > 0 ? number_format($grandDue, 2) : '—' }}</td>
                <td>—</td>
            </tr>
        </tfoot>
        @endif

    </table>

    <div class="footer-note">
        Total {{ count($bookings) }} booking(s)
    </div>

</body>

</html>