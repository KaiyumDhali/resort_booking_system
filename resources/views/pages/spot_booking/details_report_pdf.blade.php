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

        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .report-header h2 { margin: 3px 0; font-size: 20px; }
        .report-header h4 { margin: 0; color: #555; }

        .filter-section {
            border: 1px solid #dcdcdc;
            background: #fafafa;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 10px;
        }

        .filter-item { display: inline-block; margin-right: 12px; }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary-table td {
            border: 1px solid #dcdcdc;
            padding: 5px 4px;
            text-align: center;
            background: #fafafa;
        }

        .summary-table .s-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #888;
            display: block;
        }

        .summary-table .s-value {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a2e;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .report-table th {
            background: #f2f2f2;
            border: 1px solid #cfcfcf;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }

        .report-table td {
            border: 1px solid #d9d9d9;
            padding: 4px;
            vertical-align: middle;
        }

        /* ── Grouped rows (mirrors the web report) ── */
        .row-date-header td {
            background: #2c2c2a;
            color: #d3d1c7;
            font-weight: bold;
            padding: 4px 8px;
        }

        .row-invoice-header td {
            background: #E6F1FB;
            color: #0C447C;
            font-weight: bold;
        }

        .row-inv-total td {
            background: #EAF3DE;
            font-weight: bold;
        }

        .row-date-total td {
            background: #FAEEDA;
            font-weight: bold;
            color: #633806;
        }

        .row-grand-total td {
            background: #D3D1C7;
            font-weight: bold;
            color: #2C2C2A;
        }

        .comp-tag {
            display: inline-block;
            font-size: 8px;
            padding: 1px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
        .comp-spot    { background: #eef2ff; color: #4f46e5; }
        .comp-package { background: #fef3e7; color: #b45309; }
        .comp-service { background: #eafaf1; color: #0a7d4c; }
        .comp-room    { background: #fde8ee; color: #b0245b; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-paid       { background: #d1e7dd; color: #0a3622; }
        .badge-partial    { background: #fff3cd; color: #664d03; }
        .badge-unpaid     { background: #f8d7da; color: #58151c; }
        .badge-approved   { background: #d1e7dd; color: #0a3622; }
        .badge-pending    { background: #fff3cd; color: #664d03; }
        .badge-cancelled  { background: #f8d7da; color: #58151c; }

        .dim { color: #999; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer-table { width: 100%; font-size: 10px; }
        .footer-table td { border: none; }
    </style>

</head>

<body style="font-size:12px">
    <div class="report-header">
        <img src="{{ $imageDataUri }}" height="55">
        <h2>{{ $data['company_name'] }}</h2>
        <h4>Spot Bookings Details Report</h4>
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
            <td style="width:14.28%"><span class="s-label">Bookings</span><span class="s-value">{{ $summary['total_bookings'] }}</span></td>
            <td style="width:14.28%"><span class="s-label">Amount</span><span class="s-value">{{ number_format($summary['total_amount'], 0) }}</span></td>
            <td style="width:14.28%"><span class="s-label">Discount</span><span class="s-value" style="color:#A32D2D;">{{ number_format($summary['total_discount'], 0) }}</span></td>
            <td style="width:14.28%"><span class="s-label">Net Total</span><span class="s-value">{{ number_format($summary['net_total'], 0) }}</span></td>
            <td style="width:14.28%"><span class="s-label">Paid</span><span class="s-value" style="color:#185FA5;">{{ number_format($summary['total_paid'], 0) }}</span></td>
            <td style="width:14.28%"><span class="s-label">Due</span><span class="s-value" style="color:#A32D2D;">{{ number_format($summary['total_due'], 0) }}</span></td>
            <td style="width:14.28%"><span class="s-label">Refundable</span><span class="s-value" style="color:#854F0B;">{{ number_format($summary['total_refundable'], 0) }}</span></td>
        </tr>
    </table>

    {{-- ── GROUPED TABLE (Date → Invoice → Component → totals) ── --}}
    <table class="report-table">
        @if ($bookings->count())
            <thead>
                <tr class="text-uppercase text-center">
                    <th style="width:8%">Invoice No</th>
                    <th style="width:17%" class="text-left">Customer / Component</th>
                    <th style="width:8%">Booking Date</th>
                    <th style="width:6%">Persons</th>
                    <th style="width:9%">Amount</th>
                    <th style="width:8%">Discount</th>
                    <th style="width:8%">Net</th>
                    <th style="width:8%">Paid</th>
                    <th style="width:8%">Due</th>
                    <th style="width:9%">Refundable</th>
                    <th style="width:7%">Payment</th>
                    <th style="width:7%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = $bookings->groupBy(function ($item) {
                        return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
                    });

                    $grandAmount     = 0;
                    $grandDiscount   = 0;
                    $grandNet        = 0;
                    $grandPaid       = 0;
                    $grandDue        = 0;
                    $grandRefundable = 0;
                @endphp

                @foreach($grouped as $date => $dateRows)

                    <tr class="row-date-header">
                        <td colspan="12">Date : {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
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

                        <tr class="row-invoice-header">
                            <td><strong>{{ $booking->invoice_number }}</strong></td>
                            <td><strong>{{ $booking->customer_name ?? '—' }}</strong></td>
                            <td colspan="10" class="dim" style="font-size:8px;">
                                {{ $components->count() }} component(s)
                            </td>
                        </tr>

                        @forelse($components as $comp)
                            <tr>
                                <td class="dim text-center">—</td>
                                <td style="padding-left:12px;">
                                    <span class="comp-tag {{ $comp['class'] }}">{{ $comp['label'] }}</span>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                <td class="text-center dim">—</td>
                                <td class="text-right"><strong>{{ number_format($comp['amount'], 2) }}</strong></td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-center dim">—</td>
                                <td class="text-center dim">—</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="dim text-center">—</td>
                                <td colspan="11" class="dim" style="padding-left:12px;">No components recorded</td>
                            </tr>
                        @endforelse

                        <tr class="row-inv-total">
                            <td colspan="3" class="text-right" style="color:#27500A;">Invoice Total :</td>
                            <td class="text-center">{{ $booking->total_persons }}</td>
                            <td class="text-right">{{ number_format($bkAmount, 2) }}</td>
                            <td class="text-right" style="color:#A32D2D;">{{ $bkDiscount > 0 ? number_format($bkDiscount, 2) : '—' }}</td>
                            <td class="text-right">{{ number_format($bkNet, 2) }}</td>
                            <td class="text-right" style="color:#185FA5;">{{ number_format($bkPaid, 2) }}</td>
                            <td class="text-right" style="color:#854F0B;">{{ $bkDue > 0 ? number_format($bkDue, 2) : '—' }}</td>
                            <td class="text-right" style="color:#854F0B;">{{ $bkRefundable > 0 ? number_format($bkRefundable, 2) : '—' }}</td>
                            <td class="text-center"><span class="badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span></td>
                            <td class="text-center"><span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span></td>
                        </tr>

                        @php
                            $dateAmount     += $bkAmount;
                            $dateDiscount   += $bkDiscount;
                            $dateNet        += $bkNet;
                            $datePaid       += $bkPaid;
                            $dateDue        += $bkDue;
                            $dateRefundable += $bkRefundable;
                        @endphp

                    @endforeach

                    <tr class="row-date-total">
                        <td colspan="4" class="text-right">Date Total :</td>
                        <td class="text-right">{{ number_format($dateAmount, 2) }}</td>
                        <td class="text-right">{{ $dateDiscount > 0 ? number_format($dateDiscount, 2) : '—' }}</td>
                        <td class="text-right">{{ number_format($dateNet, 2) }}</td>
                        <td class="text-right">{{ number_format($datePaid, 2) }}</td>
                        <td class="text-right">{{ $dateDue > 0 ? number_format($dateDue, 2) : '—' }}</td>
                        <td class="text-right">{{ $dateRefundable > 0 ? number_format($dateRefundable, 2) : '—' }}</td>
                        <td>—</td>
                        <td>—</td>
                    </tr>

                    @php
                        $grandAmount     += $dateAmount;
                        $grandDiscount   += $dateDiscount;
                        $grandNet        += $dateNet;
                        $grandPaid       += $datePaid;
                        $grandDue        += $dateDue;
                        $grandRefundable += $dateRefundable;
                    @endphp

                @endforeach

                <tr class="row-grand-total">
                    <td colspan="4" class="text-right">Grand Total :</td>
                    <td class="text-right">{{ number_format($grandAmount, 2) }}</td>
                    <td class="text-right">{{ $grandDiscount > 0 ? number_format($grandDiscount, 2) : '—' }}</td>
                    <td class="text-right">{{ number_format($grandNet, 2) }}</td>
                    <td class="text-right">{{ number_format($grandPaid, 2) }}</td>
                    <td class="text-right">{{ $grandDue > 0 ? number_format($grandDue, 2) : '—' }}</td>
                    <td class="text-right">{{ $grandRefundable > 0 ? number_format($grandRefundable, 2) : '—' }}</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="12">No spot bookings found for the selected filters !!!</td>
            </tr>
        @endif
    </table>

    <table class="footer-table" style="border: none; border-top: none; position: fixed; bottom: 60px; width: 100%;">
        <tr>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">Checked by</td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">Verified by</td>
            <td style="border: none;"></td>
            <td style="border-top: 1px solid #000000; font-weight: bold; text-align:center;">Approved by</td>
        </tr>
    </table>

</body>

</html>