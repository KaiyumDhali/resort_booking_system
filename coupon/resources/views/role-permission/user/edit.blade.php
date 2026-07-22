@extends('layouts.admin')

@section('content')




<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Edit User</h1>
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
                    <form class="add-product__form" action="{{ route('users.update',$user->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="">
                            <div class="">
                                <div class="">
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">User Name</label>
                                        <div class="input-group">
                                            <input id="name" class="form-control" type="text" name="name" placeholder="" value="{{ $user->name }}" required autocomplete="name" autofocus>
                                                    @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">User Email</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="email" placeholder="" value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Password</label>
                                        <div class="input-group">
                                            <input class="form-control" id="password" type="password" name="password" placeholder="" value="" required autocomplete="email">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input class="form-control" id="password-confirm" type="password" name="password_confirmation" placeholder="" value="{{ old('email') }}" required autocomplete="new-password">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 form-group form-group--lg">
                                        <label class="form-label">Roles</label>
                                        <div class="input-group">
                                            <select name="roles[]" class="form-control" multiple>
                                                <!--<option value="">Select Role</option>-->
                                                @foreach ($roles as $role)
                                                <option value="{{ $role }}"
                                                {{ in_array($role, $userRoles) ? 'selected':'' }}>
                                                {{ $role }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('roles') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                       


                                </div>
                                <div class="add-product__submit">
                                    <div class="modal__footer-button">
                                        <button class="button button--primary button--block" type="submit"><span class="button__text">Update</span>
                                        </button>
                                    </div>
                                    <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('users.index') }}"><span class="button__text">Cancel</span></a>
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