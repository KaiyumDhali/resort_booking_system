<x-default-layout>
    <style>
        /* ===============================
           CLEAN STANDARD UI - BOOKING EDIT PAGE
        =============================== */
        :root{
            --bg: #f6f8fb;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --primary: #2563eb;
            --danger: #ef4444;
            --success: #16a34a;
            --shadow: 0 10px 30px rgba(0,0,0,.06);
            --radius: 14px;
        }

        .booking-page{
            background: var(--bg);
            border-radius: var(--radius);
        }

        .section-title{
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }
        .section-subtitle{
            font-size: 12px;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .card-clean{
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-head{
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            background: var(--card);
            z-index: 5;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }

        .filters{
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: end;
            justify-content: flex-end;
        }

        .filters .form-group label{
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .btn-clean{
            border-radius: 10px !important;
            padding: 8px 14px !important;
            font-weight: 600;
        }

        /* Room list scroll area */
        .room-scroll{
            max-height: 610px;
            overflow: auto;
            padding: 14px 16px 18px;
        }

        .date-header{
            position: sticky;
            top: 2px;
            z-index: 4;
            background: var(--card);
            padding: 10px 0 8px;
            border-bottom: 1px dashed var(--border);
            margin-bottom: 10px;
        }

        .date-header h4{
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: var(--success);
            text-align: center;
        }

        /* Room chips */
        .room-grid{
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 10px;
            padding-bottom: 12px;
        }

        @media (max-width: 1400px){ .room-grid{ grid-template-columns: repeat(6, minmax(0, 1fr)); } }
        @media (max-width: 1200px){ .room-grid{ grid-template-columns: repeat(5, minmax(0, 1fr)); } }
        @media (max-width: 992px){
            .two-col-stack{ flex-direction: column; }
            .room-grid{ grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
        @media (max-width: 576px){ .room-grid{ grid-template-columns: repeat(4, minmax(0, 1fr)); } }

        .room-chip{
            width: 100%;
            border: 1px solid var(--border);
            background: #f9fafb;
            color: var(--text);
            border-radius: 12px;
            padding: 10px 0;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            transition: .15s ease;
            user-select: none;
        }
        .room-chip:hover{
            transform: translateY(-1px);
            border-color: #cbd5e1;
            background: #fff;
        }
        .room-chip.is-booked{
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
            cursor: not-allowed;
            opacity: .95;
        }
        .room-chip.is-selected{
            background: #fef9c3;
            border-color: #fbbf24;
            color: #92400e;
        }

        /* Booking table */
        .table-clean{ margin: 0; }
        .table-clean thead th{
            position: sticky;
            top: 0;
            z-index: 3;
            background: #111827;
            color: #fff;
            font-size: 12px;
            letter-spacing: .3px;
            text-transform: uppercase;
        }
        .table-clean td, .table-clean th{
            padding: .55rem !important;
            vertical-align: middle;
        }
        .table-wrap{
            max-height: 320px;
            overflow: auto;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        /* Form spacing */
        .form-block{
            padding: 14px 16px 16px;
        }
        .form-block .form-label{
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 5px;
        }
        .form-block .form-control,
        .form-block .form-select{
            border-radius: 10px;
        }

        /* Sticky bottom actions */
        .sticky-actions{
            position: sticky;
            bottom: 0;
            z-index: 10;
            background: rgba(246,248,251,.85);
            backdrop-filter: blur(8px);
            padding: 12px 0 0;
        }
        .action-bar{
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding: 12px 16px;
            border: 1px solid var(--border);
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .hint{
            font-size: 12px;
            color: var(--muted);
        }

        .summary-row{
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }
        .summary-row:last-child{ border-bottom: none; }
        .summary-label{ font-size: 13px; color: var(--muted); }
        .summary-value{ font-size: 15px; font-weight: 700; color: var(--text); }
        .summary-value.text-danger{ color: var(--danger) !important; }
        .summary-value.text-success{ color: var(--success) !important; }
    </style>

    <div class="app-main flex-column flex-row-fluid booking-page" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="kt_app_content_container" class="app-container">
                    <div class="d-flex flex-row gap-8 two-col-stack">

                        {{-- LEFT: ROOM LIST --}}
                        <div class="col-12 col-lg-6">
                            <div class="card-clean">
                                <div class="card-head">
                                    <div>
                                        <p class="section-title">Room List</p>
                                        <p class="section-subtitle">Pick room(s) by date. Booked rooms are disabled.</p>
                                    </div>
                                    <div class="filters">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" id="start_date"
                                                class="form-control form-control-sm"
                                                value="{{ $bookingGroup->first()->check_in ?? date('Y-m-d') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" id="end_date"
                                                class="form-control form-control-sm"
                                                value="{{ $bookingGroup->last()->check_out ?? now()->addDays(29)->format('Y-m-d') }}">
                                        </div>
                                        <button type="button" id="searchBtn" class="btn btn-sm btn-primary btn-clean">
                                            Search
                                        </button>
                                    </div>
                                </div>

                                <div class="room-scroll">
                                    <div id="room_list_container">
                                        <!-- Rooms loaded dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: BOOKING + CUSTOMER --}}
                        <div class="col-12 col-lg-6">
                            <form method="POST" action="{{ route('booking.update1', $bookingGroup->first()->booking_no) }}" id="editBookingForm">
                                @csrf
                                @method('PATCH')

                                {{-- Booking Details --}}
                                <div class="card-clean mb-6">
                                    <div class="card-head">
                                        <div>
                                            <p class="section-title">Booking Details</p>
                                            <p class="section-subtitle">Review selected rooms and adjust dates.</p>
                                        </div>
                                        <div class="hint">Tip: Check-out must be after check-in.</div>
                                    </div>
                                    <div class="form-block">
                                        <div class="table-wrap">
                                            <table class="table table-striped table-bordered table-clean" id="booking_table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Room No</th>
                                                        <th>Price/Night</th>
                                                        <th>Check In</th>
                                                        <th>Check Out</th>
                                                        <th>Days</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bookingGroup as $booking)
                                                    <tr data-room-id="{{ $booking->room_id }}">
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
                                                        <td class="day-count fw-bold text-center">
                                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) + 1 }}
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger remove-room">Remove</button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Customer & Guest Details --}}
                                <div class="card-clean mb-6">
                                    <div class="card-head">
                                        <div>
                                            <p class="section-title">Customer & Guest Details</p>
                                            <p class="section-subtitle">Update customer info and guest list.</p>
                                        </div>
                                    </div>
                                    <div class="form-block">

                                        {{-- Customer Info --}}
                                        <p class="hint mb-3" style="font-weight:600;color:var(--text);">Customer Information</p>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label">NID</label>
                                                <input type="text" name="nid_number"
                                                    class="form-control form-control-sm"
                                                    value="{{ $firstBooking->customer->nid_number }}">
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_name"
                                                    class="form-control form-control-sm fw-semibold"
                                                    value="{{ $firstBooking->customer->customer_name }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_mobile"
                                                    class="form-control form-control-sm"
                                                    value="{{ $firstBooking->customer->customer_mobile }}" required>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_address"
                                                    class="form-control form-control-sm"
                                                    value="{{ $firstBooking->customer->customer_address }}" required>
                                            </div>
                                            <div class="col-12 d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="as_guest" value="1" id="asGuestSwitch"
                                                        {{ isset($selfGuest) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="asGuestSwitch">
                                                        Add as Guest (Self)
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary btn-clean"
                                                    data-bs-toggle="modal" data-bs-target="#organizationModal">
                                                    + Organization Info
                                                </button>
                                            </div>
                                        </div>

                                        <hr>

                                        {{-- Guest Section --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <p class="hint mb-0" style="font-weight:600;color:var(--text);">Guest Information</p>
                                            <button type="button" id="addGuest" class="btn btn-sm btn-success btn-clean">
                                                + Add Guest
                                            </button>
                                        </div>

                                        <div id="guest-container">
                                            @foreach($guests ?? [] as $index => $guest)
                                            <div class="guest-row border rounded p-3 mb-3" style="background:#f9fafb;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-semibold" style="color:var(--primary);">Guest {{ $index + 1 }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-danger removeGuest">Remove</button>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="guests[{{ $index }}][nid]"
                                                            value="{{ $guest->nid }}" placeholder="NID"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="guests[{{ $index }}][name]"
                                                            value="{{ $guest->name }}" placeholder="Name"
                                                            class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="guests[{{ $index }}][mobile]"
                                                            value="{{ $guest->mobile }}" placeholder="Mobile"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="guests[{{ $index }}][address]"
                                                            value="{{ $guest->address }}" placeholder="Address"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="guests[{{ $index }}][relation]"
                                                            value="{{ $guest->relation }}" placeholder="Relation"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>

                                {{-- Payment Summary --}}
                                <div class="card-clean mb-6">
                                    <div class="card-head">
                                        <div>
                                            <p class="section-title">Payment Summary</p>
                                            <p class="section-subtitle">Auto calculated from selected rooms</p>
                                        </div>
                                        <div class="hint">Updates automatically</div>
                                    </div>
                                    <div class="form-block">
                                        <div class="row g-3">

                                            <div class="col-md-9 d-flex align-items-center">
                                                <span class="hint fw-semibold" style="color:var(--text);">Total</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" id="grand_total"
                                                    class="form-control form-control-sm fw-bold text-end"
                                                    value="{{ $totalAmount ?? 0 }}" readonly>
                                            </div>

                                            <div class="col-md-9 d-flex align-items-center">
                                                <span class="hint">Discount</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" id="discount_amount" name="discount_amount"
                                                    class="form-control form-control-sm text-end"
                                                    value="{{ $discount ?? 0 }}" min="0">
                                            </div>

                                            <div class="col-md-9 d-flex align-items-center">
                                                <span class="hint fw-semibold" style="color:var(--text);">After Discount</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" id="total_price"
                                                    class="form-control form-control-sm fw-bold text-end"
                                                    value="{{ $totalAmount - $discount ?? 0 }}" readonly>
                                            </div>

                                            <div class="col-12"><hr class="my-1"></div>

                                            <div class="col-md-9 d-flex align-items-center">
                                                <label class="form-label mb-0">Paid Amount <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" id="paid_amount" name="paid_amount"
                                                    class="form-control form-control-sm text-end"
                                                    value="{{ $totalPaid ?? 0 }}">
                                            </div>

                                            <div class="col-12"><hr class="my-1"></div>

                                            <div class="col-md-9 d-flex align-items-center">
                                                <span class="hint fw-bold" style="font-size:14px;color:var(--danger);">Due</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" id="due_amount"
                                                    class="form-control form-control-sm fw-bold text-danger text-end"
                                                    value="{{ ($totalAmount - $discount) - $totalPaid ?? 0 }}" readonly>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- Sticky Action Bar --}}
                                <div class="sticky-actions mt-6">
                                    <div class="action-bar">
                                        <a href="{{ route('booking.index') }}" class="btn btn-sm btn-success btn-clean">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-sm btn-primary btn-clean">
                                            Update Booking
                                        </button>
                                    </div>
                                </div>

                                {{-- Organization Modal --}}
                                <div class="modal fade" id="organizationModal" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Organization Information</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Organization Name</label>
                                                    <input type="text" name="organization_name"
                                                        value="{{ $firstBooking->customer->organization_name }}"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Organization Email</label>
                                                    <input type="email" name="organization_email"
                                                        value="{{ $firstBooking->customer->organization_email }}"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Organization Mobile</label>
                                                    <input type="text" name="organization_mobile"
                                                        value="{{ $firstBooking->customer->organization_mobile }}"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        {{-- RIGHT END --}}

                    </div><!-- two-col-stack -->
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
        let end   = $('#end_date').val();
        let url   = '{{ route("booking_search", ["startDate" => ":startDate", "endDate" => ":endDate"]) }}';
        url = url.replace(':startDate', start).replace(':endDate', end);

        $.get(url, function(data){
            $('#room_list_container').empty();
            if(data.length){
                let displayedDates = new Set();
                let $currentGrid = null;

                data.forEach(d => {
                    if(!displayedDates.has(d.date)){
                        displayedDates.add(d.date);
                        const dateObj = new Date(d.date + "T00:00:00");
                        const formatted = dateObj.toLocaleDateString('en-US', { day:'numeric', month:'long', year:'numeric' });

                        $('#room_list_container').append(`
                            <div class="date-header"><h4>${formatted}</h4></div>
                            <div class="room-grid" data-date="${d.date}"></div>
                        `);
                        $currentGrid = $(`.room-grid[data-date="${d.date}"]`);
                    }

                    const isBooked = d.is_booked === 'Booked';
                    const chip = `
                        <button type="button"
                            class="room-chip ${isBooked ? 'is-booked' : ''}"
                            ${isBooked ? 'disabled' : ''}
                            data-room-id="${d.room_id}"
                            data-room-number="${d.room_number}"
                            data-price="${d.price_per_night}"
                            data-date="${d.date}"
                            data-check-in="${d.date}"
                            data-check-out="${d.date}">
                            ${d.room_number}
                        </button>`;
                    $currentGrid.append(chip);
                });

                // Highlight existing bookings
                @foreach($bookingGroup as $booking)
                    highlightRoomDates({{ $booking->room_id }}, '{{ $booking->check_in }}', '{{ $booking->check_out }}');
                @endforeach

            } else {
                $('#room_list_container').html('<p class="text-center text-muted mt-3">No rooms found.</p>');
            }
        });
    }

    // Room chip click → add to table
    $(document).on('click', '.room-chip:not(.is-booked)', function(){
        let roomId     = $(this).data('room-id');
        let roomNumber = $(this).data('room-number');
        let price      = $(this).data('price');
        let checkIn    = $(this).data('check-in');

        // checkOut = checkIn + 1 day
        let checkInDate  = new Date(checkIn + "T00:00:00");
        let checkOutDate = new Date(checkInDate);
        checkOutDate.setDate(checkOutDate.getDate() + 1);
        let checkOut = checkOutDate.toISOString().split('T')[0];

        // Prevent duplicates
        if($('#booking_table tbody tr[data-room-id="'+roomId+'"]').length) return;

        $('#booking_table tbody').append(`
            <tr data-room-id="${roomId}">
                <td>${roomId}<input type="hidden" name="table_room_id[]" value="${roomId}"></td>
                <td>${roomNumber}<input type="hidden" name="table_room_number[]" value="${roomNumber}"></td>
                <td><input type="number" class="form-control form-control-sm room-price" name="table_room_price[]" value="${price}" min="0"></td>
                <td><input type="date" class="form-control form-control-sm check-in-date" name="table_check_in_date[]" value="${checkIn}"></td>
                <td><input type="date" class="form-control form-control-sm check-out-date" name="table_check_out_date[]" value="${checkOut}"></td>
                <td class="day-count fw-bold text-center">1</td>
                <td><button type="button" class="btn btn-sm btn-danger remove-room">Remove</button></td>
            </tr>
        `);

        // Mark chip selected
        // Mark only this specific chip selected (by room-id AND date)
$(`.room-chip[data-room-id="${roomId}"][data-date="${checkIn}"]`).addClass('is-selected');

        updateDaysAndTotal();
        highlightRoomDates(roomId, checkIn, checkOut);
    });

    // Remove room row
    $(document).on('click', '.remove-room', function(){
        let roomId = $(this).closest('tr').data('room-id');
        $(this).closest('tr').remove();
        $(`.room-chip[data-room-id="${roomId}"]`).removeClass('is-selected');
        updateDaysAndTotal();
    });

    // Recalc on input
    $(document).on('input change', '.room-price, .check-in-date, .check-out-date', function(){
        updateDaysAndTotal();
    });

    function updateDaysAndTotal(){
        let total = 0;
        $('#booking_table tbody tr').each(function(){
            let row      = $(this);
            let checkIn  = new Date(row.find('.check-in-date').val());
            let checkOut = new Date(row.find('.check-out-date').val());
            if(!row.find('.check-in-date').val() || !row.find('.check-out-date').val()) return;

            let days = Math.max(1, Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)));
            row.find('.day-count').text(days);

            let price = parseFloat(row.find('.room-price').val()) || 0;
            total += price * days;
        });

        $('#grand_total').val(total.toFixed(2));

        let discount = parseFloat($('#discount_amount').val()) || 0;
        if(discount > total) discount = total;

        let netTotal = total - discount;
        let paid     = parseFloat($('#paid_amount').val()) || 0;
        let due      = netTotal - paid;

        $('#total_price').val(netTotal.toFixed(2));
        $('#due_amount').val(due < 0 ? Math.abs(due).toFixed(2) : due.toFixed(2));
    }

    // Highlight helpers
    function getDatesBetween(start, end){
        let dates   = [];
        let current = new Date(start);
        let last    = new Date(end);
        while(current < last){
            dates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
        }
        return dates;
    }

    function highlightRoomDates(roomId, start, end){
        let dates = getDatesBetween(start, end);
        dates.forEach(date => {
            $(`.room-chip[data-room-id="${roomId}"][data-date="${date}"]`)
                .addClass('is-selected');
        });
    }

    // Discount / paid change
    $('#discount_amount, #paid_amount').on('input', updateDaysAndTotal);

    // Init
    updateDaysAndTotal();
});

/* ── Guest ── */
let guestIndex = {{ isset($guests) ? count($guests) : 0 }};

document.getElementById('addGuest').addEventListener('click', function(){
    let index = guestIndex++;
    let html = `
        <div class="guest-row border rounded p-3 mb-3" style="background:#f9fafb;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold" style="color:var(--primary);">Guest ${index + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger removeGuest">Remove</button>
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="guests[${index}][nid]" placeholder="NID" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <input type="text" name="guests[${index}][name]" placeholder="Name" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="guests[${index}][mobile]" placeholder="Mobile" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <input type="text" name="guests[${index}][address]" placeholder="Address" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <input type="text" name="guests[${index}][relation]" placeholder="Relation" class="form-control form-control-sm">
                </div>
            </div>
        </div>`;
    document.getElementById('guest-container').insertAdjacentHTML('beforeend', html);
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeGuest')){
        e.target.closest('.guest-row').remove();
        let rows = document.querySelectorAll('.guest-row');
        rows.forEach((row, index) => {
            row.querySelector('span.fw-semibold').innerText = `Guest ${index + 1}`;
            row.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/guests\[\d+\]/, `guests[${index}]`);
            });
        });
        guestIndex = rows.length;
    }
});

/* ── Guest NID autofill ── */
document.addEventListener('input', function(e){
    if(!e.target.name) return;
    if(!e.target.name.includes('[nid]')) return;

    let parent = e.target.closest('.guest-row');
    if(!parent) return;

    let value      = e.target.value.trim();
    const nameEl   = parent.querySelector('[name*="[name]"]');
    const mobileEl = parent.querySelector('[name*="[mobile]"]');
    const nidEl    = parent.querySelector('[name*="[nid]"]');
    const addressEl= parent.querySelector('[name*="[address]"]');
    const relationEl=parent.querySelector('[name*="[relation]"]');

    if(value.length === 0){ parent.dataset.autofilled = "0"; return; }
    if(value.length < 6) return;

    fetch(`/person/search?query=${encodeURIComponent(value)}`)
        .then(res => res.json())
        .then(data => {
            if(!data || !data.source) return;
            let result = null;
            if(data.source === 'guest')    result = data.data;
            else if(data.source === 'customer') result = data.data;
            else if(data.source === 'both') result = data.guest ?? data.customer;

            if(result){
                nameEl.value    = result.name || result.customer_name || '';
                mobileEl.value  = result.mobile || result.customer_mobile || '';
                nidEl.value     = result.nid || result.nid_number || '';
                addressEl.value = result.address || result.customer_address || '';
                relationEl.value= result.relation || '';
                parent.dataset.autofilled = "1";
            }
        })
        .catch(() => {});
});

/* ── Customer NID autofill ── */
function fetchCustomerData(value){
    value = value ? value.trim() : '';
    const nameEl   = document.querySelector('[name="customer_name"]');
    const addressEl= document.querySelector('[name="customer_address"]');
    const mobileEl = document.querySelector('[name="customer_mobile"]');

    if(value.length === 0 || value.length < 6){
        if(value.length === 0){ nameEl.value=''; addressEl.value=''; mobileEl.value=''; }
        return;
    }

    fetch(`/person/search?query=${encodeURIComponent(value)}`)
        .then(res => res.json())
        .then(data => {
            let result = null;
            if(data?.source === 'customer') result = data.data;
            else if(data?.source === 'guest') result = data.data;
            else if(data?.source === 'both') result = data.customer;
            if(result){
                nameEl.value   = result.customer_name || result.name || '';
                addressEl.value= result.customer_address || result.address || '';
                mobileEl.value = result.customer_mobile || result.mobile || '';
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.guest-row').forEach((row, index) => {
        row.querySelector('span.fw-semibold').innerText = `Guest ${index + 1}`;
    });
});
</script>