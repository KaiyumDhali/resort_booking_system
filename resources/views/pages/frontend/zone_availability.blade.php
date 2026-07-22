@extends('pages.frontend.layouts.app')
@section('content')

<style>

.booking-wrapper{
    max-width:1200px;
    margin:auto;
}

/* ===== MAP ===== */

.map-wrapper{
    position: relative;
    width:100%;
    margin:auto;
    transform: scale(1.2);
    transform-origin: top center;
}

.map-wrapper img{
    width:100%;
    display:block;
}

/* overlay */

.map-overlay{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    pointer-events:none;
}

.map-overlay svg{
    width:100%;
    height:100%;
}

/* zones */

path[id^="zone-"]{
    fill: transparent;
    transition:0.3s;
    cursor:pointer;
}

path.available{
    fill: rgba(0,255,0,0.35);
    pointer-events: all;
}

path.booked{
    fill: rgba(255,0,0,0.5);
    pointer-events:none;
}

/* selected zone */

path.selected-zone{
    stroke:#000;
    stroke-width:3;
}

/* ===== BADGE ===== */

.zone-badge{
    font-size:12px;
    font-weight:600;
    text-align:center;
}

.zone-capacity{
    background:#0d6efd;
    color:#fff;
    padding:3px 6px;
    border-radius:4px;
    display:inline-block;
}

.zone-booked{
    background:#dc3545;
    color:#fff;
    padding:3px 6px;
    border-radius:4px;
    margin-top:2px;
    display:block;
}

/* ===== CARD ===== */

.booking-card{
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

.zone-info{
    text-align:center;
    font-size:14px;
    margin-bottom:10px;
    color:#555;
}

</style>


<div class="mt-5 booking-wrapper">

<form method="POST" action="{{ route('frontend.book.spot') }}">

@csrf

<input type="hidden" name="spot_id" id="spot_id">


<!-- CUSTOMER INFO + DATE -->

<div class="row py-5">

<div class="col-md-12 py-5">

<div class="booking-card">

<h4 class="mb-3">Customer Information</h4>

<div class="mb-3 col-md-6">
<label>Name</label>
<input type="text" name="customer_name" class="form-control" required>
</div>

<div class="mb-3 col-md-6">
<label>Mobile</label>
<input type="text" name="customer_mobile" class="form-control" required>
</div>

<div class="mb-3">
<label>Address</label>
<input type="text" name="customer_address" class="form-control" required>
</div>
<div class="mb-3">
<label>Select Date</label>
<input type="date" name="booking_date" class="form-control" required>
</div>
</div>

</div>


</div>


<!-- MAP FULL WIDTH -->

<div class="booking-card mt-4">

<h4 class="text-center mb-2">Select Zone</h4>

<div class="zone-info">
Click a zone on the map to select it
</div>

<div class="map-wrapper">

<img src="{{ asset('images/map/map.png') }}">

<div class="map-overlay">
{!! file_get_contents(public_path('images/map/zones.svg')) !!}
</div>

</div>

</div>


<div class="text-center mt-4">

<button type="submit" class="theme-btn btn-style-one px-5">
Confirm Booking
</button>

</div>

</form>

</div>


@php

$zoneMapArray = [
1 => 'zone-1',
2 => 'zone-2',
3 => 'zone-3',
4 => 'zone-4',
5 => 'zone-5',
6 => 'zone-6'
];

$zoneCapacityArray = $zones->mapWithKeys(function($zone){
return [$zone->id => $zone->max_capacity];
})->toArray();

@endphp


<script>

document.addEventListener("DOMContentLoaded", function(){

var zoneMap = @json($zoneMapArray);
var zoneStatus = @json($availability);
var zoneCapacity = @json($zoneCapacityArray);

for (var dbId in zoneStatus){

    var svgId = zoneMap[dbId];
    if(!svgId) continue;

    var zone = document.getElementById(svgId);
    if(!zone) continue;

    zone.classList.add(zoneStatus[dbId].status);

    var bbox = zone.getBBox();
    var svg = zone.ownerSVGElement;

    /* foreignObject container */

    var foreign = document.createElementNS("http://www.w3.org/2000/svg","foreignObject");

 foreign.setAttribute("x", bbox.x + bbox.width/2 - 35);
foreign.setAttribute("y", bbox.y - 25);
foreign.setAttribute("width", 120);
foreign.setAttribute("height", 60);

    var div = document.createElement("div");
    div.className="zone-badge";

    /* capacity */

    var capacity = document.createElement("div");
    capacity.className="zone-capacity";
    capacity.innerHTML="👥 "+zoneCapacity[dbId];

    div.appendChild(capacity);

    /* booked */

    if(zoneStatus[dbId].status === 'booked'){

        var booked=document.createElement("div");
        booked.className="zone-booked";

        if(zoneStatus[dbId].dates){
            booked.innerHTML="Booked";
        }else{
            booked.innerHTML="Booked";
        }

        div.appendChild(booked);
    }

    foreign.appendChild(div);
    svg.appendChild(foreign);


    /* click */

    if(zoneStatus[dbId].status === 'available'){

        zone.style.pointerEvents="all";

        zone.addEventListener("click",(function(id){

            return function(){

                document.getElementById('spot_id').value=id;

                document.querySelectorAll("path[id^='zone-']").forEach(function(z){
                    z.classList.remove("selected-zone");
                });

                this.classList.add("selected-zone");

            }

        })(dbId));
    }
}

});

</script>

@endsection