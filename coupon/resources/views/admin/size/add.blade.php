@extends('layouts.admin')

@section('content')
        <main class="page-content">
            <div class="container container--flex">
                <div class="page-header">
                    <h1 class="page-header__title">Add Size</h1>
                </div>
                <div class="card add-product card--content-center">
                    <div class="card__wrapper">
                        <div class="">
                                <form class="add-product__form" action="{{ route('sizes.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="">
                                    <div class="">
                                        <div class="">
                                            <div class="col-12 form-group form-group--lg">
                                                <label class="form-label">Size Name</label>
                                                <div class="input-group">
                                                    <input class="form-control" type="text" name="size_name" placeholder="" value="" required>
                                                </div>
                                            </div>
                                        </div>
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
                                                                    <div class="col-12">

                                        <div class="add-product__submit">
                                            <div class="modal__footer-button">
                                                <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                                </button>
                                            </div>
                                            <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('sizes.index') }}"><span class="button__text">Cancel</span></a>
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
