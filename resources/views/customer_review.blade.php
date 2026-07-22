<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ __('review.customer_review') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: sans-serif;
    background: #f5f6fa;
}

.review-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    padding: 10px;
}

.particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.6);
    animation-name: floatParticle;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}

@keyframes floatParticle {
    0% { transform: translateY(100vh); opacity: 0; }
    10% { opacity: 1; }
    100% { transform: translateY(-10vh); opacity: 0; }
}

.review-card {
    width: 100%;
    max-width: 700px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    background: linear-gradient(-45deg,#ffffff,#f3f8ff,#e8fff5,#ffffff);
    background-size: 400% 400%;
    animation: gradientMove 15s ease infinite;
    position: relative;
    z-index: 1;
}

@keyframes gradientMove {
    0% { background-position:0% 50%;}
    50% { background-position:100% 50%;}
    100% { background-position:0% 50%;}
}

.company-header{
text-align:center;
margin-bottom:-20px;
}

.company-header img{
max-height:60px;
margin-bottom:0px;
}

.star-rating{
display:flex;
justify-content:center;
align-items:center;
gap:20px;
font-size:32px;
direction:rtl;
 
}

.star-rating input{display:none;}

.star-rating label{
cursor:pointer;
color:#ddd;
transition:0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label{
color:#ffc107;
}

textarea{resize:none;}

@media (max-width:576px){
.star-rating{font-size:28px;}
}
</style>
</head>

<body>

<div class="review-container">

<div class="review-card">

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center">

        <!-- Logo Left -->
        <div>
            @if($company_logo_one)
                <img src="{{ asset('storage/images/company/'.basename($company_logo_one)) }}" height="50">
            @endif
        </div>

        <!-- Language Buttons Right -->
        <div>
            <a href="/lang/en" class="btn btn-sm btn-primary">English</a>
            <a href="/lang/bn" class="btn btn-sm btn-success">বাংলা</a>
        </div>

    </div>
</div>
<!-- {{ app()->getLocale() }} -->
<div class="company-header">
    


<h2 style="margin-bottom:-7px;">{{ $company_name }}</h2>
<p style="margin-bottom:-7px; ">{{ $company_address }}</p>
<p>Mobile: {{ $company_mobile }}</p>

</div>

<h4 class="text-center">{{ __('review.customer_review') }}</h4>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<form method="POST" action="{{ route('review.store') }}">
@csrf


<!-- Behaviour -->
<div class="">

<label class="form-label">{{ __('review.behaviour') }}</label>

<div class="d-flex align-items-center justify-content-center gap-2">

<span style="font-size:14px;">Poor</span>

<div class="star-rating">

<input type="radio" name="behaviour_rating" value="5" id="b5"><label for="b5">★</label>
<input type="radio" name="behaviour_rating" value="4" id="b4"><label for="b4">★</label>
<input type="radio" name="behaviour_rating" value="3" id="b3"><label for="b3">★</label>
<input type="radio" name="behaviour_rating" value="2" id="b2"><label for="b2">★</label>
<input type="radio" name="behaviour_rating" value="1" id="b1"><label for="b1">★</label>

</div>

<span style="font-size:14px;">Excellent</span>

</div>
<textarea name="behaviour_note" id="behaviour_note"
class="form-control  d-none"
placeholder="{{ __('review.wrong') }}"></textarea>

</div>


<!-- Facility -->
<div class="">

<label class="form-label">{{ __('review.facility') }}</label>
<div class="d-flex align-items-center justify-content-center gap-2">

<span style="font-size:14px;">Poor</span>

<div class="star-rating">

<input type="radio" name="facility_rating" value="5" id="f5"><label for="f5">★</label>
<input type="radio" name="facility_rating" value="4" id="f4"><label for="f4">★</label>
<input type="radio" name="facility_rating" value="3" id="f3"><label for="f3">★</label>
<input type="radio" name="facility_rating" value="2" id="f2"><label for="f2">★</label>
<input type="radio" name="facility_rating" value="1" id="f1"><label for="f1">★</label>

</div>

<span style="font-size:14px;">Excellent</span>

</div>

<textarea name="facility_note" id="facility_note"
class="form-control  d-none"
placeholder="{{ __('review.wrong') }}"></textarea>

</div>


<!-- Service -->
<div class="">

<label class="form-label">{{ __('review.service') }}</label>

<div class="d-flex align-items-center justify-content-center gap-2">

<span style="font-size:14px;">Poor</span>

<div class="star-rating">

<input type="radio" name="service_rating" value="5" id="s5"><label for="s5">★</label>
<input type="radio" name="service_rating" value="4" id="s4"><label for="s4">★</label>
<input type="radio" name="service_rating" value="3" id="s3"><label for="s3">★</label>
<input type="radio" name="service_rating" value="2" id="s2"><label for="s2">★</label>
<input type="radio" name="service_rating" value="1" id="s1"><label for="s1">★</label>

</div>

<span style="font-size:14px;">Excellent</span>

</div>

<textarea name="service_note" id="service_note"
class="form-control  d-none"
placeholder="{{ __('review.wrong') }}"></textarea>

</div>


<!-- Price -->
<div class="">

<label class="form-label">{{ __('review.price') }}</label>

<div class="d-flex align-items-center justify-content-center gap-2">

<span style="font-size:14px;">Poor</span>

<div class="star-rating">

<input type="radio" name="price_rating" value="5" id="p5"><label for="p5">★</label>
<input type="radio" name="price_rating" value="4" id="p4"><label for="p4">★</label>
<input type="radio" name="price_rating" value="3" id="p3"><label for="p3">★</label>
<input type="radio" name="price_rating" value="2" id="p2"><label for="p2">★</label>
<input type="radio" name="price_rating" value="1" id="p1"><label for="p1">★</label>

</div>

<span style="font-size:14px;">Excellent</span>

</div>

<textarea name="price_note" id="price_note"
class="form-control d-none"
placeholder="{{ __('review.wrong') }}"></textarea>

</div>


<!-- Visit Again -->
<div class="">

<label class="form-label">{{ __('review.visit_again') }}</label>

<div class="d-flex gap-3">

<div class="form-check mb-2">
<input class="form-check-input" type="radio" name="visit_again" value="yes">
<label class="form-check-label">{{ __('review.yes') }}</label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="visit_again" value="no">
<label class="form-check-label">{{ __('review.no') }}</label>
</div>

</div>

<textarea name="visit_reason" id="visit_reason"
class="form-control d-none"
placeholder="{{ __('review.why') }}"></textarea>

</div>


<!-- Recommend -->
<div class="mb-2">

<label class="form-label">{{ __('review.recommend') }}</label>

<div class="d-flex gap-3">

<div class="form-check">
<input class="form-check-input" type="radio" name="recommend" value="yes">
<label class="form-check-label">{{ __('review.yes') }}</label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="recommend" value="no">
<label class="form-check-label">{{ __('review.no') }}</label>
</div>

</div>

<textarea name="recommend_reason" id="recommend_reason"
class="form-control d-none"
placeholder="{{ __('review.why') }}"></textarea>

</div>
<!-- General Note -->
<div class="">
<label class="form-label">{{ __('review.additional_note') }}</label>
<textarea name="note" class="form-control" rows="3"></textarea>
</div>

<hr>

<h5>{{ __('review.customer_info') }}</h5>

<p class="text-muted">
{{ __('review.provide_info') }}
</p>


<div class="row g-3">

<div class="col-md-6">
<label class="form-label">{{ __('review.name') }}</label>
<input type="text" name="name" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">{{ __('review.email') }}</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">{{ __('review.mobile') }}</label>
<input type="text" name="mobile" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">{{ __('review.address') }}</label>
<input type="text" name="address" class="form-control">
</div>

</div>


<div class="text-end mt-4">
<button type="submit" class="btn btn-success">
{{ __('review.submit') }}
</button>
</div>


</form>

</div>

</div>


<script>document.addEventListener("DOMContentLoaded", function(){

function ratingLogic(name, noteId){

document.querySelectorAll(`input[name="${name}"]`).forEach(radio=>{
radio.addEventListener("change",function(){

let note=document.getElementById(noteId)

if(parseInt(this.value) <=3){
note.classList.remove("d-none")
}else{
note.classList.add("d-none")
}

})
})

}

ratingLogic("behaviour_rating","behaviour_note")
ratingLogic("facility_rating","facility_note")
ratingLogic("service_rating","service_note")
ratingLogic("price_rating","price_note")


// visit again
document.querySelectorAll('input[name="visit_again"]').forEach(el=>{
el.addEventListener("change",function(){

let note=document.getElementById("visit_reason")

if(this.value==="no"){
note.classList.remove("d-none")
}else{
note.classList.add("d-none")
}

})
})


document.querySelectorAll('input[name="recommend"]').forEach(el=>{
el.addEventListener("change",function(){

let note=document.getElementById("recommend_reason")

if(this.value==="no"){
note.classList.remove("d-none")
}else{
note.classList.add("d-none")
}

})
})

})
</script>


</body>
</html>