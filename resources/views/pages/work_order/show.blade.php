@php
    $afteradvanced = 100 - (int) $workOrder->advance_percent;
@endphp

<x-default-layout>
<div class="container-fluid" style="font-family: 'SolaimanLipi', sans-serif;">

    <div class="card shadow-sm mt-4">
        <div class="card-body p-5">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h3 class="mb-1">কার্যানুরোধ পত্র</h3>
                <p class="mb-0">
                    ওয়ার্ক অর্ডার নং:
                    <strong>{{ bnNumber($workOrder->work_order_no) }}</strong>
                </p>
            </div>

            {{-- Top Info --}}
            <div class="row mb-4">
                <div class="col-md-6">

                    @if(!empty($workOrder->reference))
                        <p><strong>রেফারেন্স:</strong> {{ $workOrder->reference }}</p>
                    @endif

                    <p><strong>তারিখ:</strong> {{ bnDate($workOrder->issue_date) }}</p>

                    <p class="mb-0"><strong>{{ $workOrder->client->name ?? '' }}</strong></p>
                    <p class="mb-0">{{ $workOrder->client->designation ?? '' }}</p>
                    <p class="mb-0">{{ $workOrder->client->company ?? '' }}</p>
                    <p>{{ $workOrder->client->address ?? '' }}</p>

                    <p class="pt-2">
                        <strong>বিষয়: {{ $workOrder->subject }}</strong>
                    </p>
                </div>

                <div class="col-md-6 text-end">
                    <p class="fw-bold">অফিস কপি</p>
                </div>
            </div>

            {{-- Body --}}
            <p class="text-justify">
                আসসালামু আলাইকুম। আমরা ওয়ান্ডার পার্ক এন্ড ইকো রিসোর্ট কর্তৃপক্ষ নিম্নে বর্ণিত কাজটি
                <strong>{{ $workOrder->client->name ?? '' }}</strong>
                কে সম্পন্ন করার জন্য অনুরোধ জানাচ্ছি।
                নিম্নে বর্ণিত কাজটি প্রদানের সময় মোট টাকার
                <strong>{{ bnNumber($workOrder->advance_percent) }}%</strong>
                পরিশোধ করা হবে এবং বাকি টাকা কার্যসম্পাদনের পরবর্তী পর্যায়ে পরিশোধ করা হবে।
            </p>

            <p class="text-justify">
                কার্যসম্পাদনের শেষ তারিখ সম্পাদনকারী ব্যক্তির সঙ্গে আলোচনা সাপেক্ষে
                <strong>
                    {{ bnDate($workOrder->delivery_date, 'd-m-Y') }}
                </strong>
                নির্ধারিত করা হয়েছে।
                উক্ত কাজটি নির্ধারিত সময়ের মধ্যে অনাকাঙ্ক্ষিত কারণ ব্যতীত সুষ্ঠুভাবে সম্পন্ন না হলে
                <strong>{{ bnNumber($afteradvanced) }}%</strong>
                টাকা প্রদানের ক্ষেত্রে আমাদের কোম্পানি বিবেচনা করবে।
            </p>

            {{-- Work Items --}}
            <h6 class="fw-bold mt-4 mb-2">কাজের বিবরণ</h6>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="80">ক্রমিক নং</th>
                        <th>বিবরণ</th>
                        <th>পরিমাণ/সংখ্যা</th>
                        <th width="150" class="text-end">টাকা</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($workOrder->work_items as $key => $item)
                        @php $total += $item['price']; @endphp
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td class="text-end">{{ bnNumber(number_format($item['price'], 2)) }}/-</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="3" class="text-end">মোট =</th>
                        <th class="text-end">{{ bnNumber(number_format($total, 2)) }}/-</th>
                    </tr>
                </tbody>
            </table>

            {{-- Terms --}}
            <h6 class="fw-bold mt-4">শর্তাবলী</h6>
            <ul>
                @foreach($workOrder->terms as $term)
                    <li>{{ $term }}</li>
                @endforeach
            </ul>

            {{-- Signatures --}}
            <div class="row mt-5">
                <div class="col-md-6">
                    <p>-----------------------------</p>
                    <p class="mb-0">কার্যগ্রহণকারীর স্বাক্ষর</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>-----------------------------</p>
                    <p class="mb-0">কার্যপ্রদাণকারীর স্বাক্ষর</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="text-end mt-4">
                <a href="{{ route('work-orders.index') }}" class="btn btn-secondary btn-sm">
                    Back
                </a>
                <a href="{{ route('work-orders.pdf', $workOrder->id) }}"
                   class="btn btn-danger btn-sm" target="_blank">
                    Download PDF
                </a>
            </div>

        </div>
    </div>
</div>

<style>
@font-face {
    font-family: 'SolaimanLipi';
    src: url("{{ asset('fonts/solaimanlipi/solaimanlipi.ttf') }}") format('truetype');
}
body, table, th, td, h1, h6, p, li {
    font-family: 'SolaimanLipi', sans-serif;
}
.text-justify {
    text-align: justify;
}
</style>
</x-default-layout>
