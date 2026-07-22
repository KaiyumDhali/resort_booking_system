@php
    $imagePath    = public_path(Storage::url($data['company_logo_one']));
    $imageDataUri = 'data:' . mime_content_type($imagePath) . ';base64,' . base64_encode(file_get_contents($imagePath));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $data['company_name'] }} — Spot Booking Report</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N"
          crossorigin="anonymous">
    <style>
        @page {
            margin: 8px;
            size: A4 portrait;
        }
        body { margin: 0; padding: 0; font-size: 9px; }

        .double-underline { border-bottom: 3px double; }
        .text-green  { color: #198754; font-weight: 700; }
        .text-red    { color: #dc3545; font-weight: 700; }
        .text-orange { color: #fd7e14; }

        .filter-section {
            background: #f0f4ff;
            border: 1px solid #c7d7f9;
            border-radius: 4px;
            padding: 4px 8px;
            margin-bottom: 6px;
            font-size: 8.5px;
            color: #444;
        }
        .filter-section strong { color: #0d6efd; margin-right: 3px; }
        .filter-item {
            display: inline-block;
            background: #fff;
            border: 1px solid #c7d7f9;
            border-radius: 2px;
            padding: 1px 5px;
            margin: 1px 2px 1px 0;
            color: #333;
        }

        .inv-no { font-weight: 700; color: #0d6efd; }

        table.main-table {
            width: 100%;
            table-layout: fixed;
            font-size: 8.5px;
        }
        table.main-table th,
        table.main-table td {
            padding: 3px 4px !important;
            overflow: hidden;
            word-break: break-word;
        }
        table.main-table thead th {
            font-size: 8px;
        }

        /* Column widths — total ~100% for A4 portrait */
        .c-sl   { width: 3%; }
        .c-inv  { width: 8%; }
        .c-date { width: 8%; }
        .c-cust { width: 14%; }
        .c-mob  { width: 10%; }
        .c-per  { width: 4%; }
        .c-amt  { width: 8%; }
        .c-pkg  { width: 8%; }
        .c-svc  { width: 8%; }
        .c-disc { width: 4%; }
        .c-gt   { width: 8%; }
        .c-paid { width: 8%; }
        .c-due  { width: 9%; }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <table style="width:100%;margin-bottom:4px;border:none;border-collapse:collapse">
        <tr>
            <td style="width:70px">
                <img src="{{ $imageDataUri }}" alt="{{ $data['company_name'] }}" height="48"/>
            </td>
            <td style="text-align:center">
                <div style="font-size:14px;font-weight:700">{{ $data['company_name'] }}</div>
                <div style="font-size:11px;font-weight:600">Spot-wise Booking Report</div>
                <div style="font-size:9px">Date: {{ $data['start_date'] }} to {{ $data['end_date'] }}</div>
            </td>
            <td style="width:70px"></td>
        </tr>
    </table>

    {{-- ── ACTIVE FILTERS ── --}}
    @php
        $appliedFilters = array_filter([
            'Invoice'    => request('invoice'),
            'Customer'   => request('customer_id') ?: null,
            'Mobile'     => request('customer_mobile'),
            'NID'        => request('nid_number'),
            'Spot'       => request('spot_id') ? ($spots->firstWhere('id', request('spot_id'))?->title ?? null) : null,
            'Start Date' => request('start_date'),
            'End Date'   => request('end_date'),
        ]);
    @endphp

    @if(count($appliedFilters))
    <div class="filter-section">
        <strong>Filters:</strong>
        @foreach($appliedFilters as $label => $val)
            <span class="filter-item"><b>{{ $label }}:</b> {{ $val }}</span>
        @endforeach
    </div>
    @endif

    {{-- ── SUMMARY ROW ── --}}
    @php
        $totalGrand = $report->sum('grand_total');
        $totalPaid  = $report->sum('paid_amount');
        $totalDue   = $report->sum(fn($r) => $r->grand_total - $r->paid_amount);
        $totalDisc  = $report->sum(fn($r) => $r->spot_total * $r->spot_discount_percent / 100);
    @endphp
    <table style="width:100%;margin-bottom:5px;font-size:9px;border-collapse:collapse">
        <tr>
            <td style="padding:2px 6px"><b>Bookings:</b> {{ $report->count() }}</td>
            <td style="padding:2px 6px"><b>Grand Total:</b> ৳{{ number_format($totalGrand, 2) }}</td>
            <td style="padding:2px 6px"><b>Discount:</b> <span class="text-orange">৳{{ number_format($totalDisc, 2) }}</span></td>
            <td style="padding:2px 6px"><b>Paid:</b> <span class="text-green">৳{{ number_format($totalPaid, 2) }}</span></td>
            <td style="padding:2px 6px"><b>Due:</b> <span class="text-red">৳{{ number_format($totalDue, 2) }}</span></td>
        </tr>
    </table>

    {{-- ── TABLE ── --}}
    <table class="table table-bordered table-striped table-sm main-table">
        @if($report->count())
        <thead class="text-light bg-secondary">
            <tr class="text-uppercase text-center">
                <th class="c-sl">SL</th>
                <th class="c-inv">Invoice</th>
                <th class="c-date">Date</th>
                <th class="c-cust">Customer</th>
                <th class="c-mob">Mobile</th>
                <th class="c-per">Pax</th>
                <th class="c-amt">Spot</th>
                <th class="c-pkg">Package</th>
                <th class="c-svc">Service</th>
                <th class="c-disc">Disc%</th>
                <th class="c-gt">Grand</th>
                <th class="c-paid">Paid</th>
                <th class="c-due">Due</th>
            </tr>
        </thead>
        <tbody>
            @php $gTotal=$gPaid=$gDue=$gSpot=$gPkg=$gSvc = 0; @endphp
            @foreach($report as $i => $r)
                @php
                    $due    = $r->grand_total - $r->paid_amount;
                    $gSpot += $r->spot_total;
                    $gPkg  += $r->package_total;
                    $gSvc  += $r->service_total;
                    $gTotal+= $r->grand_total;
                    $gPaid += $r->paid_amount;
                    $gDue  += $due;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td><span class="inv-no">{{ $r->invoice_number }}</span></td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($r->booking_date)->format('d/m/Y') }}</td>
                    <td>{{ $r->customer_name ?? '—' }}</td>
                    <td>{{ $r->customer_mobile ?? '—' }}</td>
                    <td class="text-center">{{ $r->total_persons }}</td>
                    <td class="text-right">{{ number_format($r->spot_total, 2) }}</td>
                    <td class="text-right">{{ $r->package_total > 0 ? number_format($r->package_total, 2) : '—' }}</td>
                    <td class="text-right">{{ $r->service_total > 0 ? number_format($r->service_total, 2) : '—' }}</td>
                    <td class="text-center {{ $r->spot_discount_percent > 0 ? 'text-orange' : '' }}">
                        {{ $r->spot_discount_percent > 0 ? $r->spot_discount_percent.'%' : '—' }}
                    </td>
                    <td class="text-right">{{ number_format($r->grand_total, 2) }}</td>
                    <td class="text-right text-green">{{ number_format($r->paid_amount, 2) }}</td>
                    <td class="text-right {{ $due > 0 ? 'text-red' : 'text-green' }}">
                        {{ $due > 0 ? number_format($due, 2) : '—' }}
                    </td>
                </tr>
            @endforeach

            <tr class="text-light bg-secondary">
                <td class="text-right" colspan="6"><strong>Total:</strong></td>
                <td class="text-right"><strong><span class="double-underline">{{ number_format($gSpot, 2) }}</span></strong></td>
                <td class="text-right"><strong><span class="double-underline">{{ number_format($gPkg, 2) }}</span></strong></td>
                <td class="text-right"><strong><span class="double-underline">{{ number_format($gSvc, 2) }}</span></strong></td>
                <td></td>
                <td class="text-right"><strong><span class="double-underline">{{ number_format($gTotal, 2) }}</span></strong></td>
                <td class="text-right"><strong><span class="double-underline text-green">{{ number_format($gPaid, 2) }}</span></strong></td>
                <td class="text-right"><strong><span class="double-underline text-red">{{ number_format($gDue, 2) }}</span></strong></td>
            </tr>
        </tbody>
        @else
        <tr>
            <td colspan="13" class="text-center py-2 text-muted">No bookings found.</td>
        </tr>
        @endif
    </table>

</body>
</html>