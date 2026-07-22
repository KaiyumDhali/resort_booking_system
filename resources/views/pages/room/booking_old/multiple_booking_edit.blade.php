<x-default-layout>
    <style>
        .card .card-header { min-height: 40px; }
        .table> :not(caption)>*>* { padding: 0.3rem !important; }
        .selected-room {
            background-color: #ffc107 !important; 
            color: #000 !important;
            border-color: #ffc107 !important;
        }
        .room-btn { margin: 2px; }
        .dataTables_filter { float: right; }
        .dataTables_buttons { float: left; }
        .bottom { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    </style>

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <div id="kt_app_content_container" class="app-container">
                    <div class="d-flex flex-row">

                        {{-- Room List --}}
                        <div class="col-6 col-md-6 me-lg-10">
                            <div class="card card-flush py-4">
                              <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

    <h3 class="card-title mb-0">Room List</h3>

    <div class="d-flex align-items-center gap-2 flex-wrap">

        <div class="d-flex align-items-center gap-1">
            <label class="mb-0 small">Start</label>
            <input type="date" id="start_date"
                class="form-control form-control-sm"
                style="width:140px"
                value="{{ $bookingGroup->first()->check_in ?? date('Y-m-d') }}">
        </div>

        <div class="d-flex align-items-center gap-1">
            <label class="mb-0 small">End</label>
            <input type="date" id="end_date"
                class="form-control form-control-sm"
                style="width:140px"
                value="{{ $bookingGroup->last()->check_out ?? now()->addDays(29)->format('Y-m-d') }}">
        </div>

        <button class="btn btn-sm btn-primary" id="searchBtn">
            Search
        </button>

    </div>

</div>

                                <div class="card-body" style="max-height:550px; overflow-y:auto;">
                                    <div class="row" id="room_list_container">
                                        <!-- Rooms will be loaded here dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Room List End --}}

                        {{-- Booking & Customer Details --}}
                        <div class="col-6 col-md-6 me-lg-10">
                  <form method="POST" action="{{ route('booking.update1', $bookingGroup->first()->booking_no) }}" id="editBookingForm">
                        @csrf
                        @method('PATCH')

                                <div class="card card-flush py-4 mb-4">
                                    <div class="card-header"><h3 class="card-title">Booking Details</h3></div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped" id="booking_table">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Room No</th>
            <th>Price/Night</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Total Days</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookingGroup as $booking)
        <tr data-room-id="{{ $booking->room_id }}">
    <!-- Booking ID (hidden) -->
    <td>
        <input type="hidden" name="table_booking_id[]" value="{{ $booking->id }}">
        {{ $booking->room_id }}
        <input type="hidden" name="table_room_id[]" value="{{ $booking->room_id }}">
    </td>

    <td>
        {{ $booking->room->room_number }}
        <input type="hidden" name="table_room_number[]" value="{{ $booking->room->room_number }}">
    </td>

    <td>
        <input type="number" class="form-control form-control-sm room-price"
               name="table_room_price[]"
               value="{{ $booking->price_per_night ?? $booking->room->price_per_night ?? 0 }}"
               min="0">
    </td>

    <td>
        <input type="date" class="form-control form-control-sm check-in-date"
               name="table_check_in_date[]"
               value="{{ $booking->check_in_date }}">
    </td>

    <td>
        <input type="date" class="form-control form-control-sm check-out-date"
               name="table_check_out_date[]"
               value="{{ $booking->check_out_date }}">
    </td>

    <td class="day-count">
        {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) + 1 }}
    </td>

    <td>
        <button class="btn btn-sm btn-danger remove-room">Remove</button>
    </td>
</tr>

        @endforeach
    </tbody>
</table>

                                    </div>
                                </div>

                                {{-- Customer Details --}}
                                <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Customer & Guest Details</h5>
    </div>

    <div class="card-body">

        {{-- CUSTOMER INFO --}}
        <div class="mb-4">
            <h6 class="text-muted mb-3">Customer Information</h6>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label small text-muted">NID</label>
                    <input type="text" name="nid_number"
                        class="form-control form-control-sm"
                        value="{{ $firstBooking->customer->nid_number }}">
                </div>

                <div class="col-md-8">
                    <label class="form-label small text-muted">Full Name</label>
                    <input type="text" name="customer_name"
                        class="form-control form-control-sm fw-semibold"
                        value="{{ $firstBooking->customer->customer_name }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted">Mobile</label>
                    <input type="text" name="customer_mobile"
                        class="form-control form-control-sm"
                        value="{{ $firstBooking->customer->customer_mobile }}" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label small text-muted">Address</label>
                    <input type="text" name="customer_address"
                        class="form-control form-control-sm"
                        value="{{ $firstBooking->customer->customer_address }}" required>
                </div>

                {{-- SELF CHECK --}}
                <!-- <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input"
                            type="checkbox"
                            name="as_guest"
                            value="1"
                            id="asGuestSwitch"
                            {{ isset($selfGuest) ? 'checked' : '' }}>

                        <label class="form-check-label fw-semibold" for="asGuestSwitch">
                            Add Customer as Guest (Self)
                        </label>
                    </div>
                </div> -->

                 <div class="d-flex justify-content-between align-items-center">

                        <div class="form-check">
                            <input class="form-check-input"
                                            type="checkbox"
                                            name="as_guest"
                                            value="1"
                                            id="asGuestSwitch"
                                            {{ isset($selfGuest) ? 'checked' : '' }}>

                            <label class="form-check-label" for="as_guest">
                                Add as Guest (Self)
                            </label>
                        </div>

                        <button type="button"
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#organizationModal">
                            + Organization Info
                        </button>

                    </div>

            </div>
        </div>

        <hr>

        {{-- GUEST SECTION --}}
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted mb-0">Guest Information</h6>

                <button type="button" id="addGuest"
                    class="btn btn-sm btn-primary">
                    + Add Guest
                </button>
            </div>

            <div id="guest-container">

                @foreach($guests ?? [] as $index => $guest)
                <div class="guest-row border rounded p-3 mb-3 bg-light">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-primary">
                            Guest {{ $index + 1 }}
                        </span>

                        <button type="button"
                            class="btn btn-sm btn-outline-danger removeGuest">
                            Remove
                        </button>
                    </div>

                    <div class="row g-2">

                        <div class="col-md-4">
                            <input type="text"
                                name="guests[{{ $index }}][nid]"
                                value="{{ $guest->nid }}"
                                placeholder="NID"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <input type="text"
                                name="guests[{{ $index }}][name]"
                                value="{{ $guest->name }}"
                                placeholder="Name"
                                class="form-control form-control-sm"
                                required>
                        </div>

                        <div class="col-md-4">
                            <input type="text"
                                name="guests[{{ $index }}][mobile]"
                                value="{{ $guest->mobile }}"
                                placeholder="Mobile"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <input type="text"
                                name="guests[{{ $index }}][address]"
                                value="{{ $guest->address }}"
                                placeholder="Address"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <input type="text"
                                name="guests[{{ $index }}][relation]"
                                value="{{ $guest->relation }}"
                                placeholder="Relation"
                                class="form-control form-control-sm">
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <hr>

        {{-- PAYMENT SUMMARY --}}
        <div class="row">

    {{-- LEFT SIDE (your existing content) --}}
    <div class="col-md-8">
        {{-- your form / guest / customer --}}
    </div>

    {{-- RIGHT SIDE PAYMENT SUMMARY --}}
    <div class="col-md-4">

        <div class="">
            <div class="">
                <h6 class="mb-0 fw-semibold">Payment Summary</h6>
            </div>

            <div class="">

                <div class="mb-3 d-flex gap-4 py-3">
                    <label class="form-label small text-muted pt-2">Total</label>
                    <input type="number" id="total_price"
                        class="form-control form-control-sm fw-bold text-dark text-end"
                        value="{{ $totalAmount - $discount ?? 0 }}"
                        readonly>
                </div>

                <div class="mb-3 d-flex gap-4">
                    <label class="form-label small text-muted  pt-2">Discount</label>
                    <input type="number" id="discount_amount"
                        name="discount_amount"
                        class="form-control form-control-sm text-end"
                        value="{{ $discount ?? 0 }}">
                </div>

                <div class="mb-3 d-flex gap-4">
                    <label class="form-label small text-success  pt-2">Paid</label>
                    <input type="number" id="paid_amount"
                        name="paid_amount"
                        class="form-control form-control-sm text-end"
                        value="{{ $totalPaid ?? 0 }}">
                </div>

                <div class="mb-3 d-flex gap-4">
                    <label class="form-label small text-danger  pt-2">Due</label>
                    <input type="number" id="due_amount"
                        class="form-control form-control-sm fw-bold text-danger text-end"
                        value="{{ ($totalAmount - $discount) - $totalPaid ?? 0 }}"
                        readonly>
                </div>

            </div>
        </div>

    </div>

</div>

    </div>
</div>

                                <div class="d-flex justify-content-end mb-10">
                                    <a href="{{ route('booking.index') }}" class="btn btn-success me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Booking</button>
                                </div>


                                <!-- Organization Modal -->
<div class="modal fade" id="organizationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Organization Information
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">
                        Organization Name
                    </label>

                    <input type="text"
       name="organization_name"
       value="{{ $firstBooking->customer->organization_name }}"
       class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Email
                    </label>

                    <input type="email"
       name="organization_email"
       value="{{ $firstBooking->customer->organization_email }}"
       class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Mobile
                    </label>

                   <input type="text"
       name="organization_mobile"
       value="{{ $firstBooking->customer->organization_mobile }}"
       class="form-control form-control-sm">
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-light btn-sm"
                        data-bs-dismiss="modal">
                    Close
                </button>

                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-dismiss="modal">
                    OK
                </button>

            </div>

        </div>
    </div>
</div>
                            </form>
                        </div>
                        {{-- Booking End --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Set min dates
    $('#start_date, #end_date').attr('min', new Date().toISOString().split('T')[0]);

    // Load room list
    loadRoomList();
    $('#searchBtn').on('click', loadRoomList);

    function loadRoomList(){
        let start = $('#start_date').val();
        let end = $('#end_date').val();
        let url = '{{ route("booking_search", ["startDate" => ":startDate", "endDate" => ":endDate"]) }}';
        url = url.replace(':startDate', start).replace(':endDate', end);

        $.get(url, function(data){
            $('#room_list_container').empty();
            if(data.length){
                let displayedDates = new Set();
                data.forEach(d=>{
                    if(!displayedDates.has(d.date)){
                        displayedDates.add(d.date);
                        $('#room_list_container').append(`<div class="col-12 text-center text-success"><b>${new Date(d.date).toDateString()}</b></div>`);
                    }

                    let btn = d.is_booked === 'Booked'
                        ? `<button class="btn btn-sm btn-danger room-btn" disabled data-date="${d.date}">${d.room_number}</button>`
                        : `<button class="btn btn-sm btn-primary room-btn" 
                            data-room-id="${d.room_id}" 
                            data-room-number="${d.room_number}" 
                            data-price="${d.price_per_night}" 
                            data-check-in="${d.date}" 
                            data-check-out="${d.date}" 
                            data-date="${d.date}">
                            ${d.room_number}</button>`;

                    $('#room_list_container').append(`<div class="col-md-1">${btn}</div>`);
                });

                // Highlight existing booking
                @foreach($bookingGroup as $booking)
                    highlightRoomDates({{ $booking->room_id }}, '{{ $booking->check_in }}', '{{ $booking->check_out }}');
                @endforeach
            } else {
                $('#room_list_container').append('<p>No rooms found.</p>');
            }
        });
    }

    // Add room on click
    $(document).on('click', '.room-btn', function(){
        let roomId = $(this).data('room-id');
        let roomNumber = $(this).data('room-number');
        let price = $(this).data('price');
        let checkIn = $(this).data('check-in');
        let checkOut = $(this).data('check-out');

        // Prevent duplicates
        if($('#booking_table tbody tr[data-room-id="'+roomId+'"]').length) return;

        let days = 1;

        $('#booking_table tbody').append(`
            <tr data-room-id="${roomId}">
                <td>${roomId}<input type="hidden" name="table_room_id[]" value="${roomId}"></td>
                <td>${roomNumber}<input type="hidden" name="table_room_number[]" value="${roomNumber}"></td>
                <td><input type="number" class="form-control form-control-sm room-price" name="table_room_price[]" value="${price}" min="0"></td>
                <td><input type="date" class="form-control form-control-sm check-in-date" name="table_check_in_date[]" value="${checkIn}"></td>
                <td><input type="date" class="form-control form-control-sm check-out-date" name="table_check_out_date[]" value="${checkOut}"></td>
                <td class="day-count">${days}</td>
                <td><button class="btn btn-sm btn-danger remove-room">Remove</button></td>
            </tr>
        `);

        updateDaysAndTotal();
        highlightRoomDates(roomId, checkIn, checkOut);
    });

    // Remove room
    $(document).on('click', '.remove-room', function(e){
        e.preventDefault();
        $(this).closest('tr').remove();
        updateDaysAndTotal();
    });

    // Update days and total whenever any date or price changes
    $(document).on('input change', '.room-price, .check-in-date, .check-out-date', function(){
        updateDaysAndTotal();
    });

    function updateDaysAndTotal(){
        let total = 0;
        $('#booking_table tbody tr').each(function(){
            let row = $(this);
            let checkIn = new Date(row.find('.check-in-date').val());
            let checkOut = new Date(row.find('.check-out-date').val());
            if(!checkIn || !checkOut) return;

            let days = Math.max(1, Math.ceil((checkOut - checkIn)/(1000*60*60*24)));
            row.find('.day-count').text(days);

            let price = parseFloat(row.find('.room-price').val()) || 0;
            total += price * days;
        });

        let discount = parseFloat($('#discount_amount').val()) || 0;
        if(discount > total) discount = total;

        let netTotal = total - discount;
        let paid = parseFloat($('#paid_amount').val()) || 0;
        let due = netTotal - paid;

        $('#total_price').val(netTotal.toFixed(2));
        $('#due_amount').val(due < 0 ? 'Change: '+Math.abs(due).toFixed(2) : due.toFixed(2));
    }

    // Highlight booked rooms
    function getDatesBetween(start, end){
        let dates = [];
        let current = new Date(start);
        let last = new Date(end);
        while(current <= last){
            dates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
        }
        return dates;
    }

    function highlightRoomDates(roomId, start, end){
        let dates = getDatesBetween(start, end);
        dates.forEach(date=>{
            $(`.room-btn[data-room-id="${roomId}"][data-date="${date}"]`)
                .addClass('selected-room')
                .removeClass('btn-primary')
                .addClass('btn-danger')
                .prop('disabled', true);
        });
    }

    // Also update total on discount or paid amount change
    $('#discount_amount, #paid_amount').on('input', updateDaysAndTotal);

    // Initialize total on page load
    updateDaysAndTotal();
});



document.getElementById('addGuest').addEventListener('click', function () {

    let index = guestIndex++;

    let html = `
        <div class="guest-row border p-2 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Guest ${index + 1}</strong>
                <button type="button" class="btn btn-danger btn-sm removeGuest">Remove</button>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>NID</label>
                    <input type="text" name="guests[${index}][nid]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label>Name</label>
                    <input type="text" name="guests[${index}][name]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6 mt-2">
                    <label>Mobile</label>
                    <input type="text" name="guests[${index}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mt-2">
                    <label>Address</label>
                    <input type="text" name="guests[${index}][address]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mt-2">
                    <label>Relation</label>
                    <input type="text" name="guests[${index}][relation]" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    `;

    document.getElementById('guest-container').insertAdjacentHTML('beforeend', html);
});


document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeGuest')){
        e.target.closest('.guest-row').remove();

        let rows = document.querySelectorAll('.guest-row');

        rows.forEach((row, index) => {
            let newIndex = index;

            row.querySelector('strong').innerText = `Guest ${index + 1}`;

            row.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/guests\[\d+\]/, `guests[${newIndex}]`);
            });
        });

        guestIndex = rows.length;
    }
});

function fetchCustomerData(value) {

    value = value ? value.trim() : '';

    const nameEl = document.querySelector('[name="customer_name"]');
    const addressEl = document.querySelector('[name="customer_address"]');
    const mobileEl = document.querySelector('[name="customer_mobile"]');

    // 🔴 যদি empty হয় → সাথে সাথে reset
    if (value.length === 0) {
        nameEl.value = '';
        addressEl.value = '';
        mobileEl.value = '';
        return;
    }

    // 🔴 খুব ছোট হলে (partial delete / type) → reset
    if (value.length < 6) {
        nameEl.value = '';
        addressEl.value = '';
        mobileEl.value = '';
        return;
    }

    // 🟢 valid length হলে API call
    fetch(`/person/search?query=${value}`)
        .then(res => res.json())
        .then(data => {

            let result = null;

            if (data?.source === 'customer') {
                result = data.data;
            } else if (data?.source === 'guest') {
                result = data.data;
            } else if (data?.source === 'both') {
                result = data.customer;
            }

            if (result) {
                // ✅ MATCH → fill
                nameEl.value = result.customer_name || result.name || '';
                addressEl.value = result.customer_address || result.address || '';
                mobileEl.value = result.customer_mobile || result.mobile || '';
            } else {
                // ❌ NOT MATCH → reset
                nameEl.value = '';
                addressEl.value = '';
                mobileEl.value = '';
            }
        })
        .catch(() => {
            // ❌ error হলেও reset safe
            nameEl.value = '';
            addressEl.value = '';
            mobileEl.value = '';
        });
}

document.addEventListener('input', function (e) {

    if (!e.target.name) return;

    if (e.target.name.includes('[mobile]') || e.target.name.includes('[nid]')) {

        let parent = e.target.closest('.guest-row');
        if (!parent) return;

        let value = e.target.value.trim();

        const nameEl = parent.querySelector('[name*="[name]"]');
        const mobileEl = parent.querySelector('[name*="[mobile]"]');
        const nidEl = parent.querySelector('[name*="[nid]"]');
        const addressEl = parent.querySelector('[name*="[address]"]');
        const relationEl = parent.querySelector('[name*="[relation]"]');

        // default flag
        if (!parent.dataset.autofilled) {
            parent.dataset.autofilled = "0";
        }

        // 🔴 EMPTY → instant reset
        if (value.length === 0) {
            nameEl.value = '';
            mobileEl.value = '';
            nidEl.value = '';
            addressEl.value = '';
            relationEl.value = '';
            parent.dataset.autofilled = "0";
            return;
        }

        // 🔴 small input (typing বা deleting) → reset
        if (value.length < 6) {
            nameEl.value = '';
            addressEl.value = '';
            relationEl.value = '';
            // mobile/nid reset করছিনা (user typing করছে)
            return;
        }

        // 🟢 valid হলে API call (instant)
        fetch(`/person/search?query=${value}`)
            .then(res => res.json())
            .then(data => {

                if (!data) return;

                let result = null;

                // 🔥 guest priority
                if (data.source === 'guest') {
                    result = data.data;
                } else if (data.source === 'customer') {
                    result = data.data;
                } else if (data.source === 'both') {
                    result = data.guest;
                }

                if (result) {
                    // ✅ MATCH → fill
                    nameEl.value = result.name || result.customer_name || '';
                    mobileEl.value = result.mobile || result.customer_mobile || '';
                    nidEl.value = result.nid || result.nid_number || '';
                    addressEl.value = result.address || result.customer_address || '';
                    relationEl.value = result.relation || '';

                    parent.dataset.autofilled = "1";
                } else {
                    // ❌ NOT MATCH → reset
                    nameEl.value = '';
                    mobileEl.value = '';
                    addressEl.value = '';
                    relationEl.value = '';

                    parent.dataset.autofilled = "0";
                }
            })
            .catch(() => {
                // ❌ error → safe reset
                nameEl.value = '';
                mobileEl.value = '';
                addressEl.value = '';
                relationEl.value = '';
                parent.dataset.autofilled = "0";
            });
    }
});
let guestIndex = {{ isset($guests) ? count($guests) : 0 }};

document.addEventListener('DOMContentLoaded', function () {

    let rows = document.querySelectorAll('.guest-row');

    rows.forEach((row, index) => {
        let guestNo = index + 1;
        row.querySelector('strong').innerText = `Guest ${guestNo}`;
    });

});
</script>

