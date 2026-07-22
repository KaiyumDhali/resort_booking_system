
    
    
    <!DOCTYPE html>
<html>

<head>
    <title>Saffron Jusal Ltd.</title>

    <style>
    .mail_container {
        width: 50%;
    }

    .mail_head {
        padding: 20px 0px;
    }

    .mail_footer {
        padding: 20px 0px;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #858796;
        border-collapse: collapse;
    }

    .table-bordered {
        border: 1px solid #e3e6f0;
    }

    .border-primary {
        border-color: #01A950 !important;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #e3e6f0;
        padding: 5px 20px;
        text-align: left;
    }

    .table-hover tbody tr:hover {
        color: #858796;
        background-color: rgba(0, 0, 0, 0.075);
    }

    .w-25 {
        width: 25% !important;
    }

    .w-75 {
        width: 75% !important;
    }
    </style>
</head>

<body>

    <div class="mail_container">

        <h2 class="mail_head">Dear Saffron Jusal,</h2>

        <table class="table table-hover table-bordered border-primary">
            <tbody>
                <tr>
                    <th scope="row" class="w-25"><strong>Customer Name:</strong></th>
                    <td>{{ $data->product_registration->name }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Customer Mobile:</strong></th>
                    <td>{{ $data->product_registration->mobile }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Customer Address:</strong></th>
                    <td>{{ $data->product_registration->customer_address }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Memo No:</strong></th>
                    <td>{{ $data->product_registration->memo_no }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Shop Address:</strong></th>
                    <td>{{ $data->product_registration->shop_address }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Complain Date:</strong></th>
                    <td>{{ $data->complain_date }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Product Name:</strong></th>
                    <td>{{ $data->product_registration->product->product_name }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Product Serial:</strong></th>
                    <td>{{ $data->product_serial }}</td>
                </tr>
                <tr>
                    <th scope="row" class="w-25"><strong>Complain:</strong></th>
                    <td>{{ $data->complain }}</td>
                </tr>
               
            </tbody>
        </table>

        <h3 class="mail_footer">{{ $data->product_registration->name }} <br> Thank you.</h3>

    </div>
</body>

</html>
    