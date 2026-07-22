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
            font-size: 10px;
        }

        .filter-item {
            display: inline-block;
            margin-right: 12px;
        }

        /* ── Summary strip ── */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary-table td {
            border: 1px solid #dcdcdc;
            padding: 6px 8px;
            text-align: center;
            background: #fafafa;
        }

        .summary-table .s-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #888;
            display: block;
        }

        .summary-table .s-value {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
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

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-approved  { background: #d1e7dd; color: #0a3622; }
        .badge-pending   { background: #fff3cd; color: #664d03; }
        .badge-cancelled { background: #f8d7da; color: #58151c; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .dim { color: #999; }

        /* Total Row */
        .total-row {
            background: #f5f5f5;
            font-weight: bold;
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

        <h4>Spot Bookings Report</h4>
        <div style="font-size:11px;color:#555;margin-top:2px;">
            Created Date: {{ $data['start_date'] }} &ndash; {{ $data['end_date'] }}
        </div>
    </div>

    {{-- ── ACTIVE FILTERS ── --}}
    @php
        $appliedFilters = array_filter([
            'Invoice'           => request('invoice'),
            'Mobile'            => request('customer_mobile'),
            'NID'               => request('nid'),
            'Created Date From' => request('date_from'),
            'Created Date To'   => request('date_to'),
            'Status'            => match(request('status')) { '0'=>'Pending','1'=>'Approved','2'=>'Cancelled', default=>null },
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

    {{-- ── SUMMARY STRIP ── --}}
    <table class="summary-table">
        <tr>
            <td style="width:16.66%">
                <span class="s-label">Bookings</span>
                <span class="s-value">{{ $summary['total_bookings'] }}</span>
            </td>
            <td style="width:16.66%">
                <span class="s-label">Total Amount</span>
                <span class="s-value">{{ number_format($summary['total_amount'], 2) }}</span>
            </td>
            <td style="width:16.66%">
                <span class="s-label">Discount</span>
                <span class="s-value" style="color:#A32D2D;">{{ number_format($summary['total_discount'], 2) }}</span>
            </td>
            <td style="width:16.66%">
                <span class="s-label">Net Total</span>
                <span class="s-value">{{ number_format($summary['net_total'], 2) }}</span>
            </td>
            <td style="width:16.66%">
                <span class="s-label">Paid</span>
                <span class="s-value" style="color:#185FA5;">{{ number_format($summary['total_paid'], 2) }}</span>
            </td>
            <td style="width:16.66%">
                <span class="s-label">Refundable</span>
                <span class="s-value" style="color:#854F0B;">{{ number_format($summary['total_refundable'], 2) }}</span>
            </td>
        </tr>
    </table>

    <table class="report-table">
        @if ($bookings->count())
            <thead>
                <tr class="text-uppercase text-center">
                    <th style="width:3%">SL</th>
                    <th style="width:9%">Invoice</th>
                    <th style="width:7%">Date</th>
                    <th style="width:15%">Customer</th>
                    <th style="width:6%">Persons</th>
                    <th style="width:9%">Amount</th>
                    <th style="width:8%">Discount</th>
                    <th style="width:9%">Net Total</th>
                    <th style="width:9%">Paid</th>
                    <th style="width:9%">Refundable</th>
                    <th style="width:7%">Days Left</th>
                    <th style="width:9%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $key => $booking)
                    @php
                        $statusBadge = match((int) $booking->status) {
                            1       => ['class' => 'badge-approved',  'label' => 'Approved'],
                            2       => ['class' => 'badge-cancelled', 'label' => 'Cancelled'],
                            default => ['class' => 'badge-pending',   'label' => 'Pending'],
                        };
                        $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                        $daysLeft    = \Carbon\Carbon::today()->lte($bookingDate)
                            ? $bookingDate->diffInDays(\Carbon\Carbon::today())
                            : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>{{ $booking->invoice_number }}</strong></td>
                        <td class="text-center">{{ $bookingDate->format('d M Y') }}</td>
                        <td>
                            {{ $booking->customer_name ?? '—' }}
                            @if($booking->customer_mobile)
                                <br><small class="dim">{{ $booking->customer_mobile }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $booking->total_persons }}</td>
                        <td class="text-right">{{ number_format($booking->invoice_amount + $booking->manual_discount_amount, 2) }}</td>
                        <td class="text-right">{{ $booking->manual_discount_amount > 0 ? number_format($booking->manual_discount_amount, 2) : '—' }}</td>
                        <td class="text-right"><strong>{{ number_format($booking->invoice_amount, 2) }}</strong></td>
                        <td class="text-right">{{ number_format($booking->paid_amount, 2) }}</td>
                        <td class="text-right">{{ $booking->refundable_amount > 0 ? number_format($booking->refundable_amount, 2) : '—' }}</td>
                        <td class="text-center">{{ $daysLeft }}</td>
                        <td class="text-center">
                            <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($summary['total_amount'], 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($summary['total_discount'], 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($summary['net_total'], 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($summary['total_paid'], 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($summary['total_refundable'], 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="12">No bookings found for the selected filters !!!</td>
            </tr>
        @endif
    </table>

    <table class="footer-table" style="border: none; border-top: none; position: fixed; bottom: 60px; width: 100%;">
        <tr>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">
                Checked by
            </td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">
                Verified by
            </td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">
                Approved by
            </td>
        </tr>
    </table>

</body>

</html>