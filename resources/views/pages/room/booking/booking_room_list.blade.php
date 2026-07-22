<x-default-layout>
<div class="app-content flex-column-fluid">
    <div class="app-container">

        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Hourly Room Booking (Multiple Rooms & Date/Time)</h3>
            </div>

            <div class="card-body">

                {{-- Search Form --}}
                <div class="row mb-5">
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" id="global_date"
                               class="form-control form-control-sm"
                               value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Start Time</label>
                        <input type="time" id="global_start"
                               class="form-control form-control-sm">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">End Time</label>
                        <input type="time" id="global_end"
                               class="form-control form-control-sm">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button id="searchBtn"
                                class="btn btn-sm btn-primary w-100">
                            Load Rooms
                        </button>
                    </div>
                </div>

                {{-- Room List --}}
                <form id="bulkBookingForm">
                    <div class="row" id="room_list"></div>

                    {{-- Submit --}}
                    <div class="row mt-4 d-none" id="bulk_action">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success btn-sm">
                                Book Selected Rooms
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
</x-default-layout>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function(){

    // Default today's full day
    let now = new Date();

let hours = String(now.getHours()).padStart(2, '0');
let minutes = String(now.getMinutes()).padStart(2, '0');

$('#global_start').val(`${hours}:${minutes}`);
    $('#global_end').val('23:59');

    /* Time validation */
    $('#global_end').on('change', function () {
        let s = $('#global_start').val();
        let e = $('#global_end').val();

        if (s && e && s >= e) {
            alert('End time must be greater than start time');
            $('#global_end').val('');
        }
    });

    /* Load rooms with booking check */
    function loadRooms() {

        let date  = $('#global_date').val();
        let start = $('#global_start').val();
        let end   = $('#global_end').val();

        if (!date || !start || !end) {
            alert('Please select date, start and end time');
            return;
        }

        $.ajax({
            url: "{{ route('booking.hourly.search') }}",
            method: "GET",
            data: {
                date: date,
                start_time: start,
                end_time: end
            },

            success: function(data) {

                $('#room_list').html('');
                $('#bulk_action').addClass('d-none');

                if (data.length === 0) {
                    $('#room_list').html(`
                        <div class="col-12 text-center text-muted">
                            No rooms found
                        </div>
                    `);
                    return;
                }

                data.forEach(room => {

                    let rowHtml = '';

                    if(room.is_booked){

    let isDayBooking = room.booking_type === 'Day Booking';

    rowHtml = `
    <div class="col-md-3 mb-4">
        <div class="card text-center p-3 shadow-sm border border-danger">

            <h6 class="mb-2">
                Room ${room.room_number}
            </h6>

            <div class="bg-light-danger rounded p-2 mb-3">

                <div class="fw-bold text-danger">
                    ${isDayBooking ? 'Day Booking' : 'Hourly Booking'}
                </div>

                <small class="d-block">
                    Total Days: ${room.total_days ?? 0}
                </small>
                
                ${isDayBooking ? `
                    <small class="d-block">
                        ${room.booked_from} → ${room.booked_to}
                    </small>
                ` : `
                    <small class="d-block">
                        Date: ${room.booked_date}
                    </small>

                    <small class="d-block">
                        ${room.booked_start} - ${room.booked_end}
                    </small>
                `}

            </div>

            <button class="btn btn-sm btn-danger w-100" disabled>
                Booked
            </button>

        </div>
    </div>`;
} else {

                        rowHtml = `
                        <div class="col-md-3 mb-4">
                            <div class="card p-3 shadow-sm">

                                <div class="form-check mb-2">
                                    <input type="checkbox"
                                           class="form-check-input room-check"
                                           data-room-id="${room.id}">

                                    <label class="form-check-label">
                                        Room ${room.room_number}
                                    </label>
                                </div>

                                <label>Date</label>
                                <input type="date"
                                       class="form-control form-control-sm room-date"
                                       value="${date}">

                                <label>Start Time</label>
                                <input type="time"
                                       class="form-control form-control-sm room-start"
                                       value="${start}">

                                <label>End Time</label>
                                <input type="time"
                                       class="form-control form-control-sm room-end"
                                       value="${end}">

                                <input type="hidden"
                                       class="room-price_per_night"
                                       value="${room.price_per_night}">
                            </div>
                        </div>`;
                    }

                    $('#room_list').append(rowHtml);
                });

                if(data.some(r => !r.is_booked)){
                    $('#bulk_action').removeClass('d-none');
                }
            },

            error: function() {
                alert('Something went wrong');
            }
        });
    }

    /* Search click */
    $('#searchBtn').on('click', function(e){
        e.preventDefault();
        loadRooms();
    });

    /* Auto load on page load */
    loadRooms();

});


/* Bulk booking submit */
$('#bulkBookingForm').on('submit', function(e){

    e.preventDefault();

    let selectedRooms = [];

    $('.room-check:checked').each(function(){

        let card = $(this).closest('.card');

        selectedRooms.push({
            id: $(this).data('room-id'),
            date: card.find('.room-date').val(),
            start: card.find('.room-start').val(),
            end: card.find('.room-end').val(),
            price: card.find('.room-price_per_night').val()
        });

    });

    console.log(selectedRooms);

    if(selectedRooms.length === 0){
        alert('Please select at least one room');
        return;
    }

    // Build query string
    let query = selectedRooms.map(r =>
        `rooms[${r.id}][date]=${encodeURIComponent(r.date)}&` +
        `rooms[${r.id}][start]=${encodeURIComponent(r.start)}&` +
        `rooms[${r.id}][end]=${encodeURIComponent(r.end)}&` +
        `rooms[${r.id}][price]=${encodeURIComponent(r.price)}`
    ).join('&');

    // Redirect
    window.location.href =
        "{{ route('booking.create.bulk') }}?" + query;

});
</script>

