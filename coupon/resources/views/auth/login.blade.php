@extends('layouts.login')

@section('content')

<div class="page-wrapper">
    <main class="page-auth">
        <div class="page-auth__center">
            <div class="page-auth__gradient">
                <div class="page-auth__gradient-shape"></div>
                <div class="page-auth__gradient-shape"></div>
                <div class="auth-logo">
                    <!--<img class="auth-logo__icon" src="img/content/logotype.svg" width="44" alt="#" />-->
                    <img class="auth-logo__icon" src="{{ asset('assets/img/content/wonderparklogo.png') }}">
                    <div class="auth-logo__text">Wonder Park BD</div>
                </div>
                <div class="page-auth__gradient-column"></div>
            </div>
            <div class="auth-card card">
                <div class="auth-card__shape"></div>
                <div class="auth-card__shape"></div>
                <div class="card__wrapper">
                    <div class="auth-card__left">
                        <div class="auth-card__logo">
                            <div class="auth-logo">
                                <!--<img class="auth-logo__icon" src="img/content/logotype.svg" width="44" alt="#" />-->
                                <img class="auth-logo__icon" src="{{ asset('assets/img/content/wonderparklogo.png') }}">
                                <div class="auth-logo__text">Wonder Park BD</div>
                            </div>
                        </div>
                        <!--<img class="auth-card__bg auth-bg-image-light" src="img/content/auth-bg.jpg" alt="#">-->
                        <!--<img class="auth-card__bg auth-bg-image-dark" src="img/content/auth-bg-dark.jpg" alt="#">-->
                        <img class="auth-card__bg auth-bg-image-light" src="{{ asset('assets/img/content/auth-bg.jpg') }}">
                        <img class="auth-card__bg auth-bg-image-dark" src="{{ asset('assets/img/content/auth-bg-dark.jpg') }}">

                    </div>
                    <form class="auth-card__right" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="auth-card__top">
                            <img src="{{ asset('assets/img/content/wonderparklogo.png') }}" width="350">
                            <h1 class="auth-card__title">Welcome to 
                                <span class="text-theme">Wonder Park BD</span>
                            </h1>
                            <p class="auth-card__text">Welcome Back, Please login
                                <br>to your account.</p>
                        </div>
                        <div class="auth-card__body">
                            <div class="form-group">
                                <div class="input-group input-group--prepend"><span class="input-group__prepend">
                                        <svg class="icon-icon-user">
                                        <use xlink:href="#icon-user"></use>
                                        </svg></span>
                                    <!--<input class="input" type="email" value="reza@gmail.com" required>-->
                                    <input id="email" type="email" class="input @error('email') is-invalid @enderror" name="email" value="admin@wonderparkbd.com" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="input-group input-group--prepend"><span class="input-group__prepend">
                                        <svg class="icon-icon-password">
                                        <use xlink:href="#icon-password"></use>
                                        </svg></span>
                                    <!--<input class="input" type="password" value="12345678" required>-->
                                    <input id="password" type="password" class="input @error('password') is-invalid @enderror" name="password" value="admininfo" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                </div>
                            </div>
                            

                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="input-group input-group--prepend">
                                            <label class="checkbox">
                                                
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span class="checkbox__marker">
                                                    <span class="checkbox__marker-icon">
                                                        <svg viewBox="0 0 14 12">
                                                        <path d="M11.7917 1.2358C12.0798 1.53914 12.0675 2.01865 11.7642 2.30682L5.7036 8.06439C5.40574 8.34735 4.93663 8.34134 4.64613 8.05084L2.22189 5.6266C1.92604 5.33074 1.92604 4.85107 2.22189 4.55522C2.51774 4.25937 2.99741 4.25937 3.29326 4.55522L5.19538 6.45734L10.7206 1.20834C11.024 0.920164 11.5035 0.93246 11.7917 1.2358Z"/>
                                                        </svg>
                                                    </span>
                                                </span>
                                                  <label class="d-inline-block ml-2" for="remember">
                                            {{ __('Remember Me') }}
                                                </label>      
                                                        
                                            </label>
                                                
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-auto">
                                    
                                    {{-- <div class="form-group">
                                        @if (Route::has('password.request'))
                                        <a class="" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                        </a>
                                        @endif
                                    </div> --}}
                                    
                                    
                                    
                                </div>
                                
                            </div>
                        </div>
                        <div class="auth-card__bottom">
                            <div class="auth-card__buttons">
<!--                                <div class="auth-card__button">
                                    <a class="button button--secondary button--block" href="">
                                        <span class="button__text">Sign Up</span>
                                    </a>
                                </div>-->
                                <div class="auth-card__button">
<!--                                    <button class="button button--primary button--block" type="submit" ><span class="button__text">{{ __('Login') }}</span>
                                    </button>-->
                                    
                                    <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                    </button>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

@endsection
