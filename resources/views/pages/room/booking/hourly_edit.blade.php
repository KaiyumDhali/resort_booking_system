<x-default-layout>

<div class="app-content flex-column-fluid">
<div class="app-container">

<form method="POST"
      action="{{ route('booking.hourly.update', $firstBooking->booking_no) }}">

@csrf
@method('PATCH')

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">

{{-- ================= LEFT ================= --}}
<div class="col-md-7">

<div class="card p-4 shadow-sm">

<div class="row mb-4">

    <div class="col-md-3">
        <label>Date</label>

        <input type="date"
               id="edit_date"
               class="form-control form-control-sm">
    </div>

    <div class="col-md-3">
        <label>Start</label>

        <input type="time"
               id="edit_start"
               class="form-control form-control-sm">
    </div>

    <div class="col-md-3">
        <label>End</label>

        <input type="time"
               id="edit_end"
               class="form-control form-control-sm">
    </div>

    <div class="col-md-3 d-flex align-items-end">

        <button type="button"
                id="editSearchBtn"
                class="btn btn-primary btn-sm w-100">

            Search Rooms
        </button>

    </div>

</div>

<div class="row" id="edit_room_list"></div>

</div>

</div>

{{-- ================= RIGHT ================= --}}
<div class="col-md-5">

{{-- ROOM DETAILS --}}
<div class="card p-4 shadow-sm mb-4">

<h5 class="mb-3">Room Details (Edit)</h5>

<table class="table table-bordered align-middle">

<thead>
<tr>
    <th>Room</th>
    <th>Date</th>
    <th>Start</th>
    <th>End</th>
    <th>Price</th>
</tr>
</thead>

<tbody id="selected_rooms_table">

@foreach($bookingRooms as $room)

<tr id="room_row_{{ $room->room_id }}">

    <td>
        Room {{ $room->room->room_number }}

        <input type="hidden"
               name="rooms[{{ $room->room_id }}][booking_id]"
               value="{{ $room->id }}">

        <input type="hidden"
               name="rooms[{{ $room->room_id }}][room_id]"
               value="{{ $room->room_id }}">

        <input type="hidden"
               name="rooms[{{ $room->room_id }}][room_number]"
               value="{{ $room->room->room_number }}">
    </td>

    <td>
        <input type="date"
               name="rooms[{{ $room->room_id }}][date]"
               value="{{ $room->check_in_date }}"
               class="form-control form-control-sm">
    </td>

    <td>
        <input type="time"
               name="rooms[{{ $room->room_id }}][start]"
               value="{{ substr($room->check_in_datetime,0,5) }}"
               class="form-control form-control-sm">
    </td>

    <td>
        <input type="time"
               name="rooms[{{ $room->room_id }}][end]"
               value="{{ substr($room->check_out_datetime,0,5) }}"
               class="form-control form-control-sm">
    </td>

    <td>
        <input type="text"
               value="{{ $room->total_amount }}"
               class="form-control form-control-sm"
               readonly>

        <input type="hidden"
               class="room-price"
               value="{{ $room->total_amount }}">
    </td>

</tr>

@endforeach

</tbody>

</table>

</div>

{{-- CUSTOMER --}}
<div class="card p-4 shadow-sm mb-4">

<h5 class="mb-3">Customer Info</h5>

<div class="row">

<div class="col-md-5 mb-2">
<label>NID</label>

<input type="text"
       name="customer_nid"
       value="{{ $customer->nid_number }}"
       class="form-control form-control-sm"
       oninput="fetchCustomerData(this.value)">
</div>

<div class="col-md-7 mb-2">
<label>Name</label>

<input type="text"
       name="customer_name"
       value="{{ $customer->customer_name }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-5 mb-2">
<label>Mobile</label>

<input type="text"
       name="customer_mobile"
       value="{{ $customer->customer_mobile }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-7 mb-2">
<label>Address</label>

<input type="text"
       name="customer_address"
       value="{{ $customer->customer_address }}"
       class="form-control form-control-sm">
</div>

</div>

<!-- <div class="form-check mt-2">

        <input type="checkbox"
            id="as_guest"
            name="as_guest"
            value="1"
            class="form-check-input"
            {{ $selfGuest ? 'checked' : '' }}>

        <label class="form-check-label">
            Add Customer as Guest (Self)
        </label>

</div> -->
 <div class="d-flex justify-content-between align-items-center">

        <div class="form-check">
            <input type="checkbox"
            id="as_guest"
            name="as_guest"
            value="1"
            class="form-check-input"
            {{ $selfGuest ? 'checked' : '' }}>

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
<hr>

<h6>Guest List</h6>

<div id="guest-container">

@foreach($guests as $i => $g)

@if($g->customer_status == 0)

<div class="guest-row border rounded p-2 mb-2">

<div class="d-flex justify-content-between mb-2">
    <strong>Guest {{ $i + 1 }}</strong>

    <button type="button"
            class="btn btn-danger btn-sm removeGuest">

        Remove
    </button>
</div>

<div class="row">

<div class="col-md-5 mb-2">
<label>NID</label>

<input type="text"
       name="guests[{{ $i }}][nid]"
       value="{{ $g->nid }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-7 mb-2">
<label>Name</label>

<input type="text"
       name="guests[{{ $i }}][name]"
       value="{{ $g->name }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-5 mb-2">
<label>Mobile</label>

<input type="text"
       name="guests[{{ $i }}][mobile]"
       value="{{ $g->mobile }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-7 mb-2">
<label>Address</label>

<input type="text"
       name="guests[{{ $i }}][address]"
       value="{{ $g->address }}"
       class="form-control form-control-sm">
</div>

<div class="col-md-5 mb-2">
<label>Relation</label>

<input type="text"
       name="guests[{{ $i }}][relation]"
       value="{{ $g->relation }}"
       class="form-control form-control-sm">
</div>

</div>

</div>

@endif
@endforeach

</div>

<button type="button"
        id="addGuest"
        class="btn btn-success btn-sm mt-2">

+ Add Guest
</button>

</div>

{{-- PAYMENT --}}
<div class="card p-4 shadow-sm">

<h5 class="mb-3">Payment Summary</h5>

<div class="mb-2">
<label>Total</label>

<input type="text"
       id="net_total"
       class="form-control form-control-sm"
       readonly>
</div>

<div class="mb-2">
<label>Discount</label>

<input type="number"
       id="discount"
       name="discount"
       value="{{ $firstBooking->discount }}"
       class="form-control form-control-sm">
</div>

<div class="mb-2">
<label>Paid</label>

<input type="number"
       id="paid"
       name="paid"
       value="{{ $paid }}"
       class="form-control form-control-sm">
</div>

<div class="mb-2">
<label>Due</label>

<input type="text"
       id="due"
       class="form-control form-control-sm"
       readonly>
</div>

</div>

</div>

</div>

<div class="text-end mt-4 mb-5">

<button class="btn btn-primary">
    Update Booking
</button>

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
       value="{{ $customer->organization_name }}"
       class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Email
                    </label>

                    <input type="email"
       name="organization_email"
       value="{{ $customer->organization_email }}"
       class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Mobile
                    </label>

                   <input type="text"
       name="organization_mobile"
       value="{{ $customer->organization_mobile }}"
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
</div>

<script>
let currentBookingRooms = @json($currentRoomIds);
currentBookingRooms = currentBookingRooms.map(Number);
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

function calc(){

    let total = 0;

    document.querySelectorAll('.room-price').forEach(e => {
        total += parseFloat(e.value || 0);
    });

    let discount = parseFloat($('#discount').val() || 0);
    let paid = parseFloat($('#paid').val() || 0);

    let net = total - discount;
    let due = net - paid;

    $('#net_total').val(net.toFixed(2));
    $('#due').val(due.toFixed(2));
}

document.addEventListener('input', calc);
document.addEventListener('DOMContentLoaded', calc);

$(document).ready(function(){

    // ================= CURRENT TIME =================

    let now = new Date();

    let yyyy = now.getFullYear();
    let mm = String(now.getMonth() + 1).padStart(2, '0');
    let dd = String(now.getDate()).padStart(2, '0');

    let hh = String(now.getHours()).padStart(2, '0');
    let min = String(now.getMinutes()).padStart(2, '0');

    let today = `${yyyy}-${mm}-${dd}`;
    let currentTime = `${hh}:${min}`;

    $('#edit_date').val(today);

    $('#edit_start').val(currentTime);

    $('#edit_end').val('23:59');

    // ================= SEARCH CLICK =================

    $('#editSearchBtn').on('click', function(){

        let date = $('#edit_date').val();
        let start = $('#edit_start').val();
        let end = $('#edit_end').val();

        if(!date || !start || !end){
            alert('Date, Start & End required');
            return;
        }

        $.ajax({

            url: "{{ route('booking.hourly.search') }}",

            type: "GET",

            data: {
                date: date,
                start_time: start,
                end_time: end
            },

            success: function(data){

    $('#edit_room_list').html('');

    if(!data || data.length === 0){

        $('#edit_room_list').html(`
            <div class="col-12 text-center text-muted">
                No rooms found
            </div>
        `);

        return;
    }

    data.forEach(room => {

        let isSelected = currentBookingRooms.map(Number).includes(Number(room.id));

        let disabled = (room.is_booked && !isSelected) ? 'disabled' : '';

        let html = '';

        // ================= BOOKED ROOM UI =================
        if(room.is_booked && !isSelected){

            html = `
<div class="col-md-3 mb-3">

    <div class="card text-center p-3 shadow-sm border border-danger">

        <h6 class="mb-2">
            Room ${room.room_number}
        </h6>

        <div class="bg-light-danger rounded p-2 mb-3">

            <div class="fw-bold text-danger">
                ${room.booking_type}
            </div>

            ${
                room.total_days > 0
                ? `
                    <small class="d-block">
                        ${room.booked_from} → ${room.booked_to}
                    </small>
                    <small class="d-block text-warning">
                        Total Days: ${room.total_days}
                    </small>
                `
                : `
                <small class="d-block">
                        Date: ${room.booked_date}
                    </small>
                    <small class="d-block">
                        ${room.booked_start} - ${room.booked_end}
                    </small>
                `
            }

        </div>

        <button class="btn btn-sm btn-danger w-100" disabled>
            Booked
        </button>

    </div>

</div>`;
        }

        // ================= FREE / SELECTABLE ROOM UI =================
        else {

            let statusText = 'Free';
            let statusClass = 'text-success';

            if(isSelected){
                statusText = 'Selected';
                statusClass = 'text-primary';
            }

            html = `
            <div class="col-md-3 mb-3">

                <div class="card p-3 shadow-sm border">

                    <div class="form-check mb-2">

                        <input type="checkbox"
                               class="form-check-input edit-room-check"
                               data-room-id="${room.id}"
                               data-room-number="${room.room_number}"
                               ${isSelected ? 'checked' : ''}
                               ${disabled}>

                        <label>
                            Room ${room.room_number}
                        </label>

                    </div>

                    <input type="date"
                           class="form-control form-control-sm edit-date mb-1"
                           value="${date}">

                    <input type="time"
                           class="form-control form-control-sm edit-start mb-1"
                           value="${start}">

                    <input type="time"
                           class="form-control form-control-sm edit-end mb-1"
                           value="${end}">

                    <input type="hidden"
                           class="edit-price"
                           value="${room.price_per_night}">

                    <div class="${statusClass} fw-bold mt-2">
                        ${statusText}
                    </div>

                </div>

            </div>`;
        }

        $('#edit_room_list').append(html);
    });
},

            error: function(xhr){

                console.log(xhr.responseText);

                alert('Server error');
            }

        });

    });

    // ================= AUTO LOAD SEARCH =================

    $('#editSearchBtn').trigger('click');

});

// ================= ROOM SELECT =================

$(document).on('change', '.edit-room-check', function(){

    let card = $(this).closest('.card');

    let roomId = $(this).data('room-id');

    let roomNumber = $(this).data('room-number');

    let date = card.find('.edit-date').val();

    let start = card.find('.edit-start').val();

    let end = card.find('.edit-end').val();

    let price = parseFloat(card.find('.edit-price').val() || 0);

    if($(this).is(':checked')){

        if($('#room_row_' + roomId).length){
            return;
        }

        let html = `

        <tr id="room_row_${roomId}">

            <td>

                Room ${roomNumber}

                <input type="hidden"
                       name="rooms[new_${roomId}][booking_id]"
                       value="">

                <input type="hidden"
                       name="rooms[new_${roomId}][room_id]"
                       value="${roomId}">

                <input type="hidden"
                       name="rooms[new_${roomId}][room_number]"
                       value="${roomNumber}">
            </td>

            <td>
                <input type="date"
                       name="rooms[new_${roomId}][date]"
                       value="${date}"
                       class="form-control form-control-sm">
            </td>

            <td>
                <input type="time"
                       name="rooms[new_${roomId}][start]"
                       value="${start}"
                       class="form-control form-control-sm">
            </td>

            <td>
                <input type="time"
                       name="rooms[new_${roomId}][end]"
                       value="${end}"
                       class="form-control form-control-sm">
            </td>

            <td>

                <input type="text"
                       value="${price}"
                       class="form-control form-control-sm"
                       readonly>

                <input type="hidden"
                       class="room-price"
                       value="${price}">

            </td>

        </tr>
        `;

        $('#selected_rooms_table').append(html);

        calc();

    } else {

        $('#room_row_' + roomId).remove();

        calc();
    }

});

// ================= ADD GUEST =================

let i = {{ count($guests) }};

$('#addGuest').on('click', function(){

    i++;

    let html = `

    <div class="guest-row border rounded p-2 mb-2">

        <div class="d-flex justify-content-between mb-2">

            <strong>Guest ${i}</strong>

            <button type="button"
                    class="btn btn-danger btn-sm removeGuest">

                Remove
            </button>

        </div>

        <div class="row">

            <div class="col-md-5 mb-2">
                <label>NID</label>

                <input type="text"
                       name="guests[${i}][nid]"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-7 mb-2">
                <label>Name</label>

                <input type="text"
                       name="guests[${i}][name]"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-5 mb-2">
                <label>Mobile</label>

                <input type="text"
                       name="guests[${i}][mobile]"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-7 mb-2">
                <label>Address</label>

                <input type="text"
                       name="guests[${i}][address]"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-5 mb-2">
                <label>Relation</label>

                <input type="text"
                       name="guests[${i}][relation]"
                       class="form-control form-control-sm">
            </div>

        </div>

    </div>
    `;

    $('#guest-container').append(html);

});

// ================= REMOVE GUEST =================

$(document).on('click', '.removeGuest', function(){

    $(this).closest('.guest-row').remove();
});

// ================= SELF GUEST =================

$('#as_guest').on('change', function(){

    if(this.checked){
        alert('Customer will be added as Guest (Self)');
    }

});

</script>

</x-default-layout>