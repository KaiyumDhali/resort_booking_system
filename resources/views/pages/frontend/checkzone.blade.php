@extends('pages.frontend.layouts.app')
@section('content')

{{-- ================= DATA ================= --}}
<script>
    const spots = @json($spots);
    const commonFacilities = @json($commonFacilities);
    const packages = @json($packages);
    const bookings = @json($calendarData);
    const bookedSpots = @json($bookedSpots);
    const MAX_DISCOUNT = {{ $discountLimit }};
    const MULTIPLE_SPOT_DISCOUNT = {{ $multipleSpotDiscountLimit }};
    const allServices = @json($additionalServices);
</script>
<section class="bg-white">
<form action="{{ route('spot.store.1') }}" method="POST">
@csrf

<div class="booking-wrap container ">

    {{-- ================= HEADER (Shadcn Style) ================= --}}
    <div class="booking-header d-flex align-items-center justify-content-between mb-6">
        <div class="d-flex align-items-center gap-4">
            <div class="booking-icon">
                <i class="fa fa-calendar-check text-white fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">Create Spot Booking</h2>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary back-btn" aria-label="Go back">
            <i class="fa fa-arrow-left"></i>
            <span class="back-text">Back</span>
        </a>
    </div>

    {{-- ================= CUSTOMER INFO CARD ================= --}}
    <div class="shad-card mb-6 d-none">
        <div class="shad-body">
            <div class="row g-5">

                {{-- Customer --}}
               <div class="col-12 col-md-5">
    <label class="shad-label required">
        <i class="fa fa-user me-2 text-primary"></i>
        Customer Name
    </label>

    <div class="input-group">
        <div class="flex-grow-1">
        <select id="changeCustomer"
        class="form-control shad-input"
        name="customer_id" 
        
        data-control="select2"
        data-placeholder="Select Customer">
    <option></option>
    @foreach ($customerAccounts as $c)
        <option value="{{ $c->id }}"> <!-- এখানে name বা title নয়, ID দিতে হবে -->
            {{ $c->account_name }}
        </option>
    @endforeach
</select>

        </div>

        <button type="button"
                class="btn btn-light-primary"
                data-bs-toggle="modal"
                data-bs-target="#add_customer_modal"
                title="Add Customer">
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>



                {{-- Account --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="shad-label required">
                        <i class="fa fa-credit-card me-2 text-primary"></i>
                        Receive Account Type
                    </label>
                    <select id="changeAccountsName"
                            class="form-control shad-input"
                            name="receive_account"
                             data-control="select2"
                            data-placeholder="Select Payment Account">
                        <option></option>
                        @foreach ($toAccounts as $acc)
                            <option value="11">{{ $acc->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount --}}
                <div class="col-6 col-sm-3 col-md-2">
                    <label class="shad-label required">
                        <i class="fa fa-money-bill me-2 text-primary"></i>
                        Receive Amount
                    </label>
                    <input type="number" min="0" name="receive_amount"
                           class="form-control shad-input"
                           placeholder="Enter amount"
                           value="0">
                </div>

                {{-- Status --}}
                <div class="col-6 col-sm-3 col-md-2">
                    <label class="shad-label">
                        <i class="fa fa-check-circle me-2 text-primary"></i>
                        Status
                    </label>
                    <select name="status" class="form-select shad-input">
                        <option value="1" {{ old('status',1)==1 ? 'selected':'' }}>Confirmed</option>
                        <option value="0" {{ old('status')=='0' ? 'selected':'' }}>Pending</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

<div class="col-md-12 py-5">
    <div class="booking-card">


    <h4 class="mb-4">Fill Your Information</h4>

    <div class="row">

        <div class="col-12 col-md-4 mb-3">
            <label class="form-label text-black">Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <label class="form-label text-black">Mobile</label>
            <input type="text" name="customer_mobile" class="form-control" inputmode="tel" required>
        </div>

        <div class="col-12 col-sm-6 col-md-5 mb-3">
            <label class="form-label text-black">Address</label>
            <input type="text" name="customer_address" class="form-control" required>
        </div>

    </div>

</div>


</div>

    {{-- ================= CALENDAR + SPOTS ================= --}}
    <div class="row g-6 mb-6" style="margin-bottom: 40px;">

        {{-- Calendar --}}
        <div class="col-12 col-lg-4">
            <div class="shad-card h-100">
                <div class="shad-head">
                    <h4 class="fw-semibold mb-0">
                        <i class="fa fa-calendar-days me-2 text-primary"></i>
                        Select Booking Date
                    </h4>
                </div>
                <div class="shad-body">
                    <div class="legend-box mt-5">
                        <div class="legend-title">
                            <i class="fa fa-circle-info me-2"></i> Availability Legend
                        </div>
                        <div class="legend-grid">
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color free"></span> Available
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color partial"></span> Partially Booked
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color full"></span> Fully Booked
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color past"></span> Past Date
                            </div>
                        </div>
                    </div>
                    <div id="spot_booking_calendar"></div>

                    {{-- Legend --}}
                    

                </div>
            </div>
        </div>

        {{-- Spot Selection --}}
        <div class="col-12 col-lg-8">
            <div class="shad-card h-100">
                <div class="shad-head">
                    <h4 class="fw-semibold mb-0">
                        <i class="fa fa-map-pin me-2 text-primary"></i>
                        Select Zone
                        <span id="selected_date_text" class="text-muted small ms-2"></span>
                    </h4>
                </div>

                <div class="shad-body">

                    <div class="booking-card">

                        <div class="zone-info text-center mb-3">
                            Click a zone on the map to select it
                        </div>

                        <div class="map-stage" id="mapStage">
                            <div class="map-wrapper" id="mapWrapper">
                                <img src="{{ asset('images/map/map.png') }}">
                                <div class="map-overlay" id="spot_boxes">
                                    {!! file_get_contents(public_path('images/map/zones.svg')) !!}
                                </div>
                            </div>

                            <div class="map-zoom-controls" role="group" aria-label="Map zoom controls">
                                <button type="button" id="zoomInBtn" aria-label="Zoom in"><i class="fa fa-plus"></i></button>
                                <button type="button" id="zoomOutBtn" aria-label="Zoom out"><i class="fa fa-minus"></i></button>
                                <button type="button" id="zoomResetBtn" aria-label="Reset zoom"><i class="fa fa-rotate-left"></i></button>
                            </div>

                            <div class="map-hint d-lg-none" id="mapHint">
                                <i class="fa fa-hand-pointer me-1"></i> Pinch or use +/− to zoom · drag to pan · tap a zone to select
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>


   {{-- ================= SELECTED PACKAGES ================= --}}

    <div class="shad-card mb-6 d-none" id="selectedPackagesCard">
        <div class="shad-head">
            <h4 class="fw-semibold mb-0">
                <i class="fa fa-box-open me-2 text-primary"></i>
                Selected Spot
            </h4>
        </div>
        <div class="shad-body">
            <div class="row g-4" id="selected_grid"></div>

            <div class="total-row mt-5">
                <span class="text-muted">Spot Total</span>
                <span class="fw-bold text-primary fs-4">৳ <span id="spotTotal">0</span></span>
            </div>

            <input type="hidden" name="booking_date" id="booking_date">
            <input type="hidden" name="items" id="items">
            <input type="hidden" name="total_price" id="total_price">

        </div>
    </div> 

{{-- ================= PACKAGES OF SELECTED SPOT ================= --}}
<div class="shad-card mb-6" id="spotPackagesCard">

    <div class="shad-head">
        <h4 class="fw-semibold mb-0">
            <i class="fa fa-box me-2 text-primary"></i>
            Packages for <span id="spotTitleText"></span>
        </h4>
    </div>
    <div class="shad-body">
        <div class="row g-4" id="spot_packages_grid"></div>
        <div class="total-row mt-5">
                <span class="text-muted">Package Total</span>
                <span class="fw-bold text-primary fs-4">৳ <span id="summaryPackage">0</span></span>
            </div>
    </div>
</div>

<div class="mb-6" style="margin-bottom: 40px;">
    <h4 style="margin-bottom: 20px;">Included Facility</h4>

    <div id="spotFacilitiesContainer">
        {{-- Spot facilities and common facilities will be dynamically injected here --}}
    </div>
</div>
    {{-- ================= SERVICES TOGGLE ================= --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="toggleServices" checked  >
        </div>
        <label for="toggleServices" class="fw-semibold cursor-pointer">
            <i class="fa fa-sparkles me-2 text-warning"></i>
            Add Additional Services
        </label>
    </div>

    {{-- ================= SERVICES CARD ================= --}}
    <div class="shad-card mb-6" id="additionalServicesCard">
        <div class="shad-head">
            <h4 class="fw-semibold mb-0">
                <i class="fa fa-sparkles me-2 text-warning"></i>
                Additional Services
            </h4>
        </div>
        <div class="shad-body">
            <div class="table-responsive">
                <table class="table shad-table align-middle">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th width="120">Price</th>
                            <th width="150">Qty(Per Person)</th>
                            <th width="120">Total</th>
                            <th width="80" class="text-center">Select</th>
                        </tr>
                    </thead>
                    <tbody id="service_tbody">
                    
                    </tbody>
                </table>
                 <div class="total-row mt-5">
                <span class="text-muted">Additional service Total</span>
                <span class="fw-bold text-primary fs-4">৳ <span id="serviceTotalDisplay">0</span></span>
                

            </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="additional_services" id="additional_services">
    <input type="hidden" name="discount_percent" id="discount_percent">
    <input type="hidden" name="spot_discount_percent" id="spot_discount_percent">


  {{-- ================= DISCOUNT + SUMMARY ================= --}}
<div class="shad-card mb-6">
    <div class="shad-body">
        <div class="row g-5 align-items-start">

            {{-- Discount --}}
            <div class="col-12 col-lg-6">
                <!-- can leave blank or put something here if needed -->
            </div>

            {{-- Summary --}}
            <div class="col-12 col-lg-6">
                <!-- Discount Input -->
              

                <!-- Summary Box -->
                <div class="summary-box">

                    <div class="summary-line">
                        <span>Spot Total</span>
                        <span class="fw-semibold">৳ <span id="summarySpot">0</span></span>
                    </div>

                    <div class="summary-line">
                        <span>Package Total</span>
                        <span class="fw-semibold">৳ <span id="Packaget">0</span></span>
                    </div>

                    <div class="summary-line">
                        <span>Service Total</span>
                        <span class="fw-semibold">৳ <span id="summaryService">0</span></span>
                    </div>

                    

                    <div class="summary-total mt-3 py-3 d-none">
                        <span class="">
                          Grand Total
                        </span>
                        <span class="grand-total">৳ <span id="grandTotal1">0</span></span>
                    </div>
                <div class="row">
                 <div class=" d-none text-danger col-12 d-flex align-items-center justify-content-between flex-wrap" id="discountRow">
                    <div>
                        <span>Discount (<span id="discountLabel">0</span>%)</span>
                        <input type="number"
                            id="discountPercent"
                            class="ps-0 ms-2 shad-input-sm1 text-end"
                            value="0"
                            min="0" max="{{ $discountLimit }}">
                    </div>
                    <span id="discountAmountText">৳ 0</span>
                </div>

              
                    <div class="d-none col-12 d-flex justify-content-between">
                        <span class="fw-semibold">After Discount Amount</span>
                        <span class="fw-semibold">৳ <span id="afterDiscountAmount">0</span></span>
                    </div>
                 

                    <div class="d-none summary-line mt-2" id="invoiceAdjustmentRow">
                        <span>Invoice Adjustment Discount</span>
                        <span class="text-end">-৳ 
                            <span id="invoiceAdjustmentAmount" style="cursor:pointer;">0</span>
                            <input type="number"
                                   id="discountAmountInput"
                                   class="shad-input-sm1 d-none text-end"
                                   value="0"
                                   min="0"
                                   disabled>
                        </span>
                    </div>


                    <input type="hidden" name="discount_amount" id="discount_amount" value="0">

 <input type="hidden" 
       name="invoice_adjustment_discount" 
       id="invoice_adjustment_discount" 
       value="0">

                    <div class="summary-total mt-3">
                        <span class=" fw-semibold">
                          Net Payable
                        </span>
                        <span class="grand-total fw-semibold">৳ <span id="grandTotal">0</span></span>
                    </div>

                </div>
                </div>

              
            </div>

        </div>
    </div>
</div>

    {{-- ================= SAVE BUTTON ================= --}}
    <button type="submit" id="saveBookingBtn"
        class="btn shad-save-btn w-100 py-3 fw-bold"
        disabled>
        <i class="fa fa-save me-2 text-white"></i> Save Booking
    </button>

    {{-- ================= MOBILE STICKY CHECKOUT BAR ================= --}}
    <div class="mobile-sticky-bar" id="mobileStickyBar">
        <div class="msb-info">
            <span class="msb-label">Net Payable</span>
            <span class="msb-amount">৳ <span id="mobileGrandTotal">0</span></span>
        </div>
        <button type="submit" id="mobileSaveBtn" class="btn shad-save-btn msb-btn" disabled>
            <i class="fa fa-save me-2"></i> Save Booking
        </button>
    </div>

</div>
</form>


  {{-- add new customer modal --}}
    <div class="modal fade" id="add_customer_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Add Customer</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i style="font-size: 20px" class="fas fa-times"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-5">
                    <form id="new_card_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" method="POST"
                        action="{{ route('customers.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="required form-label">Customer Type</label>
                                        <select class="form-select form-select-sm" data-control="select2"
                                            name="customer_type" required>
                                            <option value="">Select Customer Type</option>
                                            @foreach ($customerTypes as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="required form-label">Customer Code</label>
                                        <input type="text" name="customer_code"
                                            class="form-control form-control-sm mb-2" placeholder="Customer Code"
                                            value="" required>
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="required form-label">Name</label>
                                        <input type="text" name="customer_name"
                                            class="form-control form-control-sm mb-2" placeholder="Customer Name"
                                            value="" required>
                                        <input type="text" class="form-control form-control-sm" name="sumite_type"
                                            value="1" hidden>
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select form-select-sm" data-control="select2" name="customer_gender">
                                            <option value="" selected>--Select Gender--</option>
                                            <option value="1">Male</option>
                                            <option value="0">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="required form-label">Cell Number</label>
                                        <input type="text" name="customer_mobile"
                                            class="form-control form-control-sm mb-2" placeholder="Customer Mobile"
                                            value="" required>
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label"> E-mail </label>
                                        <input type="text" name="customer_email"
                                            class="form-control form-control-sm mb-2" placeholder="Customer Email"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">DOB</label>
                                        <input type="date" name="customer_DOB"
                                            class="form-control form-control-sm mb-2" placeholder="Customer DOB"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">NID Number</label>
                                        <input type="text" name="nid_number"
                                            class="form-control form-control-sm mb-2" placeholder="NID Number"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Vat Reg No</label>
                                        <input type="text" name="vat_reg_no"
                                            class="form-control form-control-sm mb-2" placeholder="Vat Reg No"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label"> e-TIN No </label>
                                        <input type="text" name="tin_no"
                                            class="form-control form-control-sm mb-2" placeholder="Tin No"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Trade License</label>
                                        <input type="text" name="trade_license"
                                            class="form-control form-control-sm mb-2" placeholder="Trade License"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Commission Rate</label>
                                        <input type="text" name="discount_rate"
                                            class="form-control form-control-sm mb-2" placeholder="Discount Rate"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Customer Address</label>
                                        <input type="text" name="customer_address"
                                            class="form-control form-control-sm mb-2" placeholder="Customer Address"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Shipping Address</label>
                                        <input type="text" name="shipping_address"
                                            class="form-control form-control-sm mb-2" placeholder="Shipping Address"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Previous Due</label>
                                        <input type="text" name="is_previous_due"
                                            class="form-control form-control-sm mb-2" placeholder="Previous Due"
                                            value="">
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6">
                                <div class="card-body pt-0 pb-3">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class=" form-label">Status</label>
                                        <select class="form-select form-select-sm" data-control="select2"
                                            name="status">
                                            <option value="">--Select Status--</option>
                                            <option value="1" selected>Active</option>
                                            <option value="0">Disable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="text-center pt-5">
                            <button class="btn btn-sm btn-success me-5" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="new_card_submit" class="btn btn-sm btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
    </div>
</section>
{{-- ================= STYLES (Shadcn look for Blade) ================= --}}
<style>
:root{
    --brand:#1aa782;
    --brand-dark:#148f6e;
    --brand-light:#e6f7f2;
    --ink:#111827;
    --muted:#6b7280;
    --border:#eef0f2;
    --radius-lg:16px;
    --radius-md:12px;
    --radius-sm:8px;
}

.spot-facilities {
    list-style: none; /* default বুলেট বন্ধ */
    padding-left: 0;
    margin: 0;
}

.spot-facilities li {
    margin-bottom: 8px;
    font-size: 14px;
    color: #333;
    display: flex;
    align-items: center;
}

.facility-icon {
    display: inline-block;
    color: #0d6efd; /* icon color */
    margin-right: 8px;
    font-size: 16px;
}
    .input-group .select2-container {
    width: 100% !important;
}

.input-group {
    flex-wrap: nowrap !important;
}

    .spot-btn{
    position:relative;
}
.spot-btn.disabled{
    opacity:.5;
    pointer-events:none;
}
.spot-btn.selected{
    background:#1aa782;
    color:#fff;
    border-color:#1aa782;
}
.spot-btn.selected .pkg-price{
    color:rgba(255,255,255,.85);
}

    /* =========================
   FULLCALENDAR SHADCN THEME
========================= */
#spot_booking_calendar {
    --fc-border-color: #eee;
    --fc-page-bg-color: transparent;
    --fc-today-bg-color: rgba(26,167,130,.08);
    --fc-neutral-bg-color: transparent;
}

/* Toolbar */
.fc .fc-toolbar {
    margin-bottom: 14px !important;
    flex-wrap: wrap;
    gap: 10px;
}
.fc .fc-toolbar-title {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #111827 !important;
}

/* Buttons */
.fc .fc-button {
    background: #fff !important;
    border: 1px solid #e5e7eb !important;
    color: #374151 !important;
    border-radius: 12px !important;
    padding: 6px 12px !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    transition: .2s;
}
.fc .fc-button:hover {
    border-color: rgba(26,167,130,.4) !important;
    color: #1aa782 !important;
}
.fc .fc-button:focus {
    box-shadow: 0 0 0 3px rgba(26,167,130,.12) !important;
}
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background: rgba(26,167,130,.12) !important;
    border-color: rgba(26,167,130,.4) !important;
    color: #1aa782 !important;
}

/* Remove harsh table lines */
.fc-theme-standard td,
.fc-theme-standard th {
    border: none !important;
}

/* Day header row */
.fc .fc-col-header-cell {
    padding: 10px 0 !important;
}
.fc .fc-col-header-cell-cushion {
    color: #6b7280 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    text-transform: uppercase;
}

/* Day cell base */
.fc .fc-daygrid-day-frame {
    padding: 6px !important;
}
.fc .fc-daygrid-day-top {
    justify-content: center !important;
}
.fc .fc-daygrid-day-number {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: #374151 !important;
    margin: 0 auto !important;
    transition: .2s;
}

/* Hover effect */
.fc .fc-daygrid-day:hover .fc-daygrid-day-number {
    background: rgba(26,167,130,.12);
    color: #1aa782 !important;
}
@media (max-width: 768px) {
    .shad-table th,
    .shad-table td {
        white-space: nowrap;          /* text wrap না হওয়া */
        font-size: 14px;              /* mobile-friendly font size */
    }

    .shad-table td input.shad-input-sm,
    .shad-table td .row-total {
        min-width: 60px;              /* ensure price/total দেখায় */
    }

    .shad-table td.qty-wrap input.qty {
        width: 50px;                  /* quantity input ঠিকভাবে দেখাবে */
    }

    .shad-table th:nth-child(2),
    .shad-table td:nth-child(2) {     /* Price column */
        min-width: 120px;
    }

    .shad-table th:nth-child(4),
    .shad-table td:nth-child(4) {     /* Total column */
        min-width: 70px;
    }
}
/* Today highlight */
.fc .fc-day-today .fc-daygrid-day-number {
    background: rgba(26,167,130,.18);
    color: #1aa782 !important;
}

/* Selected day */
/* Selected day - FORCE white text */
.fc .fc-daygrid-day.selected-day .fc-daygrid-day-number{
    background:#1aa782 !important;
    color:#ffffff !important;
    font-weight:800;
}


/* Past date */
/* .fc .fc-daygrid-day.disabled-date .fc-daygrid-day-number {
    background: #f1f5f9 !important;
    color: #9ca3af !important;
} */

/* Status colors */
.fc .fc-daygrid-day.free-date .fc-daygrid-day-number {
    background: rgba(34,197,94,.14);
    color: #16a34a !important;
}
.fc .fc-daygrid-day.partially-booked-date .fc-daygrid-day-number {
    background: rgba(245,158,11,.18);
    color: #b45309 !important;
}
.fc .fc-daygrid-day.fully-booked-date .fc-daygrid-day-number {
    background: rgba(239,68,68,.18);
    color: #dc2626 !important;
}

/* Make calendar container look card-like */
.fc {
    padding: 8px 6px 14px;
}

/* Remove event dot line */
.fc-daygrid-event-dot {
    display: none !important;
}
    .booking-wrap{margin:0 auto; padding-bottom: 24px; padding-top: 180px;}
    .booking-icon{
        width:52px;height:52px;border-radius:14px;
        background:linear-gradient(135deg,#1aa782,#1fc29b);
        display:flex;align-items:center;justify-content:center;
        box-shadow:0 0 20px rgba(26,167,130,.2);
        flex-shrink:0;
    }

    .booking-header{ flex-wrap: wrap; row-gap: 12px; }
    .booking-header h2{ font-size: clamp(18px, 3.6vw, 28px); }
    .back-btn{ display:inline-flex; align-items:center; gap:6px; border-radius:10px; white-space:nowrap; }

    /* Shadcn Card */
    .shad-card{
        background:#fff;border:1px solid #eee;border-radius:16px;
        box-shadow:0 6px 18px rgba(0,0,0,.04);
        overflow:hidden;
        margin-bottom: 40px;
    }
    .shad-head{
        padding:16px 20px;border-bottom:1px solid #f3f3f3;
        background:linear-gradient(180deg,#fff,#fafafa);
    }
    .shad-body{padding:20px;}

    /* inputs */
    .shad-label{font-weight:600;font-size:13px;margin-bottom:8px;display:block;}
    .shad-input{
        height:44px;border-radius:12px;
        border:1px solid #e8e8e8;
        background:#fff;
        padding:10px 12px;
        font-size:15px;
    }
    .shad-input:focus{border-color:#1aa782;box-shadow:0 0 0 3px rgba(26,167,130,.12);}

    .shad-input-sm{
        height:36px;border-radius:10px;border:1px solid #e8e8e8;
    }
    .shad-input-sm1{
        height:28px;border-radius:6px;border:1px solid #e8e8e8;
    }

    /* Touch-friendly inputs everywhere, and avoid iOS auto-zoom-on-focus */
    .booking-wrap input[type="text"],
    .booking-wrap input[type="number"],
    .booking-wrap input[type="tel"],
    .booking-wrap input[type="date"],
    .booking-wrap select,
    .booking-wrap .form-control,
    .booking-wrap .form-select{
        font-size:15px;
    }
    .booking-card .form-control{
        min-height:46px;
        border-radius:10px;
    }

    /* Legend */
    .legend-box{background:#f8fafc;border-radius:14px;padding:14px;border:1px solid #eee;}
    .legend-title{font-weight:600;font-size:13px;margin-bottom:10px;color:#222;}
    .legend-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12px;color:#6b7280;}
    .legend-color{width:16px;height:16px;border-radius:6px;display:inline-block;border:1px solid #ddd;}
    .legend-color.free{background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.25)}
    .legend-color.partial{background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.3)}
    .legend-color.full{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.3)}
    .legend-color.past{background:#f1f5f9;border-color:#e2e8f0}

    /* Fullcalendar day colors */
    /* .fc-daygrid-day.free-date { background:#dcfce7 !important; }
    .fc-daygrid-day.disabled-date { background:#f1f5f9 !important; cursor:not-allowed; opacity:.6; }
    .fc-daygrid-day.partially-booked-date { background:#fef3c7 !important; }
    .fc-daygrid-day.fully-booked-date { background:#fee2e2 !important; } */

    /* spot selection */
    .spot-box{padding:16px;border-radius:16px;border:2px solid #eee;transition:.2s;background:#fff;}
    .spot-box:hover{border-color:rgba(26,167,130,.4);box-shadow:0 10px 20px rgba(0,0,0,.05);}
    .spot-box.active{border-color:#1aa782;background:rgba(26,167,130,.05);box-shadow:0 0 20px rgba(26,167,130,.08);}
    .spot-box.disabled{opacity:.5;cursor:not-allowed;background:#f8fafc;}

    .package-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px;}
    .pkg-btn{
        border:2px solid #eee;border-radius:12px;padding:12px;text-align:center;
        background:linear-gradient(135deg,rgba(26,167,130,.05),rgba(26,167,130,.1));
        cursor:pointer;
        min-height:64px;
    }
    .pkg-btn:hover{border-color:rgba(26,167,130,.4);}
    .pkg-btn.selected{
        background:#1aa782;color:#fff;border-color:#1aa782;
    }
    .pkg-price{font-size:12px;margin-top:6px;font-weight:700;color:#1aa782;}
    .pkg-btn.selected .pkg-price{color:rgba(255,255,255,.85);}

    /* selected packages */
    .selected-card{
        background:linear-gradient(135deg,rgba(26,167,130,.05),rgba(26,167,130,.1));
        border:1px solid rgba(26,167,130,.2);
        padding:16px;border-radius:16px;position:relative;
        height:100%;
    }
    .remove-btn{
        position:absolute;top:8px;right:8px;
        border:none;background:rgba(255,255,255,.9);font-size:14px;
        opacity:1;transition:.2s;color:#ef4444;
        width:26px;height:26px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
    }
    .total-row{display:flex;justify-content:space-between;align-items:center;border-top:1px solid #eee;padding-top:14px;flex-wrap:wrap;gap:8px;}

    /* qty */
    .qty-wrap{display:flex;align-items:center;gap:6px;}
    .qty-wrap input.qty{width:48px;text-align:center;padding:6px 4px;}
    .qty-btn{
        width:34px;height:34px;border-radius:10px;border:1px solid #eee;
        background:#fff;font-weight:800;cursor:pointer;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
    }
    .qty-btn:active{background:var(--brand-light);}

    /* summary */
    .summary-box{
        padding:16px;border-radius:16px;background:#f8fafc;border:1px solid #eee;
    }
    .summary-line{display:flex;justify-content:space-between;font-size:13px;margin-bottom:10px;color:#6b7280;gap:10px;flex-wrap:wrap;}
    .summary-total{
        border-top:1px solid #eee;padding-top:12px;margin-top:12px;
        display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
    }
   
    .percent-sign{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#888;font-weight:600;}

    /* save button */
    .shad-save-btn{
        background:linear-gradient(135deg,#1aa782,#1fc29b);
        color:white;border-radius:14px;font-size:16px;
        box-shadow:0 12px 20px rgba(26,167,130,.18);
        border:none;
    }
    .shad-save-btn:disabled{opacity:.5;cursor:not-allowed;}

    .spot-check{
    position:absolute;
    top:12px;
    right:12px;
    width:26px;
    height:26px;
    border-radius:50%;
    background:#1aa782;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    font-weight:900;
    box-shadow:0 4px 10px rgba(26,167,130,.4);
}

.spot-box{
    position:relative;
}

.spot-box.has-package{
    border-color:#1aa782;
    background:rgba(26,167,130,.05);
}
/* =============================
   FORCE selected date text WHITE
============================= */

/* number link */
.fc .fc-daygrid-day.selected-day .fc-daygrid-day-number,
.fc .fc-daygrid-day.selected-day .fc-daygrid-day-number a{
    background:#1aa782 !important;
    color:#ffffff !important;
    font-weight:800 !important;
}

/* hover হলেও white থাকবে */
.fc .fc-daygrid-day.selected-day:hover .fc-daygrid-day-number,
.fc .fc-daygrid-day.selected-day:hover .fc-daygrid-day-number a{
    background:#1aa782 !important;
    color:#ffffff !important;
}

/* today + selected conflict fix */
.fc .fc-day-today.selected-day .fc-daygrid-day-number,
.fc .fc-day-today.selected-day .fc-daygrid-day-number a{
    background:#1aa782 !important;
    color:#ffffff !important;
}

/* ================= MAP STAGE / ZOOM ================= */
.map-stage{
    position:relative;
    width:100%;
    overflow:hidden;
    border-radius:16px;
    border:1px solid var(--border);
    touch-action:none;
}
.map-wrapper {
    position: relative;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    touch-action: none; /* mobile gestures */
    cursor: grab;
}
.map-wrapper img {
    width: 100%;
    display: block;
    user-select: none;
    pointer-events: none;
}
.map-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: all;
}

.map-overlay svg{
    width:100%;
    height:100%;
}

.map-overlay path{
    fill: transparent;
    stroke: transparent;
    stroke-width:2;
    cursor:pointer;
    transition:.3s;
}

/* hover */
.map-overlay path:hover{
    fill: rgba(255, 204, 0, 0.7);
}

/* selected */
.map-overlay path.selected{
    fill: rgba(255, 204, 0, 0.7);
    stroke:#1aa782;
}

/* booked zone */
.map-overlay path.disabled{
    fill: rgba(255,0,0,0.5);
    
    pointer-events:none;
}

.map-zoom-controls{
    position:absolute;
    right:12px;
    bottom:12px;
    z-index:5;
    display:flex;
    flex-direction:column;
    gap:8px;
    background:rgba(255,255,255,.9);
    padding:6px;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,.12);
    border:1px solid var(--border);
}
.map-zoom-controls button{
    width:38px;height:38px;border-radius:10px;border:1px solid var(--border);
    background:#fff;color:var(--ink);font-size:15px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    transition:.2s;
}
.map-zoom-controls button:hover{background:var(--brand-light);color:var(--brand-dark);border-color:var(--brand);}
.map-zoom-controls button:active{transform:scale(.92);}

.map-hint{
    position:absolute;
    top:10px;
    left:10px;
    right:10px;
    z-index:5;
    background:rgba(17,24,39,.78);
    color:#fff;
    font-size:11.5px;
    padding:8px 12px;
    border-radius:999px;
    text-align:center;
    pointer-events:none;
    line-height:1.3;
}

/* ================= MOBILE STICKY CHECKOUT BAR ================= */
.mobile-sticky-bar{
    display:none;
    position:fixed;
    left:0;right:0;bottom:0;
    z-index:1030;
    background:#fff;
    border-top:1px solid var(--border);
    box-shadow:0 -10px 28px rgba(0,0,0,.10);
    padding:10px 16px;
    padding-bottom:calc(10px + env(safe-area-inset-bottom));
    align-items:center;
    justify-content:space-between;
    gap:14px;
}
.msb-info{display:flex;flex-direction:column;line-height:1.2;}
.msb-label{font-size:10.5px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:700;}
.msb-amount{font-size:19px;font-weight:800;color:var(--brand-dark);}
.msb-btn{
    width:auto;flex-shrink:0;
    padding:12px 22px;border-radius:12px;font-size:14px;
}

/* Accessible focus ring everywhere */
a:focus-visible,
button:focus-visible,
input:focus-visible,
select:focus-visible,
.pkg-btn:focus-visible,
.map-overlay path:focus-visible{
    outline:3px solid rgba(26,167,130,.5);
    outline-offset:2px;
}

@media (prefers-reduced-motion: reduce){
    .spot-box, .pkg-btn, .qty-btn, .map-zoom-controls button, .fc .fc-button, .fc .fc-daygrid-day-number{
        transition:none !important;
    }
}

/* ================= RESPONSIVE: TABLET / LAPTOP ================= */
@media (max-width:991px){
    .booking-wrap{padding-top:140px;}
    #saveBookingBtn{display:none;}
    .mobile-sticky-bar{display:flex;}
    .booking-wrap{padding-bottom:96px;}
}

/* ================= RESPONSIVE: PHONES ================= */
@media (max-width:576px){
    .booking-wrap{padding-top:108px;}
    .shad-body{padding:14px;}
    .shad-head{padding:12px 14px;}
    .shad-head h4{font-size:15px;}
    .booking-card h4{font-size:16px;}
    .booking-icon{width:42px;height:42px;border-radius:12px;}
    .back-btn{padding:9px 12px;}
    .package-grid{grid-template-columns:repeat(2,1fr);}
    .mb-6{margin-bottom:24px !important;}
    .map-zoom-controls{right:8px;bottom:8px;}
    .map-zoom-controls button{width:36px;height:36px;}
    .shad-input-sm1{height:32px;font-size:13px;}
}

@media (max-width:400px){
    .back-text{display:none;}
}

/* ================= RESPONSIVE SERVICE TABLE -> CARDS ================= */
@media (max-width:680px){
    #additionalServicesCard .table-responsive{overflow:visible;}
    .shad-table thead{display:none;}
    .shad-table, .shad-table tbody{display:block;width:100%;}
    .shad-table tr.service-row{
        display:block;
        border:1px solid var(--border);
        border-radius:14px;
        margin-bottom:12px;
        padding:12px 14px;
        background:#fff;
    }
    .shad-table tr:not(.service-row){display:block;}
    .shad-table td{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:7px 0;
        border:none !important;
        width:100% !important;
        white-space:normal !important;
        min-width:0 !important;
    }
    .shad-table td[data-label]::before{
        content:attr(data-label);
        font-weight:600;
        color:var(--muted);
        font-size:11.5px;
        text-transform:uppercase;
        letter-spacing:.03em;
        margin-right:10px;
        flex-shrink:0;
    }
    .shad-table td .form-control{
        max-width:140px;
        margin-left:auto;
    }
    .shad-table td .service-check{
        width:22px;height:22px;
    }
}

.modal-dialog.mw-650px{max-width:650px;}
@media (max-width:576px){
    .modal-dialog.mw-650px{max-width:calc(100% - 1rem);margin:.5rem auto;}
    #add_customer_modal .modal-body{padding:16px !important;}
}
</style>

{{-- Fullcalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

{{-- ================= SCRIPT LOGIC ================= --}}

<script>
/* ================= GLOBAL ================= */
let selectedSpots = [];
let tickedPackage = null;
let totalCapacity = 0;
let serviceTotal = 0;
let discountPercent = 0;
let manualDiscountAmount = 0;
let useManualDiscount = false;
function round2(num){
    return Math.round((Number(num) + Number.EPSILON) * 100) / 100;
}


function getSpotDiscountPercent(){
    const spotCount = selectedSpots.length;

    if (spotCount <= 1) return 0;

    const effectiveSpotCount = Math.min(spotCount, 4); // 👈 cap at 4
    return effectiveSpotCount * MULTIPLE_SPOT_DISCOUNT;
}

function formatMoney(num){
    return (num || 0).toLocaleString();
}

/* ================= CALENDAR ================= */
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('discount_percent').value = 0;

    const isMobile = window.innerWidth < 576;

    const calendar = new FullCalendar.Calendar(
        document.getElementById('spot_booking_calendar'), {
        initialView:'dayGridMonth',
        height: isMobile ? 'auto' : 480,
        headerToolbar:{
            left:'prev',
            center:'title',
            right:'next'
        },

        dayCellClassNames(arg){
            const d = arg.date;
            const today = new Date(); today.setHours(0,0,0,0);
             const dateStr = d.getFullYear() + '-' +
        String(d.getMonth() + 1).padStart(2, '0') + '-' +
        String(d.getDate()).padStart(2, '0');

             if(d < today) return ['disabled-date'];
            if(bookings[dateStr]){
                const total = spots.length;
                const booked = bookings[dateStr].booked;
                if(booked >= total) return ['fully-booked-date'];
                if(booked > 0) return ['partially-booked-date'];
            }
            return ['free-date'];
        },

        dateClick(info){
            const d = info.date;
            const today = new Date(); today.setHours(0,0,0,0);
             if(d < today){ alert('Past date not allowed'); return; }

            document.querySelectorAll('.fc-daygrid-day')
                .forEach(x=>x.classList.remove('selected-day'));
            info.dayEl.classList.add('selected-day');

        const dateStr = d.getFullYear() + '-' +
    String(d.getMonth() + 1).padStart(2, '0') + '-' +
    String(d.getDate()).padStart(2, '0');

            document.getElementById('booking_date').value = dateStr;
            document.getElementById('selected_date_text').innerText = `(${dateStr})`;

            renderSpots(dateStr);
        }
    });

    calendar.render();
    bindTopValidation();
    initServiceEvents();
    initDiscountEvents();
    initToggleServices();
     initManualDiscountEvents(); 
     renderSpotCapacities(); 
});

/* ================= SPOTS ================= */
function renderSpots(dateStr){

    const totalSpots  = spots.length;
    const bookedCount = bookings[dateStr]?.booked || 0;
    const isFullyBooked = bookedCount >= totalSpots;

    const bookedSpotIds = Array.isArray(bookedSpots[dateStr])
        ? bookedSpots[dateStr].map(id => parseInt(id))
        : [];

    spots.forEach(spot=>{

        const path = document.getElementById('zone-'+spot.id);
        if(!path) return;

        path.classList.remove('selected','disabled');

        const isBookedSpot =
            isFullyBooked ||
            bookedSpotIds.includes(parseInt(spot.id));

        if(isBookedSpot){
            path.classList.add('disabled');
        }

        if(selectedSpots.find(s=>s.id===spot.id)){
            path.classList.add('selected');
        }

        path.onclick = () => {

            if(path.classList.contains('disabled')) return;

            if(path.classList.contains('selected')){

                path.classList.remove('selected');
                removeSpot(spot.id);

            }else{

                path.classList.add('selected');
                addSpot(spot);

            }

        };

    });

}

/* ================= SPOT HANDLING ================= */
function addSpot(spot){
    if(selectedSpots.find(s=>s.id===spot.id)) return;

    selectedSpots.push({
        id: spot.id,
        title: spot.title,
        price: parseFloat(spot.price),
        max_capacity: parseInt(spot.max_capacity),
        facilities: spot.facilities
    });

    recalcCapacity();
    renderPackagesByCapacity();
    renderSelected();
    renderSpotsButtons();
     renderSpotFacilities();
     renderServices();
}

function removeSpot(spotId){
    selectedSpots = selectedSpots.filter(s=>s.id!==spotId);
    recalcCapacity();
    validateTickedPackage();
    renderPackagesByCapacity();
    renderSelected();
    renderSpotsButtons();
    renderSpotFacilities();
    renderServices();

    const path = document.getElementById('zone-'+spotId);
    if(path) path.classList.remove('selected');
}

/* ================= SPOT BUTTONS SYNC ================= */
function renderSpotsButtons(){

    spots.forEach(spot=>{

        const path = document.getElementById('zone-'+spot.id);
        if(!path) return;

        const isSelected = selectedSpots.find(s=>s.id===spot.id);

        if(isSelected){
            path.classList.add('selected');
        }else{
            path.classList.remove('selected');
        }

    });

}
function recalcCapacity(){
    totalCapacity = selectedSpots.reduce((sum,s)=>sum+s.max_capacity,0);
}

/* ================= PACKAGES ================= */
function renderPackagesByCapacity(){
    const grid = document.getElementById('spot_packages_grid');
    grid.innerHTML = '';

    const validPackages = packages.filter(p=>parseInt(p.persons) <= totalCapacity);
    if(validPackages.length===0){
        grid.innerHTML = `<div class="text-muted text-center py-5">Select spot(s) to see packages</div>`;
        return;
    }

    validPackages.forEach(pkg=>{
        const selected = tickedPackage?.id === pkg.id;
        grid.insertAdjacentHTML('beforeend', `
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="pkg-btn ${selected?'selected':''}" onclick="selectPackage(${pkg.id})">
                    <div class="fw-bold">${pkg.persons} Persons</div>
                    <div>৳ ${formatMoney(pkg.price)}</div>
                    <span class="spot-check" style="${selected?'':'display:none'}">✔</span>
                </div>
            </div>
        `);
    });

    document.getElementById('spotTitleText').innerText =
        selectedSpots.map(s=>s.title).join(', ') + ` (Capacity: ${totalCapacity})`;
}

function selectPackage(pkgId){
    tickedPackage = (tickedPackage?.id === pkgId) ? null : {id:pkgId};
    renderPackagesByCapacity();
    renderSelected();
    renderServices();
}

function validateTickedPackage(){
    if(!tickedPackage) return;
    const pkg = packages.find(p=>p.id===tickedPackage.id);
    if(!pkg || pkg.persons > totalCapacity){
        tickedPackage = null;
    }
}


function renderSelected(){
    const grid = document.getElementById('selected_grid');
    const card = document.getElementById('selectedPackagesCard');
    grid.innerHTML = '';

    let rawSpotTotal = 0;

    selectedSpots.forEach(s=>{
        rawSpotTotal += s.price;
        grid.insertAdjacentHTML('beforeend', `
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="selected-card">
                    <button type="button" class="remove-btn" onclick="removeSpot(${s.id})" aria-label="Remove ${s.title}">✕</button>
                    <div class="fw-bold">${s.title}</div>
                    <div>৳ ${formatMoney(s.price)}</div>
                </div>
            </div>
        `);
    });

    card.classList.toggle('d-none', selectedSpots.length===0);

    // ===== SPOT DISCOUNT =====
    const spotDiscount = getSpotDiscountPercent();
    const discountedSpot = round2(rawSpotTotal - (rawSpotTotal * spotDiscount / 100));

    document.getElementById('spotTotal').innerText = formatMoney(discountedSpot);

    // 🔥 VERY IMPORTANT
    document.getElementById('spot_discount_percent').value = spotDiscount;

    updateServices();
    updateSummary();
    updateItems();
    bindTopValidation();
}

/* ================= SERVICES ================= */
function updateServices(){
    serviceTotal = 0;
    let services = [];

    document.querySelectorAll('.service-row').forEach(row=>{
        const chk = row.querySelector('.service-check');
        const priceInput = row.querySelector('.price');
        const qtyInput = row.querySelector('.qty');
        if(!chk || !priceInput || !qtyInput) return;

        const price = parseFloat(priceInput.value || 0);
        const qty = parseInt(qtyInput.value || 1);
        const total = price * qty;

        const totalEl = row.querySelector('.row-total');
        if(totalEl) totalEl.innerText = formatMoney(total);

        if(chk.checked){
            serviceTotal += total;
            services.push({
                service_id: chk.dataset.id || null,
                title: chk.dataset.title || chk.value || 'Service',
                price, qty, total
            });
        }
    });

    document.getElementById('serviceTotalDisplay').innerText = formatMoney(serviceTotal);
    document.getElementById('additional_services').value = JSON.stringify(services);

    updateSummary();
}

/* ================= UPDATE ITEMS (SPOTS + PACKAGE + SERVICES) ================= */
function updateItems(){
    const itemsArr = [];

    // Spots
    selectedSpots.forEach(s=>{
        itemsArr.push({
            id: s.id,
            title: s.title,
            price: s.price,
            max_capacity: s.max_capacity,
            type: 'spot'
        });
    });

    // Package
    if(tickedPackage){
        const pkg = packages.find(p=>p.id==tickedPackage.id);
        if(pkg){
            itemsArr.push({
                id: pkg.id,
                title: `${pkg.persons} Persons Package`,
                price: parseFloat(pkg.price),
                persons: pkg.persons,
                type: 'package',
                spot_id: selectedSpots.length === 1 ? selectedSpots[0].id : null
            });
        }
    }

    // Services
    document.querySelectorAll('.service-row').forEach(row=>{
        const chk = row.querySelector('.service-check');
        if(chk && chk.checked){
            const price = parseFloat(row.querySelector('.price').value || 0);
            const qty = parseInt(row.querySelector('.qty').value || 1);
            itemsArr.push({
                service_id: chk.dataset.id,
                title: chk.dataset.title || chk.value || 'Service',
                price, qty, total: price*qty,
                type: 'service'
            });
        }
    });

    document.getElementById('items').value = JSON.stringify(itemsArr);
}

/* ================= SUMMARY ================= */

function updateSummary(){
    const rawSpot = selectedSpots.reduce((s,x)=>s+x.price,0);
    const spotDisc = getSpotDiscountPercent();
    const spotTotal = round2(rawSpot - (rawSpot * spotDisc / 100));

    const packageTotal = tickedPackage
        ? parseFloat(packages.find(p=>p.id===tickedPackage.id).price)
        : 0;

    const subtotal = spotTotal + packageTotal + serviceTotal;
     const pact =packageTotal;
const percentDiscountAmount = round2(
    subtotal * discountPercent / 100
);

const invoiceDiscount = useManualDiscount ? round2(manualDiscountAmount) : 0;

const totalDiscount = round2(percentDiscountAmount + invoiceDiscount);
const afterDiscountAmount = round2(subtotal - percentDiscountAmount);
const grandTotal = round2(subtotal - totalDiscount);




    // ===== UI UPDATE =====
    document.getElementById('summarySpot').innerText = formatMoney(spotTotal);
    document.getElementById('summaryPackage').innerText = formatMoney(packageTotal);
    document.getElementById('Packaget').innerText = formatMoney(pact);
    document.getElementById('summaryService').innerText = formatMoney(serviceTotal);

    // % label update
    document.getElementById('discountLabel').innerText = discountPercent;

    // % discount amount (first line)
    document.getElementById('discountAmountText').innerText =
        formatMoney(percentDiscountAmount);

    // invoice adjustment line
    document.getElementById('invoiceAdjustmentAmount').innerText =
    formatMoney(manualDiscountAmount);


    // After discount line
document.getElementById('afterDiscountAmount').innerText =
    formatMoney(afterDiscountAmount);

// hidden inputs (REQUEST)
// invoice adjustment discount (REQUEST)
document.getElementById('invoice_adjustment_discount').value =
    invoiceDiscount.toFixed(2);


// discount_amount already contains TOTAL discount (percent + invoice)
document.getElementById('discount_amount').value =
    totalDiscount.toFixed(2);

    document.getElementById('grandTotal1').innerText = formatMoney(subtotal);
    document.getElementById('grandTotal').innerText = formatMoney(grandTotal);
    document.getElementById('total_price').value = grandTotal.toFixed(2);

    const mobileTotalEl = document.getElementById('mobileGrandTotal');
    if(mobileTotalEl) mobileTotalEl.innerText = formatMoney(grandTotal);

    validateForm();
}

/* ================= DISCOUNT ================= */
function initDiscountEvents(){
    const discountInput = document.getElementById('discountPercent');
    const invoiceRow = document.getElementById('invoiceAdjustmentRow');

    if(!discountInput) return;

    discountInput.addEventListener('input', function(){
        let val = parseFloat(this.value || 0);
        val = Math.min(Math.max(val,0), MAX_DISCOUNT);

        discountPercent = val;
        this.value = val;
        document.getElementById('discount_percent').value = val;

        // invoice row visibility rule
        if(val > 0){
            invoiceRow.classList.remove('d-none');
        }else{
            invoiceRow.classList.add('d-none');
            manualDiscountAmount = 0;
            useManualDiscount = false;
            document.getElementById('discountAmountInput').value = '';
        }

        updateSummary();
    });
}



/* ================= TOGGLE SERVICES ================= */
function initToggleServices(){

    const toggle = document.getElementById('toggleServices');
    const card = document.getElementById('additionalServicesCard');

    if(!toggle || !card) return;

    toggle.addEventListener('change', function(){

        if(this.checked){
            card.classList.remove('d-none');
        }else{
            card.classList.add('d-none');
        }

    });

}

/* ================= SERVICE EVENTS ================= */
function initServiceEvents(){
    document.addEventListener('change', function(e){
        if(e.target.matches('.service-check')) updateServices();
    });
    document.addEventListener('input', function(e){
        if(e.target.matches('.price,.qty')) updateServices();
    });
}

/* ================= QUANTITY BUTTONS ================= */
document.addEventListener('click', function(e){
    if(e.target.closest('.qty-btn')){
        const btn = e.target.closest('.qty-btn');
        const row = btn.closest('.service-row');
        if(!row) return;

        const input = row.querySelector('.qty');
        if(!input) return;

        let val = parseInt(input.value || 1);
        if(btn.classList.contains('plus')) val++;
        if(btn.classList.contains('minus')) val = Math.max(1, val-1);

        input.value = val;
        updateServices();
    }
});

/* ================= FORM VALIDATION ================= */
function validateForm(){
    const customer = document.getElementById('changeCustomer');
    const account  = document.getElementById('changeAccountsName');
    const amount   = document.querySelector('input[name="receive_amount"]');
    const date     = document.getElementById('booking_date');
    const saveBtn  = document.getElementById('saveBookingBtn');
    const mobileSaveBtn = document.getElementById('mobileSaveBtn');

    if(!date) return;

    const ok = date.value && selectedSpots.length > 0;

    if(saveBtn) saveBtn.disabled = !ok;
    if(mobileSaveBtn) mobileSaveBtn.disabled = !ok;
}

function bindTopValidation(){
    const customer = document.getElementById('changeCustomer');
    const account  = document.getElementById('changeAccountsName');
    const amount   = document.querySelector('input[name="receive_amount"]');
    const date     = document.getElementById('booking_date');

    if(customer) customer.addEventListener('input', validateForm);
    if(account) account.addEventListener('input', validateForm);
    if(amount) amount.addEventListener('input', validateForm);
    if(date) date.addEventListener('input', validateForm);

    validateForm();
}
/* ================= MANUAL DISCOUNT ================= */
function initManualDiscountEvents(){
    const text = document.getElementById('invoiceAdjustmentAmount');
    const input = document.getElementById('discountAmountInput');
    const row = document.getElementById('invoiceAdjustmentRow');

    if(!text || !input || !row) return;

    // click to edit
    text.addEventListener('click', ()=>{
        row.classList.remove('d-none');
        text.classList.add('d-none');

        input.disabled = false;        // ✅ enable
        input.classList.remove('d-none');
        input.focus();
    });

    // typing
    input.addEventListener('input', ()=>{
        let val = parseFloat(input.value || 0);
        if(val < 0) val = 0;

        manualDiscountAmount = val;
        useManualDiscount = val > 0;

        updateSummary();
    });

    // blur
    input.addEventListener('blur', ()=>{
        input.classList.add('d-none');
        input.disabled = true;         // ✅ disable again
        text.classList.remove('d-none');

        if(manualDiscountAmount <= 0){
            useManualDiscount = false;
            row.classList.add('d-none');
        }

        updateSummary();
    });
}




document.querySelectorAll('form').forEach(form=>{
    form.addEventListener('submit', function () {
         updateSummary();
        const el = document.getElementById('spot_discount_percent');
        if(el){
            el.value = getSpotDiscountPercent();
        }
    });
});

function renderSpotFacilities() {
    const container = document.getElementById('spotFacilitiesContainer');
    container.innerHTML = '';

    if (selectedSpots.length === 0) {
        container.innerHTML = '<p class="text-muted">Select a zone to see its facilities</p>';
        return;
    }

    // ===========================
    // Spot-specific facilities
    // ===========================
    selectedSpots.forEach(spot => {
        const spotDiv = document.createElement('div');
        spotDiv.style.marginBottom = '20px';

        const ul = document.createElement('ul');
        ul.className = 'spot-facilities';

        if (spot.facilities && spot.facilities.length > 0) {
            spot.facilities.forEach(fac => {
                const li = document.createElement('li');
                li.innerHTML = `<span class="facility-icon">✔️</span> ${fac.facility}`;
                ul.appendChild(li);
            });
        }

        // শুধু যদি spot-এর facilities থাকে
        if (ul.childElementCount > 0) {
            spotDiv.appendChild(ul);
            container.appendChild(spotDiv);
        }
    });

    // ===========================
    // Common facilities (একবার)
    // ===========================
    if (typeof commonFacilities !== 'undefined' && commonFacilities.length > 0) {
        const commonDiv = document.createElement('div');
        commonDiv.style.marginBottom = '20px';
        const ul = document.createElement('ul');
        ul.className = 'spot-facilities';

        commonFacilities.forEach(cf => {
            const li = document.createElement('li');
            li.innerHTML = `<span class="facility-icon">✔️</span> ${cf.facility_name}`;
            ul.appendChild(li);
        });

        commonDiv.appendChild(ul);
        container.appendChild(commonDiv);
    }
}

function renderSpotCapacities(){
    if(!spots || spots.length===0) return;
    const svgContainer = document.getElementById('spot_boxes');
    if(!svgContainer) return;

    // আগে যদি আগের capacity flags থাকে, remove করে দাও
    document.querySelectorAll('.capacity-flag').forEach(f => f.remove());

    spots.forEach(spot=>{
        const path = document.getElementById('zone-'+spot.id);
        if(!path) return;

        const svgNS = "http://www.w3.org/2000/svg";

        // Bounding box নিয়ে flag-এর position ঠিক করি
        const bbox = path.getBBox();
        const flagWidth = 180;
        const flagHeight = 35;
        const offsetY = 3; // path top থেকে কত দূরে flag

        // Flag group
        const g = document.createElementNS(svgNS,'g');
        g.classList.add('capacity-flag');

        // Flag background (rectangle with pointer)
        const rect = document.createElementNS(svgNS,'rect');
        rect.setAttribute('x', bbox.x + bbox.width/2 - flagWidth/2);
        rect.setAttribute('y', bbox.y - flagHeight - offsetY);
        rect.setAttribute('width', flagWidth);
        rect.setAttribute('height', flagHeight);
        rect.setAttribute('rx', 4); // rounded corner
        rect.setAttribute('fill', '#ffcc00'); // yellow flag
        rect.setAttribute('stroke', '#333');
        rect.setAttribute('stroke-width', 1);

        // small pointer triangle
        const pointer = document.createElementNS(svgNS,'polygon');
        pointer.setAttribute('points', `
            ${bbox.x + bbox.width/2 - 5},${bbox.y - offsetY}
            ${bbox.x + bbox.width/2 + 5},${bbox.y - offsetY}
            ${bbox.x + bbox.width/2},${bbox.y - offsetY + 6}
        `);
        pointer.setAttribute('fill', '#ffcc00');
        pointer.setAttribute('stroke', '#333');
        pointer.setAttribute('stroke-width', 1);

        // Text inside flag
        // Text inside flag
const text = document.createElementNS(svgNS,'text');
text.setAttribute('x', bbox.x + bbox.width/2);
text.setAttribute('y', bbox.y - flagHeight/2 - offsetY + 4); // vertical center
text.setAttribute('text-anchor', 'middle');
text.setAttribute('fill', '#000');
text.setAttribute('font-size', '10px');
text.setAttribute('font-weight', 'bold');
text.textContent = `Max Capacity: ${spot.max_capacity}`;

        // append all to group
        g.appendChild(rect);
        g.appendChild(pointer);
        g.appendChild(text);

        // append group to svg
       // default hidden
g.style.opacity = "0";
g.style.transition = "0.25s ease";

// append group to svg
path.parentNode.appendChild(g);

// hover show
path.addEventListener('mouseenter', function () {
    g.style.opacity = "1";
});

// hover hide
path.addEventListener('mouseleave', function () {
    g.style.opacity = "0";
});
    });


}
</script>

<script>
const mapWrapper = document.getElementById('mapWrapper');

let zoomLevel = 1;
let minZoom = 1;
let maxZoom = 3;

let isDragging = false;
let startX = 0;
let startY = 0;
let offsetX = 0;
let offsetY = 0;

let targetZoom = zoomLevel;
let targetOffsetX = offsetX;
let targetOffsetY = offsetY;

function updateTransform() {
    zoomLevel += (targetZoom - zoomLevel) * 0.2;
    offsetX += (targetOffsetX - offsetX) * 0.2;
    offsetY += (targetOffsetY - offsetY) * 0.2;

    mapWrapper.style.transform =
        `translate(${offsetX}px, ${offsetY}px) scale(${zoomLevel})`;

    requestAnimationFrame(updateTransform);
}
updateTransform();


// ===== Desktop Scroll Zoom =====
mapWrapper.addEventListener('wheel', e => {
    e.preventDefault();

    const rect = mapWrapper.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    const prevZoom = targetZoom;
    const delta = e.deltaY < 0 ? 0.2 : -0.2;

    targetZoom = Math.min(Math.max(targetZoom + delta, minZoom), maxZoom);

    const zoomFactor = targetZoom / prevZoom;

    targetOffsetX -= (mouseX - targetOffsetX) * (zoomFactor - 1);
    targetOffsetY -= (mouseY - targetOffsetY) * (zoomFactor - 1);

}, { passive:false });


// ===== Dragging Map =====
mapWrapper.addEventListener('mousedown', e => {
    isDragging = true;
    startX = e.clientX - targetOffsetX;
    startY = e.clientY - targetOffsetY;
    mapWrapper.style.cursor = 'grabbing';
});

window.addEventListener('mousemove', e => {
    if(!isDragging) return;
    targetOffsetX = e.clientX - startX;
    targetOffsetY = e.clientY - startY;
});

window.addEventListener('mouseup', () => {
    isDragging = false;
    mapWrapper.style.cursor = 'grab';
});


// ===== Mobile Touch =====
let lastDist = 0;

mapWrapper.addEventListener('touchstart', e => {

    hideMapHint();

    if(e.touches.length === 1){
        startX = e.touches[0].clientX - targetOffsetX;
        startY = e.touches[0].clientY - targetOffsetY;
    }

}, { passive:false });


mapWrapper.addEventListener('touchmove', e => {
    e.preventDefault();

    if(e.touches.length === 1){
        targetOffsetX = e.touches[0].clientX - startX;
        targetOffsetY = e.touches[0].clientY - startY;
    }

    if(e.touches.length === 2){

        const t1 = e.touches[0];
        const t2 = e.touches[1];

        const dist = Math.hypot(
            t2.clientX - t1.clientX,
            t2.clientY - t1.clientY
        );

        if(lastDist){

            const rect = mapWrapper.getBoundingClientRect();

            const midX = (t1.clientX + t2.clientX)/2 - rect.left;
            const midY = (t1.clientY + t2.clientY)/2 - rect.top;

            const prevZoom = targetZoom;

            targetZoom += (dist - lastDist) * 0.003;
            targetZoom = Math.min(Math.max(targetZoom, minZoom), maxZoom);

            const zoomFactor = targetZoom / prevZoom;

            targetOffsetX -= (midX - targetOffsetX) * (zoomFactor - 1);
            targetOffsetY -= (midY - targetOffsetY) * (zoomFactor - 1);

        }

        lastDist = dist;
    }

}, { passive:false });


mapWrapper.addEventListener('touchend', () => {
    lastDist = 0;
});

// ===== Zoom +/- / Reset buttons (great for mobile where pinch can be fiddly) =====
function zoomMapBy(delta){
    const rect = mapWrapper.getBoundingClientRect();
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    const prevZoom = targetZoom;
    targetZoom = Math.min(Math.max(targetZoom + delta, minZoom), maxZoom);
    const zoomFactor = targetZoom / prevZoom;

    targetOffsetX -= (centerX - targetOffsetX) * (zoomFactor - 1);
    targetOffsetY -= (centerY - targetOffsetY) * (zoomFactor - 1);
}

function hideMapHint(){
    const hint = document.getElementById('mapHint');
    if(hint) hint.style.display = 'none';
}

document.getElementById('zoomInBtn')?.addEventListener('click', ()=>{ hideMapHint(); zoomMapBy(0.4); });
document.getElementById('zoomOutBtn')?.addEventListener('click', ()=>{ hideMapHint(); zoomMapBy(-0.4); });
document.getElementById('zoomResetBtn')?.addEventListener('click', ()=>{
    hideMapHint();
    targetZoom = 1;
    targetOffsetX = 0;
    targetOffsetY = 0;
});

setTimeout(hideMapHint, 6000);


function getServicesForSelectedSpots(){

    if(selectedSpots.length === 0) return [];

    const selectedIds = selectedSpots.map(s => parseInt(s.id));

    return allServices.filter(service => {

        // Global service
        if(service.is_global == 1) return true;

        // Spot match
        if(service.spots && service.spots.length > 0){
            return service.spots.some(sp =>
                selectedIds.includes(parseInt(sp.id))
            );
        }

        return false;
    });
}



function getServiceDynamicPrice(service){

    if(!service.prices || service.prices.length === 0){
        return parseFloat(service.price || 0);
    }

    const capacity = getSelectedPackageCapacity();

    if(capacity === 0){
        return parseFloat(service.price || 0);
    }

    const matched = service.prices.find(p => {
        return capacity >= parseInt(p.min_person) &&
               capacity <= parseInt(p.max_person);
    });

    return matched ? parseFloat(matched.price) : 0;
}


function getSelectedPackageCapacity(){
    if(!tickedPackage) return 0;

    const pkg = packages.find(p => p.id === tickedPackage.id);
    return pkg ? parseInt(pkg.persons) : 0;
}


function renderServices(){

    const tbody = document.getElementById('service_tbody');
    if(!tbody) return;

    tbody.innerHTML = '';
    serviceTotal = 0;

    const services = getServicesForSelectedSpots();

    if(services.length === 0){
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    No service available for selected spot
                </td>
            </tr>
        `;
        return;
    }

    services.forEach(service => {

        const dynamicPrice = getServiceDynamicPrice(service);

        tbody.insertAdjacentHTML('beforeend', `
            <tr class="service-row">
                <td data-label="Service">${service.title}</td>

                <td data-label="Price">
                    <input type="number"
                        class="form-control price" readonly
                        value="${dynamicPrice}">
                </td>

                <td data-label="Qty (Per Person)">
                    <div class="qty-wrap justify-content-end">
                        <button type="button" class="qty-btn minus" aria-label="Decrease quantity">−</button>
                        <input type="number"
                            class="form-control qty text-center"
                            value="1" min="1" inputmode="numeric">
                        <button type="button" class="qty-btn plus" aria-label="Increase quantity">+</button>
                    </div>
                </td>

                <td data-label="Total">৳ <span class="row-total">0</span></td>

                <td data-label="Select">
                    <input type="checkbox"
                        class="service-check"
                        data-id="${service.id}"
                        data-title="${service.title}">
                </td>
            </tr>
        `);
    });

    updateServices();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('booking_success'))
<script>
Swal.fire({
    icon:'success',
    title:'{{ session("booking_success") }}',
    text:'Our representative will contact you shortly.'
});
</script>
@endif
@endsection