<?php

namespace App\Http\Controllers;

use App\Models\ProductRegistration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductRegistrationController extends Controller
{


    public function index()
    {
        $productregistration = ProductRegistration::all();
        return view('admin.product_registration.index', compact('productregistration'));
    }

    public function registration_success()
    {

        return view('admin.product_registration.registration_success');
    }


    public function create()
    {
        //
    }


    // public function store(Request $request)
    // {

    //     dd($request->all());

    //     $request->validate([
    //         'code' => 'required|string|max:255',

    //     ]);


    //     $couponSerialStatus = Coupon::where('coupon_serial', $request->coupon_serial)->update(['status' => 1]);

    //     return redirect()->route('registration_success')->with([
    //         'message' => 'Coupon Successfully Availed',
    //         'alert-type' => 'success'
    //     ]);
    // }

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        // Define your static code
        $staticCode = '00';

        // Check if the entered code matches the static code
        if ($request->code !== $staticCode) {
            return redirect()->back()->with([
                'message' => 'Invalid code entered.',
                'alert-type' => 'error'
            ]);
        }

        // Proceed with database update (assuming you're using 'coupon_serial')
        $couponSerialStatus = Coupon::where('coupon_serial', $request->coupon_serial)
            ->update(['status' => 1]);

        return redirect()->route('registration_success')->with([
            'message' => 'Coupon Successfully Availed',
            'alert-type' => 'success'
        ]);
    }



    public function show(ProductRegistration $productRegistration)
    {
        //
    }


    public function edit(ProductRegistration $productRegistration)
    {
        //
    }


    public function update(Request $request, ProductRegistration $productRegistration)
    {
        //
    }


    public function destroy(ProductRegistration $productRegistration)
    {
        //
    }
}
