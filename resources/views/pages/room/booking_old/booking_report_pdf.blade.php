@php
    $imagePath = public_path(Storage::url($data['company_logo_one']));
    $imageDataUri = 'data:' . mime_content_type($imagePath) . ';base64,' . base64_encode(file_get_contents($imagePath));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $data['company_name'] }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
       body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color: #2c2c2c;
}

/* Header */
.report-header {
    text-align: center;
    margin-bottom: 12px;
}

.report-header h2 {
    margin: 3px 0;
    font-size: 20px;
}

.report-header h4 {
    margin: 0;
    color: #555;
}

/* Filter Box */
.filter-section {
    border: 1px solid #dcdcdc;
    background: #fafafa;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
}

/* Main Table */
.report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
}

.report-table th {
    background: #f2f2f2;
    border: 1px solid #cfcfcf;
    padding: 6px;
    text-align: center;
    font-weight: bold;
}

.report-table td {
    border: 1px solid #d9d9d9;
    padding: 5px;
    vertical-align: middle;
}

.report-table tbody tr:nth-child(even) {
    background: #fcfcfc;
}

/* Total Row */
.total-row {
    background: #f5f5f5;
    font-weight: bold;
}

.double-underline {
    border-bottom: 3px double #000;
}

/* Footer */
.footer-table {
    width: 100%;
    font-size: 10px;
}

.footer-table td {
    border: none;
}
    </style>

</head>

<body style="font-size:12px">
    <div class="report-header">
    <img src="{{ $imageDataUri }}" height="60">

    <h2>{{ $data['company_name'] }}</h2>

    <h4>Booking Details Report</h4>
</div>

    {{-- ── ACTIVE FILTERS ── --}}
    @php
        $appliedFilters = array_filter([
            'Customer'       => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
            'Mobile'         => request('customer_mobile'),
            'NID'            => request('nid_number'),
            'Check-in From'  => request('date_from'),
            'Check-in To'    => request('date_to'),
            'Room'           => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))?->room_number ?? null) : null,
            'Payment'        => request('pay_status'),
            'Status'         => match(request('booking_status')) { '1'=>'Checked In','2'=>'Checked Out','3'=>'Cancelled', default=>null },
        ]);
    @endphp

    @if(count($appliedFilters))
    <div class="filter-section">
        <strong>Filters Applied:</strong>
        @foreach($appliedFilters as $label => $val)
            <span class="filter-item"><b>{{ $label }}:</b> {{ $val }}</span>
        @endforeach
    </div>
    @endif

    <table class="report-table table table-bordered table-striped table-sm table-hover pt-0">
        @if ($bookings->count())
            <thead class="text-light bg-secondary">
                <tr class="text-uppercase text-center">
                    <th scope="col">SL</th>
                    <th scope="col">Booking No</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Room</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
                    <th scope="col">Days</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Disc.</th>
                    <th scope="col">Net</th>
                    <th scope="col">Paid</th>
                    <th scope="col">Due</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = 0;
                    $totalDiscount = 0;
                    $totalNet = 0;
                    $totalPaid = 0;
                    $totalDue = 0;
                @endphp
                @foreach ($bookings as $key => $booking)
                    @php
                        $days        = $booking->total_days ?: 1;
                        $net         = $booking->total_amount - $booking->discount;
                        $paid        = $payments[$booking->booking_no] ?? 0;
                        $due         = max(0, $net - $paid);
                        $payStatus   = $paid >= $net ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                        $payBadge    = match($payStatus) {
                            'paid'    => ['class' => 'badge-paid',    'label' => 'Paid'],
                            'partial' => ['class' => 'badge-partial', 'label' => 'Partial'],
                            default   => ['class' => 'badge-unpaid',  'label' => 'Unpaid'],
                        };
                        $statusBadge = match($booking->Booking_status) {
                            2       => ['class' => 'badge-checkout', 'label' => 'Checked Out'],
                            3       => ['class' => 'badge-cancel',   'label' => 'Cancelled'],
                            default => ['class' => 'badge-checkin',  'label' => 'Checked In'],
                        };

                        $totalAmount   += $booking->total_amount;
                        $totalDiscount += $booking->discount;
                        $totalNet      += $net;
                        $totalPaid     += $paid;
                        $totalDue      += $due;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>{{ $booking->booking_no }}</strong></td>
                        <td>
                            {{ $booking->customer->customer_name ?? '—' }}
                            @if($booking->customer?->customer_mobile)
                                <br><small class="text-muted">{{ $booking->customer->customer_mobile }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="room-tag">{{ $booking->room->room_number ?? 'Room '.$booking->room_id }}</span>
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                        <td class="text-center">{{ $days }}</td>
                        <td class="text-right">{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="text-right">{{ $booking->discount > 0 ? number_format($booking->discount, 2) : '—' }}</td>
                        <td class="text-right"><strong>{{ number_format($net, 2) }}</strong></td>
                        <td class="text-right">{{ number_format($paid, 2) }}</td>
                        <td class="text-right">{{ $due > 0 ? number_format($due, 2) : '—' }}</td>
                        <td class="text-center"><span class="badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span></td>
                        <td class="text-center"><span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span></td>
                    </tr>
                @endforeach

                <tr class="text-light bg-secondary">
                    <td class="text-right" colspan="7"><strong>Total:</strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalAmount, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalDiscount, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalNet, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalPaid, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalDue, 2) }}</span></strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="14">No bookings found for the selected filters !!!</td>
            </tr>
        @endif
    </table>

    
<table class="table text-center" style="border: none; border-top: none; position: fixed; bottom: 80px; width: 100%;">
        <tr>
            <td style="border-top: 1px solid #000000; font-weight: bold;">
                Checked by
            </td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold;">
                Verified by
            </td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold;">
                Approved by
            </td>
        </tr>
    </table>
    
</body>

</html>