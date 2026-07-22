@extends('layouts.dashboard')

@section('content')
    <main class="page-content">
        <div class="container">
            <div class="widgets">
                <div class="widgets__row row gutter-bottom-xl">
                    

                    <div class="col-12 col-sm-6 col-xl-2 d-flex">
                        <div class="widget">
                            <div class="widget__wrapper">
                                <div class="widget__row">
                                    <div class="widget__left">
                                        <h3 class="widget__title">Total Coupon</h3>
                                        {{-- <div class="widget__status-title text-grey">Total Coupon</div> --}}
                                        <div class="widget__spacer"></div>
                                        <div class="widget__trade">
                                            <span class="widget__trade-count">{{ $couponCount }}</span>
                                            <span class="trade-icon trade-icon--up">
                                        </div>
                                        <div class="widget__details"><a class="link-under text-grey"
                                                href="{{ route('couponserials.index') }}">Detail</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 d-flex">
                        <div class="widget">
                            <div class="widget__wrapper">
                                <div class="widget__row">
                                    <div class="widget__left">
                                        <h3 class="widget__title">Coupon Availed</h3>
                                        {{-- <div class="widget__status-title text-grey">Total Coupon</div> --}}
                                        <div class="widget__spacer"></div>
                                        <div class="widget__trade">
                                            <span class="widget__trade-count">{{ $couponAvailedCount }}</span>
                                            <span class="trade-icon trade-icon--up">
                                        </div>
                                        <div class="widget__details"><a class="link-under text-grey"
                                                href="{{ route('couponserials.index') }}">Detail</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 d-flex">
                        <div class="widget">
                            <div class="widget__wrapper">
                                <div class="widget__row">
                                    <div class="widget__left">
                                        <h3 class="widget__title">Coupon Remain</h3>
                                        {{-- <div class="widget__status-title text-grey">Total Coupon</div> --}}
                                        <div class="widget__spacer"></div>
                                        <div class="widget__trade">
                                            <span class="widget__trade-count">{{ $couponRemainCount }}</span>
                                            <span class="trade-icon trade-icon--up">
                                        </div>
                                        <div class="widget__details"><a class="link-under text-grey"
                                                href="{{ route('couponserials.index') }}">Detail</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

           

        </div>

    </main>
@endsection
