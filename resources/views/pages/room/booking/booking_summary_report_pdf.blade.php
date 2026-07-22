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

        /* ── Report table: fixed layout so 13 columns always fit the page width ── */
        .report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .report-table th {
            background: #f2f2f2;
            border: 1px solid #cfcfcf;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            word-wrap: break-word;
        }

        .report-table td {
            border: 1px solid #d9d9d9;
            padding: 4px 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fcfcfc;
        }

        /* Column widths — sum to 100% */
        .col-sl       { width: 3%; }
        .col-invoice  { width: 9%; }
        .col-customer { width: 18%; }
        .col-rooms    { width: 9%; }
        .col-checkin  { width: 9%; }
        .col-checkout { width: 9%; }
        .col-days     { width: 4%; }
        .col-total    { width: 10%; }
        .col-discount { width: 9%; }
        .col-net      { width: 10%; }
        .col-paid     { width: 10%; }
        .col-due      { width: 9%; }

        .customer-mobile {
            display: block;
            font-size: 7.5px;
            color: #888;
            font-weight: normal;
            margin-top: 1px;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-green {
            color: #198754;
            font-weight: bold;
        }

        .text-red {
            color: #dc3545;
            font-weight: bold;
        }

        .text-orange {
            color: #fd7e14;
            font-weight: bold;
        }

        .room-tag {
            display: inline-block;
            background: #eef2ff;
            color: #4f46e5;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
        }

        .spot-tag {
            font-size: 7.5px;
            color: #6f42c1;
            font-weight: bold;
        }

        .double-underline {
            border-bottom: 3px double #000;
        }

        .footer-row {
            background: #f5f5f5;
            font-weight: bold;
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
                <h5>Booking Summary Report</h5>
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
        $appliedFilters = array_filter([
            'Invoice'    => request('invoice'),
            'Customer'   => request('customer_id')
                ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null)
                : null,
            'Mobile'     => request('customer_mobile'),
            'NID'        => request('nid_number'),
            'Room'       => request('room_id')
                ? ($rooms->firstWhere('id', request('room_id'))?->room_name ?? null)
                : null,
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

    {{-- Summary --}}
    @php
        $filteredReport = $report->filter(
            fn($r) => !str_starts_with($r->booking_no, 'INV')
        );

        $totalBookings = $report->count();
        $totalAmount = $filteredReport->sum('total_amount');
        $totalDiscount = $filteredReport->sum('discount');

        $netTotal = $totalAmount - $totalDiscount;

        $totalPaid = $allPayments->sum();

        $totalDue = $netTotal - $totalPaid;
    @endphp

    <table style="margin-bottom:10px; font-size:9px;">
        <tr>
            <td><strong>Bookings:</strong> {{ $totalBookings }}</td>
            <td class="text-right">
                <strong>Total:</strong>
                {{ number_format($totalAmount, 2) }}
            </td>
            <td class="text-right">
                <strong>Discount:</strong>
                {{ number_format($totalDiscount, 2) }}
            </td>
            <td class="text-right">
                <strong>Net:</strong>
                {{ number_format($netTotal, 2) }}
            </td>
            <td class="text-right text-green">
                <strong>Paid:</strong>
                {{ number_format($totalPaid, 2) }}
            </td>
            <td class="text-right text-red">
                <strong>Due:</strong>
                {{ number_format($totalDue, 2) }}
            </td>
        </tr>
    </table>

    {{-- Table --}}
    <table class="report-table">

        <colgroup>
            <col class="col-sl">
            <col class="col-invoice">
            <col class="col-customer">
            <col class="col-rooms">
            <col class="col-checkin">
            <col class="col-checkout">
            <col class="col-days">
            <col class="col-total">
            <col class="col-discount">
            <col class="col-net">
            <col class="col-paid">
            <col class="col-due">
        </colgroup>

        <thead>
            <tr>
                <th>SL</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Rooms</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Days</th>
                <th>Total</th>
                <th>Discount</th>
                <th>Net</th>
                <th>Paid</th>
                <th>Due</th>
            </tr>
        </thead>

        <tbody>

            @forelse($report as $r)

                @php
                    $isINV = str_starts_with($r->booking_no, 'INV');
                    $paid  = $isINV ? 0 : ($allPayments[$r->booking_no] ?? 0);
                    $net   = $r->total_amount - $r->discount;  // ✅ INV তেও calculate হবে
                    $due   = $isINV ? 0 : max(0, $net - $paid);
                @endphp

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        <strong>{{ $r->booking_no }}</strong>
                    </td>

                    <td>
                        {{ $r->customer_name ?? '—' }}
                        @if($r->customer_mobile)
                            <span class="customer-mobile">{{ $r->customer_mobile }}</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <span class="room-tag">
                            {{ $r->room_numbers ?? '—' }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($r->check_in_date)->format('d M y') }}
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($r->check_out_date)->format('d M y') }}
                    </td>

                    <td class="text-center">
                        {{ $r->total_days }}
                    </td>

                    <td class="text-right">
                        {{ number_format($r->total_amount, 2) }}
                    </td>

                    <td class="text-right text-orange">
                        {{ $r->discount > 0 ? number_format($r->discount, 2) : '—' }}
                    </td>

                    <td class="text-right">
                        {{ number_format($net, 2) }}
                    </td>

                    <td class="text-right text-green">
                        @if($isINV)
                            <span class="spot-tag">Spot Booking</span>
                        @else
                            {{ number_format($paid, 2) }}
                        @endif
                    </td>

                    <td class="text-right {{ $due > 0 ? 'text-red' : '' }}">
                        @if($isINV)
                            <span class="spot-tag">Paid</span>
                        @else
                            {{ $due > 0 ? number_format($due, 2) : '—' }}
                        @endif
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="12" class="text-center">
                        No bookings found for the selected filters !!!
                    </td>
                </tr>

            @endforelse

        </tbody>

        <tfoot>

            <tr class="footer-row">

                <td colspan="7" class="text-right">
                    <strong>Total:</strong>
                </td>

                <td class="text-right">
                    <strong>
                        <span class="double-underline">
                            {{ number_format($totalAmount, 2) }}
                        </span>
                    </strong>
                </td>

                <td class="text-right">
                    <strong>
                        <span class="double-underline">
                            {{ number_format($totalDiscount, 2) }}
                        </span>
                    </strong>
                </td>

                <td class="text-right">
                    <strong>
                        <span class="double-underline">
                            {{ number_format($netTotal, 2) }}
                        </span>
                    </strong>
                </td>

                <td class="text-right text-green">
                    <strong>
                        <span class="double-underline">
                            {{ number_format($totalPaid, 2) }}
                        </span>
                    </strong>
                </td>

                <td class="text-right text-red">
                    <strong>
                        <span class="double-underline">
                            {{ number_format($totalDue, 2) }}
                        </span>
                    </strong>
                </td>

            </tr>

        </tfoot>

    </table>

</body>

</html>