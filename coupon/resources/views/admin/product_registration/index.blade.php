@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container">
        <div class="page-header">
            <h1 class="page-header__title">Registered Products</h1>
        </div>

        
        <div class="table-wrapper" style="margin-top: 50px;">
            <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible" data-simplebar>
                <table class="table table--lines" id="productregistrationTable">
                    <thead class="table__header">
                        <tr class="table__header-row">
                            <th class="table__th-sort"><span class="align-middle">ID</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Name</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Category</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Sub Category</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Product Serial</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Memo No</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Memo Image</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Name</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Mobile</span><span class="sort sort--down"></span></th>
                            <th class="table__th-sort"><span class="align-middle">Customer Address</span><span class="sort sort--down"></span></th>
                        </tr>
                    </thead>
                    <tbody id='tbody'>
                        @if(!empty($productregistration))
                        @foreach($productregistration as $productregistrations)

@php
//    echo '<pre>';
//    print_r($productregistrations);
//    echo '</pre>';
//    die();
@endphp

                        <tr class="table__row">
                            <td class="table__td">{{$productregistrations->id}}</td>
                            <td class="table__td">{{$productregistrations->product->product_name}}</td>
                            <td class="table__td">{{$productregistrations->product->category->category_name}}</td>
                            <td class="table__td">{{$productregistrations->product->subCategory->sub_category_name}}</td>
                            <td class="table__td">{{$productregistrations->product_serial }}</td>
                            <td class="table__td">{{$productregistrations->memo_no }}</td>
                            <td class="table__td">
                            @if ($productregistrations->memo_path != null)
                                <img class="" width="100" src="{{ asset($productregistrations->memo_path) }}"  alt="Memo Image" />
                            @endif 
                            </td>
                            <td class="table__td">{{$productregistrations->name}}</td>
                            <td class="table__td">{{$productregistrations->mobile}}</td>
                            <td class="table__td">{{$productregistrations->customer_address }}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr class="table__row">
                            <td class="table__td">No Product Registered Yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

</main>




<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    let dataTableOptions = {
//      dom: '<"top"fB>rt<"bottom"lip>',
        paging: true,
        ordering: true,
        searching: true,
        responsive: true,
        lengthMenu: [5, 10, 25, 50, 100],
        pageLength: 25,
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    };

$('#productregistrationTable').DataTable(dataTableOptions);
</script>



@endsection