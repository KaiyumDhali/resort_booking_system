@extends('layouts.user')

@section('content')


    @if (isset($message))
        <div class="text-center alert alert-success">
            {{ $message }}
        </div>
    @endif



    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 style="text-align: center;">Complain Details</h2>
            </div>

        </div>

        <div class="row">
            @foreach ($find_product->product->productimage as $product_image)
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a href="{{ asset($product_image->image_path) }}" target="_blank" class="fancybox" rel="ligthbox">
                        <img src="{{ asset($product_image->image_path) }}" class="zoom img-fluid " alt="">
                    </a>
                </div>
            @endforeach

            <div class="col-md-12">

                <table class="table table-striped table-hover table-responsive my-5">

                    <tbody>
                        <tr>
                            <td>Product ID</td>
                            <td>{{ $find_product->product_id }}</td>
                        </tr>
                        <tr>
                            <td>Product Serial</td>
                            <td>{{ $find_product->product_serial }}</td>
                        </tr>
                        <tr>
                            <td>Product Name</td>
                            <td>{{ $find_product->product->product_name }}</td>
                        </tr>
                        <tr>
                            <td>Category Name</td>
                            <td>{{ $find_product->product->category->category_name }}</td>
                        </tr>
                        <tr>
                            <td>Subcategory Name</td>
                            <td>{{ $find_product->product->subCategory->sub_category_name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="container">
        <form class="" action="{{ route('productcomplain.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" name="product_id" value="{{ $find_product->product_id }}"
                id="">
            <input type="hidden" class="form-control" name="product_reg_id" value="{{ $registration_id }}" id="">
            <input type="hidden" class="form-control" name="product_serial" value="{{ $product_serial }}" id="">
            <div class="row">
                <div class="col">
                    <label class="pb-3">Write Your Complain</label>
                    <textarea class="form-control" name="complain" id="" placeholder="Write Your Complain ...." required></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card my-4">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-terms-and-condition" aria-expanded="false"
                                        aria-controls="flush-terms-and-condition">
                                        <strong class="pe-2">Product Warreanty</strong> Terms and Condition
                                    </button>
                                </h2>
                                <div id="flush-terms-and-condition" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body"> 
                                        <ul>
                                            <li><p>1. ---</p></li>
                                            <li><p>2. ---</p></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
                        <label class="form-check-label text-danger" for="flexCheckDefault">
                          Agree Terms and Condition
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>

    @if (isset($productComplains) && count($productComplains) > 0)
        <div class="container my-5">
            <div class="row">
                <div class="col-12">
                    <hr class="pt-4">
                    <div class="page-header">
                        <h3 class="page-header__title">Complain list</h3>
                    </div>

                    <div class="card my-4">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @foreach ($productComplains as $productComplain)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-{{ $productComplain->id }}" aria-expanded="false"
                                            aria-controls="flush-{{ $productComplain->id }}">
                                            <strong class="pe-2">Product: </strong>
                                            {{ $productComplain->product_serial }} <strong class="ps-2 ps-md-5 pe-2">#Date:
                                            </strong> {{ $productComplain->complain_date }}
                                        </button>
                                    </h2>
                                    <div id="flush-{{ $productComplain->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">{{ $productComplain->complain }}</div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <!--            <table class="table table-striped table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Product Serial</th>
                                        <th scope="col">Complain Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                
                     @foreach ($productComplains as $productComplain)
    <tr>
                                        <th scope="row">{{ $productComplain->id }}</th>
                                        <td>{{ $productComplain->product_serial }}</td>
                                        <td>{{ $productComplain->complain_date }}</td>
                                        <td><a class="btn btn-sm btn-info" href="{{ route('productcomplain.show', $productComplain->id) }}">View</td>
                                    </tr>
    @endforeach
                                </tbody>
                            </table>-->


                </div>
            </div>

        </div>
    @endif

@endsection
