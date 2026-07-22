<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerReview;
use App\Models\CompanySetting; // company setting model

class CustomerReviewController extends Controller
{
    public function create()
    {
        $companySetting = CompanySetting::where('status', 1)
            ->orderBy('id','desc')
            ->first();

        $data = [
            'company_name' => $companySetting->company_name ?? '',
            'company_address' => $companySetting->company_address ?? '',
            'company_logo_one' => $companySetting->company_logo_one ?? '',
            'company_mobile' => $companySetting->company_mobile ?? ''
        ];
// dd($data['company_logo_one']);
        return view('customer_review', $data);
    }

   public function store(Request $request)
{

    $request->validate([
        'name' => 'required'
        
    ]);

    CustomerReview::create([

        'behaviour_rating' => $request->behaviour_rating,
        'behaviour_note' => $request->behaviour_note,

        'facility_rating' => $request->facility_rating,
        'facility_note' => $request->facility_note,

        'service_rating' => $request->service_rating,
        'service_note' => $request->service_note,

        'price_rating' => $request->price_rating,
        'price_note' => $request->price_note,

        'visit_again' => $request->visit_again,
        'visit_reason' => $request->visit_reason,

        'recommend' => $request->recommend,
        'recommend_reason' => $request->recommend_reason,

        'note' => $request->note,

        'name' => $request->name,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'address' => $request->address

    ]);

    return back()->with('success','Review Submitted Successfully');

}

 public function report(Request $request)
{

    $query = CustomerReview::query();

    if($request->name){
        $query->where('name','like','%'.$request->name.'%');
    }

    if($request->mobile){
        $query->where('mobile','like','%'.$request->mobile.'%');
    }

    if($request->behaviour_rating){
        $query->where('behaviour_rating',$request->behaviour_rating);
    }

    if($request->facility_rating){
        $query->where('facility_rating',$request->facility_rating);
    }

    if($request->service_rating){
        $query->where('service_rating',$request->service_rating);
    }
    if($request->price_rating){
        $query->where('price_rating',$request->price_rating);
    }

    if($request->visit_again){
        $query->where('visit_again',$request->visit_again);
    }

    if($request->recommend){
        $query->where('recommend',$request->recommend);
    }

    if($request->from_date){
        $query->whereDate('created_at','>=',$request->from_date);
    }

    if($request->to_date){
        $query->whereDate('created_at','<=',$request->to_date);
    }
$reviews = $query->latest()->get();

/* =========================
   DASHBOARD CALCULATION
========================= */

$avgBehaviour = round($reviews->avg('behaviour_rating'), 1);
$avgFacility  = round($reviews->avg('facility_rating'), 1);
$avgService   = round($reviews->avg('service_rating'), 1);
$avgPrice     = round($reviews->avg('price_rating'), 1);

$visitYes = $reviews->where('visit_again','yes')->count();
$visitNo  = $reviews->where('visit_again','no')->count();

$recommendYes = $reviews->where('recommend','yes')->count();
$recommendNo  = $reviews->where('recommend','no')->count();

/* ========================= */

$companySetting = CompanySetting::where('status',1)
    ->orderBy('id','desc')
    ->first();

$company_name = $companySetting->company_name ?? '';
$company_address = $companySetting->company_address ?? '';
$company_logo_one = $companySetting->company_logo_one ?? '';
$company_mobile = $companySetting->company_mobile ?? '';

return view('reviewsreport', compact(
    'reviews',
    'company_name',
    'company_address',
    'company_logo_one',
    'company_mobile',

    // 👇 NEW DATA
    'avgBehaviour',
    'avgFacility',
    'avgService',
    'avgPrice',
    'visitYes',
    'visitNo',
    'recommendYes',
    'recommendNo'
));

}

public function dashboard(){
      $query = CustomerReview::query();
      $reviews = $query->latest()->get();

/* =========================
   DASHBOARD CALCULATION
========================= */

$avgBehaviour = round($reviews->avg('behaviour_rating'), 1);
$avgFacility  = round($reviews->avg('facility_rating'), 1);
$avgService   = round($reviews->avg('service_rating'), 1);
$avgPrice     = round($reviews->avg('price_rating'), 1);

$visitYes = $reviews->where('visit_again','yes')->count();
$visitNo  = $reviews->where('visit_again','no')->count();

$recommendYes = $reviews->where('recommend','yes')->count();
$recommendNo  = $reviews->where('recommend','no')->count();

/* ========================= */

$companySetting = CompanySetting::where('status',1)
    ->orderBy('id','desc')
    ->first();

$company_name = $companySetting->company_name ?? '';
$company_address = $companySetting->company_address ?? '';
$company_logo_one = $companySetting->company_logo_one ?? '';
$company_mobile = $companySetting->company_mobile ?? '';
    return view('reviwedashboard', compact(
    'reviews',
    'company_name',
    'company_address',
    'company_logo_one',
    'company_mobile',

    // 👇 NEW DATA
    'avgBehaviour',
    'avgFacility',
    'avgService',
    'avgPrice',
    'visitYes',
    'visitNo',
    'recommendYes',
    'recommendNo'
));
}
}