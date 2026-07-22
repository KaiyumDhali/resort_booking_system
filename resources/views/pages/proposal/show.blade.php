
@php
    $allFacilities = collect($facilities ?? [])->merge($spotFacilities ?? []);
@endphp

<x-default-layout>
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Proposal #{{ $proposal->id }}</h2>
        <a class="btn btn-outline-dark" href="{{ route('proposals.pdf', $proposal) }}">Download PDF</a>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4>{{ $proposal->proposal_title ?? 'Proposal' }}</h4>
           @php($customer = $proposal->customer)

<p><strong>Client:</strong>
    {{ $customer?->customer_name ?? 'N/A' }}
    | {{ $customer?->customer_email ?? $proposal->client_email ?? 'N/A' }}
    | {{ $customer?->customer_mobile ?? $proposal->client_phone ?? 'N/A' }}
</p>


            @if($proposal->intro_text)
                <h5>Introduction</h5>
                <pre style="white-space: pre-wrap;">{{ $proposal->intro_text }}</pre>
            @endif

            @if($rooms->count())
                <h5>Rooms</h5>
                <ul>
                    @foreach($rooms as $it)
                        <li>
                            {{ $it->title }} — {{ $it->quantity }} room(s) × {{ $it->nights }} night(s)
                            @ ৳{{ number_format($it->unit_price, 2) }}
                            = ৳{{ number_format($it->line_total, 2) }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($spots->count())
                <h5>Spots</h5>
                <ul>
                    @foreach($spots as $it)
                        <li>
                            {{ $it->title }} — qty {{ $it->quantity }}
                            @ ৳{{ number_format($it->unit_price, 2) }}
                            = ৳{{ number_format($it->line_total, 2) }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($packages->count())
                <h5>Packages</h5>
                <ul>
                    @foreach($packages as $it)
                        <li>
                            {{ $it->title }} — qty {{ $it->quantity }}
                            @ ৳{{ number_format($it->unit_price, 2) }}
                            = ৳{{ number_format($it->line_total, 2) }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($services->count())
                <h5>Additional Services</h5>
                <ul>
                    @foreach($services as $it)
                        <li>
                            {{ $it->title }} — qty {{ $it->quantity }}
                            @ ৳{{ number_format($it->unit_price, 2) }}
                            = ৳{{ number_format($it->line_total, 2) }}
                        </li>
                    @endforeach
                </ul>
            @endif


@if($allFacilities->count())
    <h5>Facilities Included</h5>
    <ul>
        @foreach($allFacilities as $it)
            <li>
                {{ $it->title ?? '' }}
                @if(($it->item_type ?? null) === 'spot_facility' && !empty($it->description))
                    <small class="text-muted">({{ $it->description }})</small>
                @endif
            </li>
        @endforeach
    </ul>
@endif



            <hr>
            <p><strong>Subtotal:</strong> ৳{{ number_format($proposal->subtotal, 2) }}</p>
            <p><strong>Discount:</strong> ৳{{ number_format($proposal->discount, 2) }}</p>
            <p><strong>Tax:</strong> ৳{{ number_format($proposal->tax, 2) }}</p>
            <p><strong>Total:</strong> ৳{{ number_format($proposal->total, 2) }}</p>

            @if($proposal->terms_text)
                <h5>Terms</h5>
                <pre style="white-space: pre-wrap;">{{ $proposal->terms_text }}</pre>
            @endif

            @if($proposal->notes_text)
                <h5>Notes</h5>
                <pre style="white-space: pre-wrap;">{{ $proposal->notes_text }}</pre>
            @endif
        </div>
    </div>
</div>
</x-default-layout>
