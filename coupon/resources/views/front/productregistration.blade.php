@extends('layouts.user')

@section('content')

    @if (isset($message))
        <div class="text-center alert alert-danger">
            {{ $message }}
        </div>
    @endif

    @if (isset($find_coupons))       

        <div class="container">
            <form class="" action="{{ route('productregistration.store') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                {{-- <input type="hidden" class="form-control" name="product_id" value="{{ $find_product->product_id }}"id=""> --}}
                <input type="hidden" class="form-control" name="coupon_serial" value="{{ $find_coupons->coupon_serial }}"id="">
                
                <div class="row">
                    <div class="col text-center">
                        <label style="text-align: center; color: green; font-weight: bold;" for="inputCode">Enter Validation Code</label>
                        <input type="text" class="form-control" name="code" id="inputCode" placeholder="Enter your code" required>
                    </div>
                </div>
               
                <button type="submit" class="btn btn-primary mt-3 w-100">Submit</button>

            </form>
        </div>

    @else
        {{-- <div class="container">
            <div class="row">
                <div class="col-12 text-center py-5 my-0 my-md-5">
                    <h3>Coupon Not Valid !<br> Please Contact with Authority </h3>
                </div>
            </div> --}}
    @endif

@endsection
