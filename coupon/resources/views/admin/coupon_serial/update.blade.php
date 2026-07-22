@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Edit Coupon Serial</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div>
                    <form class="add-product__form" action="{{ route('couponserials.update', $coupon->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- important for PUT method -->

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="col-12 form-group form-group--lg">
                                    <label class="form-label">Status</label>
                                    <div class="input-group">
                                        <select class="form-control" name="status" required>
                                            <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Not Availed</option>
                                            <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Availed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="add-product__submit">
                                <div class="modal__footer-button">
                                    <button class="button button--primary button--block" type="submit">
                                        <span class="button__text">Update</span>
                                    </button>
                                </div>
                                <div class="modal__footer-button">
                                    <a class="button button--secondary button--block" href="{{ route('couponserials.index') }}">
                                        <span class="button__text">Cancel</span>
                                    </a>
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
