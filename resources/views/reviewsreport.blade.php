<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Review Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets55/style.css" />
    <link rel="stylesheet" href="assets55/responsive.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: sans-serif;
    background: #f4f6f9;
    position: relative;
    overflow-x: hidden;
}
.card {
    border-radius: 12px;
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
/* Floating particles */
.particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.6);
    animation-name: floatParticle;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}

@keyframes floatParticle {
    0% { transform: translateY(100vh) translateX(0px); opacity: 0; }
    10% { opacity: 1; }
    100% { transform: translateY(-10vh) translateX(50px); opacity: 0; }
}

/* Card */
.report-card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 10px;
    padding: 20px;
    background: #fff;
    position: relative;
    z-index: 1;
}

/* Company Header */
.company-header {
    text-align: center;
    border-bottom: 2px solid #ddd;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.company-header img {
    max-height: 70px;
    margin-bottom: 8px;
}

.company-header h3 {
    margin: 0;
    font-weight: 600;
}

.company-header p {
    margin: 2px 0;
    font-size: 14px;
    color: #555;
}

/* Report title */
.report-title {
    font-weight: 600;
    text-align: center;
    margin-bottom: 15px;
}

/* Table */
.table th, .table td {
    font-size: 14px;
}

.badge {
    font-size: 12px;
    padding: 6px 10px;
}

/* Responsive for mobile */
@media (max-width: 576px) {
    .company-header h3 { font-size: 20px; }
    .table th, .table td { font-size: 12px; }
}
</style>
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
                    <a href="{{ route('review.report.dashboard') }}" class="nav-option option1">
                        <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182148/Untitled-design-(29).png"
                            class="nav-img" alt="dashboard" />
                        <h4 class="text-black">Dashboard</h4>
                      </a>

                   <a href="{{ route('review.report') }}" class="nav-option option3">
    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183320/5.png"
        class="nav-img" alt="report" />
    <h4>Report</h4>
</a>

                    
                </div>
            </nav>
        </div>
   <div class="main">      
<div class=" mt-4">

    <!-- Floating particles -->
    <div class="particle" style="width:6px;height:6px;top:80%; left:10%; animation-duration:12s;"></div>
    <div class="particle" style="width:4px;height:4px;top:90%; left:30%; animation-duration:10s;"></div>
    <div class="particle" style="width:5px;height:5px;top:70%; left:60%; animation-duration:14s;"></div>
    <div class="particle" style="width:3px;height:3px;top:85%; left:80%; animation-duration:9s;"></div>
    <div class="particle" style="width:6px;height:6px;top:75%; left:50%; animation-duration:11s;"></div>

    <!-- Card -->
    <!--<div class="card report-card">-->

        <!-- Company Header -->
    <!--    <div class="company-header">-->
    <!--        @if($company_logo_one)-->
    <!--        <img src="{{ asset('storage/images/company/' . basename($company_logo_one)) }}" alt="Company Logo">-->
    <!--        @endif-->
    <!--        <h3>{{ $company_name }}</h3>-->
    <!--        <p>{{ $company_address }}</p>-->
    <!--        <p>Mobile: {{ $company_mobile }}</p>-->
    <!--    </div>-->

        <h4 class="report-title">Customer Review Report</h4>

    
<form method="GET" class="mb-4">

<div class="row g-2">

<div class="col-md-2">
    <div class="mb-2">
        <label for="name" class="form-label fw-semibold">Name</label>
        <input 
            type="text"
            id="name"
            name="name"
            class="form-control "
            placeholder="Enter name"
            value="{{ request('name') }}"
        >
    </div>
</div>

<div class="col-md-2">
    <label for="name" class="form-label fw-semibold">Mobile</label>
<input type="text" name="mobile" class="form-control"
placeholder="Mobile"
value="{{ request('mobile') }}">
</div>

<div class="col-md-2">
    <label for="name" class="form-label fw-semibold">Behaviour</label>
<select name="behaviour_rating" class="form-control">
<option value="">Select Rating</option>
@for($i=1;$i<=5;$i++)
<option value="{{$i}}" {{ request('behaviour_rating')==$i?'selected':'' }}>
{{$i}} Star
</option>
@endfor
</select>
</div>
<div class="col-md-2">
    <label for="name" class="form-label fw-semibold">Facility</label>
<select name="facility_rating" class="form-control">
<option value="">Select Rating</option>
@for($i=1;$i<=5;$i++)
<option value="{{$i}}" {{ request('facility_rating')==$i?'selected':'' }}>
{{$i}} Star
</option>
@endfor
</select>
</div>
<div class="col-md-2">
    <label for="name" class="form-label fw-semibold">Service</label>
<select name="service_rating" class="form-control">
<option value="">Select Rating</option>
@for($i=1;$i<=5;$i++)
<option value="{{$i}}" {{ request('service_rating')==$i?'selected':'' }}>
{{$i}} Star
</option>
@endfor
</select>
</div>
<div class="col-md-2">
    <label for="name" class="form-label fw-semibold">Pricing</label>
<select name="price_rating" class="form-control">
<option value="">Select Rating</option>
@for($i=1;$i<=5;$i++)
<option value="{{$i}}" {{ request('price_rating')==$i?'selected':'' }}>
{{$i}} Star
</option>
@endfor
</select>
</div>

<div class="col-md-2">
     <label for="name" class="form-label fw-semibold">Visit Again</label>
<select name="visit_again" class="form-control">
<option value="">Select (Yes/No)</option>
<option value="yes" {{ request('visit_again')=='yes'?'selected':'' }}>Yes</option>
<option value="no" {{ request('visit_again')=='no'?'selected':'' }}>No</option>
</select>
</div>
<div class="col-md-2">
     <label for="name" class="form-label fw-semibold">Recommend</label>
<select name="recommend" class="form-control">
<option value="">Select (Yes/No)</option>
<option value="yes" {{ request('recommend')=='yes'?'selected':'' }}>Yes</option>
<option value="no" {{ request('recommend')=='no'?'selected':'' }}>No</option>
</select>
</div>

<div class="col-md-2">
     <label for="name" class="form-label fw-semibold">Start Date</label>
<input type="date" name="from_date" class="form-control"
value="{{ request('from_date') }}">
</div>

<div class="col-md-2">
     <label for="name" class="form-label fw-semibold">End Date</label>
<input type="date" name="to_date" class="form-control"
value="{{ request('to_date') }}">
</div>
<div class="col-md-2 ms-auto d-flex align-items-center mt-4 gap-4">
    <button class="btn btn-primary btn-sm">Search</button>
    <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
</div>
</div>


</form>
       <div class="table-responsive">
<table class="table table-bordered table-hover">

<thead class="table-dark">
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
            ]
        }]
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
<script src="./assets55/index.js"></script>
</body>

</html>