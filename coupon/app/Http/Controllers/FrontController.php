<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductRegistration;
use App\Models\ProductComplain;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{

    // public function findproduct($psid)
    // {

    //     $find_coupons = Coupon::select('coupons.*')->where('coupons.coupon_serial', $psid)->first();

    //     dd($find_coupons->status);

    //     // $find_product = ProductSerial::select('product_serials.*')->with('product')->where('product_serials.product_serial', $psid)->first();
    //     // $product_serial = ProductRegistration::where('product_registrations.product_serial', $psid)->value('product_serial');
    //     // $registration_id = ProductRegistration::where('product_registrations.product_serial', $psid)->value('id');

    //     if ($find_coupons->status == null) {

    //         // return view('front.productregistration', compact('find_coupons'));
    //         return view('front.productcomplain', compact('find_coupons'))->with([
    //             'message' => 'Coupon Not Valid',
    //             'alert-type' => 'success'
    //         ]);
    //     } elseif ($find_coupons->status == 0) {

    //         return view('front.productregistration', compact('find_coupons'));
    //     } elseif ($find_coupons->status == 1) {


    //         $find_coupons = Coupon::select('coupons.*')->where('coupons.coupon_serial', $psid)->first();

    //         // $productComplains = ProductComplain::where('product_serial', $product_serial)->get();

    //         return view('front.productcomplain', compact('find_coupons'))->with([
    //             'message' => 'Coupon Already Availed',
    //             'alert-type' => 'success'
    //         ]);
    //     }
    // }


    public function findproduct($psid)
    {
        // Find the coupon by serial number
        $find_coupons = Coupon::where('coupon_serial', $psid)->first();

        // Check if the coupon exists
        if (!$find_coupons) {
            return view('front.productcomplain')->with([
                'message' => 'Coupon Not Found',
                'alert-type' => 'error'
            ]);
        }

        // Check status and return appropriate view
        if (is_null($find_coupons->status)) {
            return view('front.productcomplain', compact('find_coupons'))->with([
                'message' => 'Coupon Not Valid',
                'alert-type' => 'warning'
            ]);
        } elseif ($find_coupons->status == 0) {
            return view('front.productregistration', compact('find_coupons'));
        } elseif ($find_coupons->status == 1) {
            return view('front.productcomplain', compact('find_coupons'))->with([
                'message' => 'Coupon Already Availed',
                'alert-type' => 'warning'
            ]);
        }

        // Optional: handle unexpected status values
        return view('front.productcomplain')->with([
            'message' => 'Unexpected Coupon Status',
            'alert-type' => 'error'
        ]);
    }
}
