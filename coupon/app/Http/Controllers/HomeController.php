<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\SubCategory;
use App\Models\ProductRegistration;
use App\Models\ProductComplain;
use App\Models\Visitor;
use Carbon\Carbon;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function __construct() {
    //     $this->middleware('permission:view dashboard', ['only' => ['index']]);
    //     $this->middleware('permission:create dashboard', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:update dashboard', ['only' => ['update', 'edit']]);
    //     $this->middleware('permission:delete dashboard', ['only' => ['destroy']]);
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        // $value = env('reza');
        // $value = config('global.reza');
        // $value = abcd;
        // dd($value);


        $couponCount = Coupon::count();
        $couponAvailedCount = Coupon::where('status', 1)->count();
        $couponRemainCount = Coupon::where('status', 0)->count();

        $productsCount = Product::count();
        $productComplains = ProductComplain::where('status', 0)->count();
        $productComplainSolved = ProductComplain::where('status', 1)->count();

        $productSerial = ProductSerial::where('status', 1)->get();

        $productRegistration = ProductRegistration::all()->count();
        $productComplain = ProductComplain::all()->groupBy('product_serial')->count();

        // dd($productComplain);

        if ($productComplain != 0) {
            $productComplainPercent = ($productComplain / $productRegistration) * 100;
        } else {
            $productComplainPercent = 0;
        }

        $totalVisit = Visitor::all()->count();
        $totalVisitToday = Visitor::whereDate('created_at', Carbon::today())->count();
        $uniqueVisit = Visitor::distinct('ip_address')->count('ip_address');



        //    $productComplainPercent = ($productComplain / $productRegistration) * 100;

        //        dd($productComplainPercent);
        //      return view('home');
        return view('admin.dashboard', compact('couponRemainCount','couponAvailedCount','couponCount', 'uniqueVisit', 'totalVisitToday', 'totalVisit', 'productsCount', 'productComplains', 'productComplainSolved', 'productComplainPercent'));
    }
}
