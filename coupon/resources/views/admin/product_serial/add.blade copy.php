@extends('layouts.admin')

@section('content')
    <main class="page-content">
        <div class="container container--flex">
            <div class="page-header">
                <h1 class="page-header__title">Product Serial</h1>
            </div>
            <div class="card add-product card--content-center">
                <div class="card__wrapper">
                    <div class="">
                        <form class="add-product__form" action="{{ route('productserials.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="">
                                <div class="row">

                                    {{-- <div class="col-6">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Select Product</label>
                                        <div class="input-group input-group--append">
                                            <select class="form-control" name="product_id" required>
                                                <option value="">--Select Product--</option>
                                                        @foreach ($allProducts as $key => $value)
                                                <option value="{{ $key }}">{{ $value}}</option>
                                                        @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

                                    <div class="col-6">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Select Product</label>
                                            <div class="input-group input-group--append">
                                                <select class="form-control" name="product_id" required>
                                                    <option value="">--Select Product--</option>
                                                    @foreach ($allProducts2 as $id => $productserial)
                                                        <option value="{{ $productserial['id'] }}">
                                                            Name:{{ $productserial['product_name'] }} Code:
                                                            {{ $productserial['product_code'] }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-6">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Date</label>
                                            <div class="input-group">
                                                <input class="form-control" type="date" name="productiondate"
                                                    placeholder="" value="" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                <div class="col-6">
                                                                    <div class="col-12 form-group form-group--lg">
                                                                        <label class="form-label">Date</label>
                                                                        <div class="input-group">
                                                                            <select class="form-control" name="month" required>
                                                                                <option value="" disabled selected>Select Month</option>
                                                                                <option value="01">January</option>
                                                                                <option value="02">February</option>
                                                                                <option value="03">March</option>
                                                                                <option value="04">April</option>
                                                                                <option value="05">May</option>
                                                                                <option value="06">June</option>
                                                                                <option value="07">July</option>
                                                                                <option value="08">August</option>
                                                                                <option value="09">September</option>
                                                                                <option value="10">October</option>
                                                                                <option value="11">November</option>
                                                                                <option value="12">December</option>
                                                                            </select>
                                
                                                                            <select class="form-control" name="year" required>
                                                                                <option value="" disabled selected>Select Year</option>
                                                    @php
                                                        $currentYear = date('Y');
                                                        $yearsRange = range($currentYear, $currentYear + 10);
                                                    @endphp
                                                    @foreach ($yearsRange as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div> --}}




                                    <div class="col-6">
                                        <div class="col-12 form-group form-group--lg">
                                            <label class="form-label">Quantity</label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" name="quantity" placeholder=""
                                                    value="" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!--                                <div class="col-6">
                                                                        <div class="col-12 form-group form-group--lg">
                                                                            <label class="form-label">Begin Number</label>
                                                                            <div class="input-group">
                                                                                <input class="form-control" type="text" name="begin_number" placeholder="" value="" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-6">
                                                                        <div class="col-12 form-group form-group--lg">
                                                                            <label class="form-label">End Number</label>
                                                                            <div class="input-group">
                                                                                <input class="form-control" type="text" name="end_number" placeholder="" value="" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>-->

                                    <!--                                <div class="col-6">
                                                                        <div class="col-12 form-group form-group--lg">
                                                                            <label class="form-label">Status</label>
                                                                            <div class="input-group input-group--append">
                                                                                <select class="form-control" name="status" required>
                                                                                    <option value="">--Select Status--</option>
                                                                                    <option value="1" selected>Active</option>
                                                                                    <option value="2">Disable</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>-->



                                </div>
                                <div class="col-12">
                                    <div class="add-product__submit">
                                        <div class="modal__footer-button">
                                            <button class="button button--primary button--block" type="submit"><span
                                                    class="button__text">Save</span>
                                            </button>
                                        </div>
                                        <div class="modal__footer-button"><a class="button button--secondary button--block"
                                                href="{{ route('productserials.index') }}"><span
                                                    class="button__text">Cancel</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
