@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Update Brand</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    <form class="add-product__form" action="{{ route('brands.update', $brand->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="">

                            <div class="">
                                <div class="">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Brand Name</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="brand_name" placeholder="" value="{{$brand->brand_name}}" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Brand Code</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="brand_code" placeholder="" value="{{$brand->brand_code}}" required maxlength="2">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 form-group form-group--lg">
                                    <label class="form-label">Status</label>
                                    <div class="input-group input-group--append">

                                        <select class="form-control" name="status" required>
                                            <option value="">--Select Status--</option>
                                            <option value="1" {{ $brand->status==1?'selected':'' }}>Active</option>
                                            <option value="2" {{ $brand->status==2?'selected':'' }}>Disable</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-12">

                                    <div class="add-product__submit">
                                        <div class="modal__footer-button">
                                            <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                            </button>
                                        </div>
                                        <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('brands.index') }}"><span class="button__text">Cancel</span></a>
                                        </div>
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
