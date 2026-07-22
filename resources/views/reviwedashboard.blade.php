<html>

<head>
    <title>GeeksForGeeks</title>
    <link rel="stylesheet" href="assets55/style.css" />
    <link rel="stylesheet" href="assets55/responsive.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <div class="logosec">
            <div class="logo">   @if($company_logo_one)
            <img src="{{ asset('storage/images/company/' . basename($company_logo_one)) }}" alt="Company Logo">
            @endif</div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png"
                class="icn menuicn" id="menuicn" alt="menu-icon" />
        </div>
        <div class="searchbar">
            <input type="text" placeholder="Search" />
            <div class="searchbtn">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png"
                    class="icn srchicn" alt="search-icon" />
            </div>
        </div>

        <div class="message">
            <div class="circle"></div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183322/8.png" class="icn" alt="" />
            <div class="dp">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png"
                    class="dpicn" alt="dp" />
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="navcontainer1">
            <nav class="nav">
                <div class="nav-upper-options">
                   <a href="{{ route('review.report.dashboard') }}"
   class="nav-option {{ request()->routeIs('review.report.dashboard') ? 'active' : '' }}">
    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182148/Untitled-design-(29).png"
        class="nav-img" />
    <h4>Dashboard</h4>
</a>

<a href="{{ route('review.report') }}"
   class="nav-option {{ request()->routeIs('review.report') ? 'active' : '' }}">
    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183320/5.png"
        class="nav-img" />
    <h4>Report</h4>
</a>

                    
                </div>
            </nav>
        </div>
        <div class="main">
            <div class="searchbar2">
                <input type="text" name="" id="" placeholder="Search" />
                <div class="searchbtn">
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png"
                        class="icn srchicn" alt="search-button" />
                </div>
            </div>

           



                <!-- CHARTS -->
                <div class="row">

                    <div class="col-md-6 mb-4">
                        <div class="card p-3 shadow-sm">
                            <h6 class="text-center">Ratings Overview</h6>
                            <canvas id="ratingChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card p-3 shadow-sm">
                            <h6 class="text-center">Visit Again</h6>
                            <canvas id="visitChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card p-3 shadow-sm">
                            <h6 class="text-center">Recommend</h6>
                            <canvas id="recommendChart"></canvas>
                        </div>
                    </div>

                </div>
           

            <div class="">
                <div class="report-header">
                    <h1 class="recent-Articles">Recent Reviews</h1>
                   
                     <a  href="{{ route('review.report') }}" class="view">
                       View All
                      </a>
                </div>

                <div class="">
                    <div class="table-responsive">
<table class="table table-bordered table-hover">

<thead class="">
<tr>

<th>#</th>
<th>Name</th>
<th>Mobile</th>

<th>Behaviour</th>
<th>Facility</th>
<th>Service</th>
<th>Pricing</th>

<th>Visit Again</th>
<th>Recommend</th>

<th>Notes</th>

<th>Date</th>

</tr>
</thead>

<tbody>

@foreach($reviews as $key => $review)

<tr>

<td>{{ $key+1 }}</td>

<td>
<b>{{ $review->name }}</b><br>
<small>{{ $review->email }}</small><br>
<small>{{ $review->address }}</small>
</td>

<td>{{ $review->mobile }}</td>


<!-- Behaviour Rating -->
<td>

@for($i=1;$i<=5;$i++)
@if($i <= $review->behaviour_rating)
<span style="color:#ffc107;">★</span>
@else
<span style="color:#ddd;">★</span>
@endif
@endfor

@if($review->behaviour_note)
<br>
<small class="text-danger">{{ $review->behaviour_note }}</small>
@endif

</td>


<!-- Facility Rating -->
<td>

@for($i=1;$i<=5;$i++)
@if($i <= $review->facility_rating)
<span style="color:#ffc107;">★</span>
@else
<span style="color:#ddd;">★</span>
@endif
@endfor

@if($review->facility_note)
<br>
<small class="text-danger">{{ $review->facility_note }}</small>
@endif

</td>


<!-- Service Rating -->
<td>

@for($i=1;$i<=5;$i++)
@if($i <= $review->service_rating)
<span style="color:#ffc107;">★</span>
@else
<span style="color:#ddd;">★</span>
@endif
@endfor

@if($review->service_note)
<br>
<small class="text-danger">{{ $review->service_note }}</small>
@endif

</td>


<!-- Pricing -->
<td>

@for($i=1;$i<=5;$i++)
@if($i <= $review->price_rating)
<span style="color:#ffc107;">★</span>
@else
<span style="color:#ddd;">★</span>
@endif
@endfor

@if($review->price_note)
<br>
<small class="text-danger">{{ $review->price_note }}</small>
@endif

</td>


<!-- Visit Again -->
<td>

@if($review->visit_again == 'yes')

<span class="badge bg-success">Yes</span>

@elseif($review->visit_again == 'no')

<span class="badge bg-danger">No</span>

@if($review->visit_reason)
<br>
<small class="text-danger">{{ $review->visit_reason }}</small>
@endif

@endif

</td>


<!-- Recommend -->
<td>

@if($review->recommend == 'yes')

<span class="badge bg-success">Yes</span>

@elseif($review->recommend == 'no')

<span class="badge bg-danger">No</span>

@if($review->recommend_reason)
<br>
<small class="text-danger">{{ $review->recommend_reason }}</small>
@endif

@endif

</td>


<!-- Notes -->
<td>

@if($review->note)
{{ $review->note }}
@endif

</td>


<td>

{{ date('d M Y', strtotime($review->created_at)) }}

</td>

</tr>

@endforeach

</tbody>
</table>
</div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets55/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// Ratings Chart
new Chart(document.getElementById('ratingChart'), {
    type: 'bar',
    data: {
        labels: ['Behaviour', 'Facility', 'Service', 'Pricing'],
        datasets: [{
            label: 'Average Rating',
            data: [
                {{ $avgBehaviour ?? 0 }},
                {{ $avgFacility ?? 0 }},
                {{ $avgService ?? 0 }},
                {{ $avgPrice ?? 0 }}
            ],
            backgroundColor: [
                '#8081AF',
                '#155F96',
                '#5CBBC1',
                '#ADD59D'
            ],
            hoverBackgroundColor: [
                '#6f70a0',
                '#124e7a',
                '#4aa9af',
                '#8fcf7f'
            ],
            borderRadius: 12,
            barThickness: 125
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 5,
                grid: {
                    color: '#f1f1f1'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
// Visit Again Chart
new Chart(document.getElementById('visitChart'), {
    type: 'pie',
    data: {
        labels: ['Yes', 'No'],
        datasets: [{
            data: [{{ $visitYes }}, {{ $visitNo }}]
        }]
    }
});

// Recommend Chart
new Chart(document.getElementById('recommendChart'), {
    type: 'doughnut',
    data: {
        labels: ['Yes', 'No'],
        datasets: [{
            data: [{{ $recommendYes }}, {{ $recommendNo }}]
        }]
    }
});

</script>
</body>

</html>