@extends('layouts.admin')

@section('content')

<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Add Role</h1>
        </div>
                        @if ($errors->any())
        <ul class="alert alert-warning">
                    @foreach ($errors->all() as $error)
            <li>{{$error}}</li>
                    @endforeach
        </ul>
                @endif
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    <form class="add-product__form" action="{{ route('roles.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                        <div class="">
                            <div class="">
                                <div class="">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Role Name</label>
                                        <div class="input-group">
                                            <input id="name" class="form-control" type="text" name="name" placeholder="" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                                    @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                        </div>
                                    </div>

                                </div>
                                <div class="add-product__submit">
                                    <div class="modal__footer-button">
                                        <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                        </button>
                                    </div>
                                    <div class="modal__footer-button">
                                        <a class="button button--secondary button--block" href="{{ route('roles.index') }}"><span class="button__text">Cancel</span></a>
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