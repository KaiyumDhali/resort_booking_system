@extends('layouts.user')

@section('content')
<div class="container">
    <div class="row">
    <div class="col-12">

        <div class="page-header">
            <h3 class="page-header__title">Complain list</h3>
        </div>

        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Registration ID</th>
                    <th scope="col">Product Serial</th>
                    <th scope="col">Complain Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>

                 @foreach($productComplains as $productComplain)
                <tr>
                    <th scope="row">{{$productComplain->id}}</th>
                    <td>{{$productComplain->product_reg_id}}</td>
                    <td>{{$productComplain->product_serial}}</td>
                    <td>{{$productComplain->complain_date}}</td>
                    <td><a class="btn btn-sm btn-info" href="{{ route('productcomplain.show', $productComplain->id) }}">View</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>

</div>
@endsection