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
        .double-underline {
            border-bottom: 4px double;
        }

        .pagenum:before {
            content: counter(page);
        }

        .room-tag {
            background-color: #eef2ff;
            color: #4f46e5;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 700;
        }

        .text-green  { color: #198754; font-weight: 700; }
        .text-red    { color: #dc3545; font-weight: 700; }
        .text-orange { color: #fd7e14; }

        .filter-section {
            background: #f0f4ff;
            border: 1px solid #c7d7f9;
            border-radius: 5px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 10px;
            color: #444;
        }

        .filter-section strong {
            color: #0d6efd;
            margin-right: 4px;
        }

        .filter-item {
            display: inline-block;
            background: #fff;
            border: 1px solid #c7d7f9;
            border-radius: 3px;
            padding: 1px 7px;
            margin: 2px 3px 2px 0;
            color: #333;
        }
        @page {
    margin: 12px 12px 12px 12px;
}

body {
    margin: 0;
    padding: 0;
}
    </style>

</head>

<body style="font-size:12px">
    <table class="table table-borderless table-sm m-0">
        <tbody>
            <tr class="align-middle">
                <td style="width: 100px;" class="text-left">
                    <img src="{{ $imageDataUri }}" alt="{{ $data['company_name'] }}" height="60" />
                </td>
                <td class="text-center">
                    <h3 class="mb-0">{{ $data['company_name'] }}</h3>
                    <h6 class="mb-0">Booking Summary Report</h6>
                    <h6 class="mb-0">Date: {{ $data['start_date'] }} to {{ $data['end_date'] }}</h6>
                </td>
                <td style="width: 100px;"></td>
            </tr>
        </tbody>
    </table>

    {{-- ── ACTIVE FILTERS ── --}}
    @php
        $appliedFilters = array_filter([
            'Invoice'        => request('invoice'),
            'Customer'       => request('customer_id') ? ($customers->firstWhere('id', request('customer_id'))?->customer_name ?? null) : null,
            'Mobile'         => request('customer_mobile'),
            'NID'            => request('nid_number'),
            'Room'           => request('room_id') ? ($rooms->firstWhere('id', request('room_id'))?->room_name ?? null) : null,
            'Start Date'     => request('start_date'),
            'End Date'       => request('end_date'),
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

    <table class="table table-bordered table-striped table-sm table-hover pt-0">
        @if ($report->count())
            <thead class="text-light bg-secondary">
                <tr class="text-uppercase text-center">
                    <th scope="col">SL</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Room</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
                    <th scope="col">Days</th>
                    <th scope="col">Total</th>
                    <th scope="col">Discount</th>
                    <th scope="col">Paid</th>
                    <th scope="col">Due</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount   = 0;
                    $totalDiscount = 0;
                    $totalPaid     = 0;
                    $totalDue      = 0;
                @endphp
                @foreach ($report as $key => $r)
                    @php
                        $totalAmount   += $r->total_amount;
                        $totalDiscount += $r->discount;
                        $totalPaid     += $r->paid_amount;
                        $totalDue      += $r->due_amount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>{{ $r->booking_no }}</strong></td>
                        <td>{{ $r->customer_name ?? '—' }}</td>
                        <td>{{ $r->customer_mobile ?? '—' }}</td>
                        <td class="text-center"><span class="room-tag">{{ $r->room_number ?? '—' }}</span></td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($r->check_in_date)->format('d M Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($r->check_out_date)->format('d M Y') }}</td>
                        <td class="text-center">{{ $r->total_days }}</td>
                        <td class="text-right">{{ number_format($r->total_amount, 2) }}</td>
                        <td class="text-right text-orange">{{ $r->discount > 0 ? number_format($r->discount, 2) : '—' }}</td>
                        <td class="text-right text-green">{{ number_format($r->paid_amount, 2) }}</td>
                        <td class="text-right {{ $r->due_amount > 0 ? 'text-red' : 'text-green' }}">{{ $r->due_amount > 0 ? number_format($r->due_amount, 2) : '—' }}</td>
                    </tr>
                @endforeach

                <tr class="text-light bg-secondary">
                    <td class="text-right" colspan="8"><strong>Total:</strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalAmount, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalDiscount, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalPaid, 2) }}</span></strong></td>
                    <td class="text-right"><strong><span class="double-underline">{{ number_format($totalDue, 2) }}</span></strong></td>
                </tr>
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="12">No bookings found for the selected filters !!!</td>
            </tr>
        @endif
    </table>

    <!-- <table class="table text-center" style="border: none; border-top: none; position: fixed; bottom: 80px; width: 100%;">
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
    </table> -->

</body>

</html>