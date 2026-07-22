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

        /* ── Grouped-report row styles (mirrors the web report) ── */
        .row-date-header td {
            background: #2c2c2a;
            color: #d3d1c7;
            font-weight: bold;
            padding: 5px 8px;
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

        .room-tag {
            display: inline-block;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-paid     { background: #d1e7dd; color: #0a3622; }
        .badge-partial  { background: #fff3cd; color: #664d03; }
        .badge-unpaid   { background: #f8d7da; color: #58151c; }
        .badge-checkin  { background: #cfe2ff; color: #084298; }
        .badge-checkout { background: #e2e3e5; color: #41464b; }
        .badge-cancel   { background: #f8d7da; color: #58151c; }

        .dim { color: #999; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

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
        <div style="font-size:11px;color:#555;margin-top:2px;">
            Booking Date: {{ $data['start_date'] }} &ndash; {{ $data['end_date'] }}
        </div>
    </div>

    {{-- ── ACTIVE FILTERS ── --}}
    @php
        $appliedFilters = array_filter([
            'Customer'          => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
            'Mobile'            => request('customer_mobile'),
            'NID'               => request('nid_number'),
            'Booking Date From' => request('date_from'),
            'Booking Date To'   => request('date_to'),
            'Room'              => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))?->room_number ?? null) : null,
            'Payment'           => request('pay_status'),
            'Status'            => match(request('booking_status')) { '1'=>'Checked In','2'=>'Checked Out','3'=>'Cancelled', default=>null },
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

    {{-- ── GROUPED TABLE (mirrors the web report: date → booking → room rows → totals) ── --}}
    <table class="report-table">
        @if ($bookings->count())
            <thead>
                <tr class="text-uppercase text-center">
                    <th style="width:8%">Booking No</th>
                    <th style="width:20%" class="text-left">Customer / Room</th>
                    <th style="width:9%">Check In</th>
                    <th style="width:9%">Check Out</th>
                    <th style="width:5%">Days</th>
                    <th style="width:10%">Amount</th>
                    <th style="width:9%">Discount</th>
                    <th style="width:9%">Net</th>
                    <th style="width:9%">Paid</th>
                    <th style="width:9%">Due</th>
                    <th style="width:8%">Payment</th>
                    
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = collect($bookings)->groupBy(function ($item) {
                        return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
                    });

                    $grandAmount   = 0;
                    $grandDiscount = 0;
                    $grandNet      = 0;
                    $grandPaid     = 0;
                    $grandDue      = 0;
                @endphp

                @foreach($grouped as $date => $dateRows)

                    {{-- DATE HEADER --}}
                    <tr class="row-date-header">
                        <td colspan="11">Date : {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
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
                            <td colspan="9" class="dim" style="font-size:9px;">
                                {{ $rows->count() }} room(s)
                            </td>
                        </tr>

                        {{-- ROOM ROWS --}}
                        @foreach($rows as $row)
                            <tr>
                                <td class="dim text-center" style="font-size:9px;">—</td>
                                <td style="padding-left:14px;">
                                    <span class="room-tag">{{ $row->room->room_number ?? 'Room '.$row->room_id }}</span>
                                </td>
                                <td class="text-center">{{ $row->check_in_date ? \Carbon\Carbon::parse($row->check_in_date)->format('d M Y') : '—' }}</td>
                                <td class="text-center">{{ $row->check_out_date ? \Carbon\Carbon::parse($row->check_out_date)->format('d M Y') : '—' }}</td>
                                <td class="text-center">{{ $row->total_days ?: 1 }}</td>
                                <td class="text-right"><strong>{{ number_format($row->total_amount, 2) }}</strong></td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                                <td class="text-right dim">—</td>
                            </tr>
                        @endforeach

                        {{-- BOOKING TOTAL --}}
                        <tr class="row-inv-total">
                            <td colspan="5" class="text-right" style="color:#27500A;">Booking Total :</td>
                            <td class="text-right">{{ number_format($bkAmount, 2) }}</td>
                            <td class="text-right" style="color:#A32D2D;">{{ $bkDiscount > 0 ? number_format($bkDiscount, 2) : '—' }}</td>
                            <td class="text-right">{{ number_format($bkNet, 2) }}</td>
                            <td class="text-right" style="color:#185FA5;">{{ number_format($bkPaid, 2) }}</td>
                            <td class="text-right" style="color:#854F0B;">{{ $bkDue > 0 ? number_format($bkDue, 2) : '—' }}</td>
                            <td class="text-center">
                                @if($isInv)
                                    <span class="badge badge-paid">Spot Booking</span>
                                @else
                                    <span class="badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
                                @endif
                            </td>
                            <!-- <td class="text-center">
                                <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                            </td> -->
                        </tr>

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
                        <td colspan="5" class="text-right">Date Total :</td>
                        <td class="text-right">{{ number_format($dateAmount, 2) }}</td>
                        <td class="text-right">{{ $dateDiscount > 0 ? number_format($dateDiscount, 2) : '—' }}</td>
                        <td class="text-right">{{ number_format($dateNet, 2) }}</td>
                        <td class="text-right">{{ number_format($datePaid, 2) }}</td>
                        <td class="text-right">{{ $dateDue > 0 ? number_format($dateDue, 2) : '—' }}</td>
                        <td>—</td>
                    </tr>

                    @php
                        $grandAmount   += $dateAmount;
                        $grandDiscount += $dateDiscount;
                        $grandNet      += $dateNet;
                        $grandPaid     += $datePaid;
                        $grandDue      += $dateDue;
                    @endphp

                @endforeach

                {{-- GRAND TOTAL --}}
                <tr class="row-grand-total">
                    <td colspan="5" class="text-right">Grand Total :</td>
                    <td class="text-right">{{ number_format($grandAmount, 2) }}</td>
                    <td class="text-right">{{ $grandDiscount > 0 ? number_format($grandDiscount, 2) : '—' }}</td>
                    <td class="text-right">{{ number_format($grandNet, 2) }}</td>
                    <td class="text-right">{{ number_format($grandPaid, 2) }}</td>
                    <td class="text-right">{{ $grandDue > 0 ? number_format($grandDue, 2) : '—' }}</td>
                    <td>—</td>
                </tr>
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="11">No bookings found for the selected filters !!!</td>
            </tr>
        @endif
    </table>

    <table class="footer-table" style="border: none; border-top: none; position: fixed; bottom: 80px; width: 100%;">
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