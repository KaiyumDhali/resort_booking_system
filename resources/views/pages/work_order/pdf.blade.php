@php
    $afteradvanced = 100 - (int) $workOrder->advance_percent;
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Work Order - {{ $workOrder->work_order_no }}</title>

<style>
@font-face {
    font-family: 'Kalpurush';
    src: url("{{ public_path('fonts/Kalpurush.ttf') }}") format('truetype');
}

body, table, th, td, h1, h6, p, li {
    font-family: 'Kalpurush', sans-serif;
    font-size: 14px;
}



body {
    margin: 0;
    padding: 0;
    line-height: 1.6;
    color: #000;
}

.company-header-space {
    display: none;
}

footer {
    position: fixed;
    bottom: -60px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 12px;
}

header {
    text-align: center;
    margin-bottom: 30px;
}

header h1 {
    margin: 0;
    font-size: 24px;
}

header p {
    margin: 5px 0;
}

.top-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.top-info div p {
    margin: 0px 0;
}

p.text-justify {
    text-align: justify;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th, table td {
    border: 1px solid #000;
    padding: 6px 8px;
}

table th {
    background-color: #f2f2f2;
}

table th:nth-child(1), table td:nth-child(1) { text-align: center; }
table th:nth-child(4), table td:nth-child(4) { text-align: right; }

h6 {
    margin-top: 25px;
    margin-bottom: 10px;
    font-weight: bold;
}

ul {
    padding-left: 20px;
}

.signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 60px;
}

.signatures div p {
    margin: 0;
}

footer {
    position: fixed;
    bottom: 30px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 12px;
}




 .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:bold; }

    
    .logo-cell{ width:20%; vertical-align:middle; }
    .title-cell{ width:60%; vertical-align:middle; }
    .spacer-cell{ width:20%; }

    .company-name{
        font-size:34px;
        font-weight:bold;
        letter-spacing:.5px;
        text-transform:uppercase;
         font-style: italic;
        
         color: #015892;
    }
    .company-name1{
        font-size:32px;
        font-weight:bold;
        letter-spacing:.5px;
        text-transform:uppercase;
        color: #46AA4D;
         
    }

</style>
</head>
<body>
   


<header>
    <h2 style="margin:0 0 3px 0;">কার্যাদেশ</h2>
    <p style="margin:0;">কার্যাদেশ নং: <strong>{{ bnNumber($workOrder->work_order_no) }}</strong></p>
</header>

<div class="top-info">
    <div>
       @if(!empty($workOrder->reference))
        <p><strong>রেফারেন্স: {{ $workOrder->reference }}</strong></p>
        @endif

       <p>
            <strong>তারিখঃ {{  bnDate($workOrder->issue_date)  }}</strong>


        </p>

        <p><strong> {{ $workOrder->client->name ?? 'N/A' }}</strong></p>
        <p><strong>{{ $workOrder->client->designation ?? 'N/A' }}</strong></p>
        <p>{{ $workOrder->client->company ?? 'N/A' }}</p>
        <p>{{ $workOrder->client->address ?? 'N/A' }}</p>
        <p style="padding-top: 10px;"><strong>বিষয়: {{ $workOrder->subject }}</strong></p>
    </div>
</div>

<p class="text-justify">
    এই মর্মে জানানো যাচ্ছে যে, আমাদের প্রতিষ্ঠান "আপন ভুবন পিকনিক এবং শুটিং স্পট" এর প্রয়োজনীয়তার ভিত্তিতে <strong>{{ $workOrder->client->company ?? 'N/A' }}</strong> এর নিকট নিম্নলিখিত কাজটি সম্পন্ন করার জন্য কার্যাদেশ প্রদান করছে।
    নিম্নে বর্ণিত কাজটি প্রদানের সময় মোট টাকার <strong>{{ bnNumber($workOrder->advance_percent) }}%</strong>
 
    পরিশোধ করা হবে এবং বাকি টাকা কার্যসম্পাদনের পরবর্তী পর্যায়ে পরিশোধ করা হবে।
</p>
<p class="text-justify">
    কার্যসম্পাদনের শেষ তারিখ সম্পাদনকারী ব্যক্তির সঙ্গে আলোচনা সাপেক্ষে 
    <strong>{{ bnDate($workOrder->delivery_date, 'd-m-Y') }}</strong>
 নির্ধারিত করা হয়েছে। 
    উক্ত কাজটি নির্ধারিত সময়ের মধ্যে অনাকাঙ্ক্ষিত কারণ ব্যতীত সুষ্ঠুভাবে সম্পন্ন না হলে <strong>{{ bnNumber($afteradvanced) }}%</strong>
 টাকা প্রদানের ক্ষেত্রে আমাদের কোম্পানি বিবেচনা করবে।
</p>

<h6>কাজের বিবরণ</h6>
<table>
    <thead>
        <tr>
            <th>ক্রমিক নং</th>
            <th>বিবরণ</th>
            <th>পরিমাণ/সংখ্যা</th>
            <th>টাকা</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($workOrder->work_items as $key => $item)
            @php $total += $item['price']; @endphp
            <tr>
                <td>{{ bnNumber($loop->iteration) }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ bnNumber($item['quantity']) }}</td>
                <td>{{ bnNumber(number_format((float)$item['price'], 2)) }}/-</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3" style="text-align: right;">মোট = </th>
            <th>{{ bnNumber(number_format((float)$total, 2)) }}/-</th>
        </tr>
    </tbody>
</table>

<h6>শর্তাবলীঃ</h6>
<ul>
    @foreach($workOrder->terms as $term)
        <li>{{ $term }}</li>
    @endforeach
</ul>

<table style="width:100%; margin-top:60px; border:none; border-collapse:collapse;">
    <tr style="border:none;">
        <td style="text-align:left; border:none;">
            -----------------------------<br>
           কার্যগ্রহণকারীর স্বাক্ষর
        </td>
        <td style="text-align:right; border:none;">
            -----------------------------<br>
            কার্যপ্রদাণকারীর স্বাক্ষর
        </td>
    </tr>
</table>


</body>
</html>
