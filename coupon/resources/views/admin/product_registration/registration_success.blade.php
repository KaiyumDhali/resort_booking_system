@extends('layouts.user')

@section('content')

<main class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if(session('message'))
                <div class="py-5 my-5 text-center alert alert-{{ session('alert-type') }}">
                    {{ session('message') }}
                </div>
                @endif
            </div>
            <div class="col-12 text-center">
                <a href="https://wonderparkbd.com/" class="btn btn-info">
                    <img width="50%" src="{{ asset('/assets/img/content/wonderparklogo.png') }}" />
                </a>
            </div>
        </div>
    </div>
</div>
</main>



@endsection