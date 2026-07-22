@extends('layouts.user')

@section('content')

@if (isset($message))
    <div class="text-center" style="color: red; background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;">
        {{ $message }}
    </div>
@endif

@endsection
