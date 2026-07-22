<x-default-layout>
<div class="app-content flex-column-fluid">
    <div class="app-container">

        <form method="POST" action="{{ route('booking.store') }}">
        @csrf
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <div class="row">
            {{-- Left Column: Room Details --}}
            <div class="col-md-7">
                <div class="card mb-4 p-4 shadow-sm border">
                    <h5 class="mb-3">Room Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                       <tbody>
    @foreach($rooms as $roomId => $info)
        <input type="hidden" name="rooms[{{ $roomId }}][id]" value="{{ $roomId }}">
        <input type="hidden" name="rooms[{{ $roomId }}][price_per_night]" value="{{ $info['price'] }}" class="room-price">

        <tr>
            <td>Room {{ $info['room_number'] ?? $roomId }}</td>
            <td>
                <input type="date" name="rooms[{{ $roomId }}][date]" class="form-control form-control-sm" value="{{ $info['date'] }}">
            </td>
            <td>
                <input type="time" name="rooms[{{ $roomId }}][start]" class="form-control form-control-sm" value="{{ $info['start'] }}">
            </td>
            <td>
                <input type="time" name="rooms[{{ $roomId }}][end]" class="form-control form-control-sm" value="{{ $info['end'] }}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" value="{{ $info['price'] }}" readonly>
            </td>
        </tr>
    @endforeach
</tbody>
                    </table>
                </div>
            </div>

            {{-- Right Column: Customer Info + Payment Summary --}}
            <div class="col-md-5">
                {{-- Customer Info --}}
                <div class="card mb-4 p-4 shadow-sm border ">
                    <div class="row">
                    <h5 class="mb-3">Customer Information</h5>
                    <div class="mb-3 col-md-5">
                        <label class="form-label">Customer NID/Passport</label>
                        <input type="text" oninput="fetchCustomerData(this.value)" name="customer_nid"  class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3 col-md-7">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control form-control-sm" required>
                    </div>
                     <div class="mb-3 col-md-5">
                        <label class="form-label">Mobile</label>
                        <input type="text"   name="customer_mobile" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3 col-md-7">
                        <label class="form-label">Customer Address</label>
                        <input type="text" name="customer_address" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3 col-md-12">
    <div class="d-flex justify-content-between align-items-center">

        <div class="form-check">
            <input type="checkbox"
                   class="form-check-input"
                   id="as_guest"
                   name="as_guest"
                   value="1">

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
                    <hr>
                <h6>Guest Information</h6>

                <div id="guest-container"></div>

                <button type="button" id="addGuest" class="btn btn-sm btn-success mt-2">
                    + Add Guest
                </button>
                </div>

                {{-- Payment Summary --}}
                <div class="card mb-4 p-4 shadow-sm border">
                    <h5 class="mb-3">Payment Summary</h5>
                    <div class="mb-3">
                        <label>Net Total</label>
                        <input type="text" id="net_total" class="form-control form-control-sm" readonly value="0">
                    </div>
                    <div class="mb-3">
                        <label>Discount</label>
                        <input type="number" id="total_discount" name="total_discount" class="form-control form-control-sm" value="0" min="0">
                    </div>
                   <div class="mb-3">
    <label>After Discount</label>
    <input type="text" id="after_discount" name="after_discount" class="form-control form-control-sm" readonly value="0">
</div>

                    <div class="mb-3">
                        <label>Paid</label>
                        <input type="number" id="total_paid" name="total_paid" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label>Due</label>
                        <input type="text" id="due_amount" class="form-control form-control-sm" readonly value="0">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-sm">Confirm Booking</button>
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
                           class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Email
                    </label>

                    <input type="email"
                           name="organization_email"
                           class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Organization Mobile
                    </label>

                    <input type="text"
                           name="organization_mobile"
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

{{-- JS for summary calculations --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    function calculateSummary() {
        let netTotal = 0;

        document.querySelectorAll('.room-price').forEach(el => {
            let val = parseFloat(el.value);
            if (!isNaN(val)) netTotal += val;
        });

        let discount = parseFloat(document.getElementById('total_discount').value) || 0;
        let paid = parseFloat(document.getElementById('total_paid').value) || 0;

        let afterDiscount = netTotal - discount;
        let due = afterDiscount - paid;

        document.getElementById('net_total').value = netTotal.toFixed(2);
        document.getElementById('after_discount').value = afterDiscount.toFixed(2);
        document.getElementById('due_amount').value = due.toFixed(2);
    }

    document.getElementById('total_discount').addEventListener('input', calculateSummary);
    document.getElementById('total_paid').addEventListener('input', calculateSummary);

    calculateSummary();
});

let guestIndex = 0;

document.getElementById('addGuest').addEventListener('click', function () {

    guestIndex++;

    let html = `
        <div class="guest-row border p-2 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Guest ${guestIndex}</strong>
                <button type="button" class="btn btn-danger btn-sm removeGuest">Remove</button>
            </div>

            <div class="row">
            <div class="col-md-6">
                    <label>NID/Passport</label>
                    <input type="text" name="guests[${guestIndex}][nid]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label>Name</label>
                    <input type="text" name="guests[${guestIndex}][name]" class="form-control form-control-sm" required>
                </div>
                
                <div class="col-md-6 mt-2">
                    <label>Mobile</label>
                    <input type="text" name="guests[${guestIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mt-2">
                    <label>Address</label>
                    <input type="text" name="guests[${guestIndex}][address]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mt-2">
                    <label>Relation</label>
                    <input type="text" name="guests[${guestIndex}][relation]" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    `;

    document.getElementById('guest-container').insertAdjacentHTML('beforeend', html);
});


// Remove guest + re-number
document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeGuest')){
        e.target.closest('.guest-row').remove();

        // Re-number guests
        let rows = document.querySelectorAll('.guest-row');
        guestIndex = 0;

        rows.forEach((row, index) => {
            guestIndex++;

            row.querySelector('strong').innerText = `Guest ${guestIndex}`;

            row.querySelectorAll('input').forEach(input => {
                let name = input.name;
                input.name = name.replace(/guests\[\d+\]/, `guests[${guestIndex}]`);
            });
        });
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

    if (e.target.name.includes('[nid]')) {

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
// document.getElementById('as_guest').addEventListener('change', function () {
//     if (this.checked) {
//         alert('Customer will be added as Guest (Self)');
//     }
// });
</script>


</x-default-layout>
