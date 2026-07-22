<x-default-layout>

<style>
    body { font-size: 13px; }
    .report-box { padding: 15px; }
    .table th { background: #e9e9e9 !important; }
    .summary-box {
        width: 45%;
        margin-left: auto;
        margin-top: 15px;
    }
    .paid { color: green; font-weight: bold; }
    .due { color: red; font-weight: bold; }
    .section-title {
        margin-top: 20px;
        font-weight: bold;
        font-size: 15px;
    }
</style>

<div class="container-fluid report-box">

    <h4 class="text-center mb-3">BOOKING REPORT</h4>

    {{-- HEADER --}}
    <table class="table table-bordered">
        <tr>
            <td>
                <b>Booking No:</b> {{ $invoiceSummary['invoice'] }} <br>
                <b>Date:</b> {{ \Carbon\Carbon::parse($invoiceSummary['date'])->format('d M Y') }}
            </td>
            <td class="text-right">
                <b>Customer:</b> {{ $invoiceSummary['customer_name'] ?? 'N/A' }} <br>
                <b>Mobile:</b> {{ $invoiceSummary['customer_mobile'] ?? 'N/A' }}
            </td>
        </tr>
    </table>

    {{-- ROOM DETAILS --}}
    <div class="section-title">Room Details</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Room</th>
                <th class="text-center">Check In</th>
                <th class="text-center">Check Out</th>
                <th class="text-center">Days</th>
                <th class="text-right">Price</th>
                <th class="text-right">Amount</th>
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
                <td>{{ $booking->room->room_number ?? 'Room '.$booking->room_id }}</td>
                <td class="text-center">{{ $booking->check_in_date }}</td>
                <td class="text-center">{{ $booking->check_out_date }}</td>
                <td class="text-center">{{ $booking->total_days }}</td>
                <td class="text-right">
                    {{ number_format($booking->room->price_per_night ?? 0, 2) }}
                </td>
                <td class="text-right">
                    {{ number_format($booking->total_amount, 2) }}
                </td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Room Total</th>
                <th class="text-right">{{ number_format($roomSubTotal,2) }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- PAYMENT SUMMARY --}}
    <div class="summary-box">
        <table class="table table-bordered">
            <tr>
                <td>Sub Total</td>
                <td class="text-right">{{ number_format($roomSubTotal,2) }}</td>
            </tr>

            <tr>
                <td>Discount</td>
                <td class="text-right">-{{ number_format($invoiceSummary['discount_amount'],2) }}</td>
            </tr>

            <tr>
                <td>Net Total</td>
                <td class="text-right">
                    {{ number_format($roomSubTotal - $invoiceSummary['discount_amount'],2) }}
                </td>
            </tr>

            <tr>
                <td class="paid">Paid</td>
                <td class="text-right paid">
                    {{ number_format($paymentDetails->sum('amount'),2) }}
                </td>
            </tr>

            <tr>
                <td class="due">Due</td>
                <td class="text-right due">
                    {{ number_format(($roomSubTotal - $invoiceSummary['discount_amount']) - $paymentDetails->sum('amount'),2) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- OPTIONAL: GUEST SECTION HOOK --}}
    @if($guests->count())
    <div class="section-title">Guest Information</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Guest Name</th>
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
                    <td>{{ $guest->name ?? '' }}</td>
                    <td>{{ $guest->mobile ?? '' }}</td>
                    <td>{{ $guest->nid ?? '' }}</td>
                    <td>{{ $guest->address ?? '' }}</td>
                    <td>{{ $guest->relation ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</div>

</x-default-layout>