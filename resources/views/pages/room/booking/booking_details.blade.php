<x-default-layout>

<style>
    body {
        font-size: 13px;
        background: #f5f7fa;
    }

    .invoice-box {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .header {
        border-bottom: 2px solid #eee;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .header h3 {
        margin: 0;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .meta {
        font-size: 13px;
        color: #555;
    }

    .section-title {
        font-weight: 600;
        font-size: 14px;
        margin-top: 25px;
        margin-bottom: 10px;
        color: #333;
        border-left: 4px solid #0d6efd;
        padding-left: 8px;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #f1f3f5;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
    }

    .table td {
        vertical-align: middle;
    }

    .text-end { text-align: right; }

    .summary-box {
        width: 350px;
        margin-left: auto;
        margin-top: 20px;
    }

    .summary-box table td {
        padding: 6px 10px;
    }

    .total-row {
        font-size: 15px;
        font-weight: bold;
        background: #f8f9fa;
    }

    .paid {
        color: #198754;
        font-weight: 600;
    }

    .due {
        color: #dc3545;
        font-weight: 600;
    }

    .badge-soft {
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 11px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .footer {
        margin-top: 30px;
        text-align: center;
        font-size: 11px;
        color: #888;
    }
</style>

<div class="container-fluid mt-4">
<div class="invoice-box">

    {{-- HEADER --}}
    <div class="header d-flex justify-content-between">
        <div>
            <h3>BOOKING INVOICE</h3>
            <div class="meta">
                Booking No: <b>{{ $invoiceSummary['invoice'] }}</b><br>
                Date: {{ \Carbon\Carbon::parse($invoiceSummary['date'])->format('d M Y') }}
            </div>
        </div>

        <div class="text-end meta">
            <b>Customer Info</b><br>
            {{ $invoiceSummary['customer_name'] ?? 'N/A' }}<br>
            {{ $invoiceSummary['customer_mobile'] ?? '' }}
        </div>
    </div>

    {{-- ROOM DETAILS --}}
    <div class="section-title">Room Details</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Room</th>
                <th class="text-center">Check In</th>
                <th class="text-center">Check Out</th>
                <th class="text-center">Days</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>

        <tbody>
        @php $roomSubTotal = 0; @endphp

        @foreach($bookings as $booking)
            @php
                if ($booking->total_days == 0) $booking->total_days = 1;
                $roomSubTotal += $booking->total_amount;
            @endphp

            <tr>
                <td>
                    <b>{{ $booking->room->room_number ?? 'Room '.$booking->room_id }}</b>
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_in_date.' '.$booking->check_in_datetime)->format('d M Y h:i A') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_out_date.' '.$booking->check_out_datetime)->format('d M Y h:i A') }}</td>
                <td class="text-center">
                    <span class="badge-soft">{{ $booking->total_days }}</span>
                </td>
                <td class="text-end">
                    {{ number_format($booking->room->price_per_night ?? 0, 2) }}
                </td>
                <td class="text-end">
                    <b>{{ number_format($booking->total_amount, 2) }}</b>
                </td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">Room Total</td>
                <td class="text-end">{{ number_format($roomSubTotal,2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- SUMMARY --}}
    <div class="summary-box">
        <table class="table table-bordered">
            <tr>
                <td>Sub Total</td>
                <td class="text-end">{{ number_format($roomSubTotal,2) }}</td>
            </tr>

            <tr>
                <td>Discount</td>
                <td class="text-end text-danger">
                    -{{ number_format($invoiceSummary['discount_amount'],2) }}
                </td>
            </tr>

            <tr class="total-row">
                <td>Net Total</td>
                <td class="text-end">
                    {{ number_format($roomSubTotal - $invoiceSummary['discount_amount'],2) }}
                </td>
            </tr>

            <tr>
                <td class="paid">Paid</td>
                <td class="text-end paid">
                    {{ number_format($paymentDetails->sum('amount'),2) }}
                </td>
            </tr>

            <tr>
                <td class="due">Due</td>
                <td class="text-end due">
                    {{ number_format(($roomSubTotal - $invoiceSummary['discount_amount']) - $paymentDetails->sum('amount'),2) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- GUEST SECTION --}}
    @if($guests->count())
    <div class="section-title">Guest Information</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>NID</th>
                <th>Address</th>
                <th>Relation</th>
            </tr>
        </thead>

        <tbody>
            @foreach($guests as $key => $guest)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td><b>{{ $guest->name }}</b></td>
                    <td>{{ $guest->mobile }}</td>
                    <td>{{ $guest->nid }}</td>
                    <td>{{ $guest->address }}</td>
                    <td>
                        <span class="badge-soft">{{ $guest->relation }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        This is a system generated invoice • Thank you for your business
    </div>

</div>
</div>

</x-default-layout>