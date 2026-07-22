
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Saffron Jusal Limited</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
        @page {
            size: A4 landscape;
        }
        /* .title_head{
            padding-right: 30% !important;
        } */
    </style>
</head>

<body style="font-size:10px">
    <table class="table table-borderless table-sm m-0">
        <tbody>
            <tr>
                <td class="text-center title_head">
                    <h4>Saffron Jusal Limited</h4>
                    <h6>Dhaka, Bangladesh</h6>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered table-striped table-sm table-hover">
        @if ($productFeedbackLists)
            <thead class="text-light bg-success ">
                <tr style="text-uppercase text-align: center; vertical-align: middle;">
                    <th class="min-w-50px" style="text-align: center; vertical-align: middle;">Category</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Subcategory</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Product</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Serial</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Feedback Date</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Customer Email</th>
                    <th class="min-w-100px" style="text-align: center; vertical-align: middle;">Mobile Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productFeedbackLists as $key => $productFeedbackList)
                    <tr>
                        <td>{{ $productFeedbackList->category_name }}</td>
                        <td>{{ $productFeedbackList->sub_category_name }}</td>
                        <td>{{ $productFeedbackList->product_name }}</td>
                        <td>{{ $productFeedbackList->product_serial }}</td>
                        <td>{{ $productFeedbackList->feedback_date }}</td>
                        <td>{{ $productFeedbackList->customer_email }}</td>
                        <td>{{ $productFeedbackList->customer_phone }}</td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <tr class="text-center">
                <td colspan="5">No Product Feedback History Found !!!</td>
            </tr>
        @endif
    </table>
</body>

</html>
