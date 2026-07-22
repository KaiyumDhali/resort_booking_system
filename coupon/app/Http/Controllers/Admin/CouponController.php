<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class CouponController extends Controller
{


    public function index()
    {

        $coupons = Coupon::all();
        // dd($coupons);
        return view('admin.coupon_serial.index', compact('coupons'));
    }


    // single barcode qrcode print

    public function serialcodePrint(Request $request)
    {

        $print_type = $request->print_type;

        $productSerialID = $request->coupon_serial;

        // dd($productSerialID);

        $product_serials = Coupon::where('coupon_serial', $productSerialID)->first();
        // $productSerialStatus = Coupon::where('coupon_serial', $productSerialID)->update(['status' => 1]);
        $product_serial_qty = $request->quantity;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        if ($print_type == '1') {
            $html1 = view('admin.coupon_serial.print', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html1);
            // $dompdf->setPaper(array(2.4, 1.25), 'in', 'portrait');
            // $dompdf->setPaper(array(2, 3), 'in', 'landscape');
            $dompdf->setPaper(array(2, 3), 'portrait');
            // $dompdf->setPaper(array(2, 3), 'in', 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_qrcode.pdf');
        } elseif ($print_type == '2') {
            $html2 = view('admin.coupon_serial.barcodeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html2);
            // $dompdf->setPaper(array(2.4, 0.50), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode.pdf');
        } elseif ($print_type == '3') {
            $html3 = view('admin.coupon_serial.qrcodeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html3);
            // $dompdf->setPaper(array(2.4, 1.15), 'in', 'portrait');
            $dompdf->setPaper(array(3.9, 8), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_qrcode.pdf');
        }
    }

    // multiple barcode qrcode print

    public function rangeserialcodePrint(Request $request)
    {

        $print_type2 = $request->print_type2;

        $rangeStart = $request->rangestart;
        $rangeEnd = $request->rangeend;
        $product_serial_qty = $request->quantity;


        $product_serials = Coupon::whereBetween('coupon_serial', [$rangeStart, $rangeEnd])->get();

        // dd($product_serials);

        // $productSerialStatus = ProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->update(['status' => 2]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        if ($print_type2 == '1') {
            $html1 = view('admin.coupon_serial.rangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html1);
            // $dompdf->setPaper(array(2.4, 1.25), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_qrcode_range.pdf');
        } elseif ($print_type2 == '2') {
            $html2 = view('admin.coupon_serial.barcoderangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html2);
            // $dompdf->setPaper(array(2.4, 0.50), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_range.pdf');
        } elseif ($print_type2 == '3') {
            $html3 = view('admin.coupon_serial.qrcoderangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html3);
            // $dompdf->setPaper(array(2.4, 1.15), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_qrcode_range.pdf');
        }
    }


    public function create()
    {
        $allCategories = Category::pluck('category_name', 'id')->all();
        $allProducts = Product::pluck('product_name', 'id')->all();
        $allProducts2 = Product::where('status', 1)->select('id', 'product_name', 'product_code')->get()->toArray();
        //        dd($allProducts2);
        return view('admin.coupon_serial.add', compact('allProducts', 'allProducts2', 'allCategories'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric',
        ]);

        $couponSerial = new Coupon();
        $couponQty = $request->input('quantity');

        // Get the last inserted serial number
        $lastSerialNumber = $couponSerial->orderBy('id', 'desc')->value('coupon_serial');

        // If there's a last serial number, extract the numeric part
        $lastSerialNumberNumericPart = ($lastSerialNumber) ? intval(substr($lastSerialNumber, strrpos($lastSerialNumber, '-') + 1)) : 0;


        $serialNumbers = $this->generateSerialNumbers($couponQty, $lastSerialNumberNumericPart);

        $dataToInsert = [];
        foreach ($serialNumbers as $serialNumber) {
            $dataToInsert[] = [
                'coupon_serial' => $serialNumber,
                'status' => 0,
            ];
        }

        $couponSerial->insert($dataToInsert);

        return redirect()->route('couponserials.index')->with([
            'message' => 'Coupon Serial Successfully Generated!',
            'alert-type' => 'success'
        ]);
    }

    private function generateSerialNumbers($serialQty, $lastSerialNumberNumericPart)
    {
        $serialNumbers = [];

        for ($i = 1; $i <= $serialQty; $i++) {
            $serialNumbers[] =  str_pad($lastSerialNumberNumericPart + $i, 5, '0', STR_PAD_LEFT);
        }

        return $serialNumbers;
    }




    public function show(Coupon $coupon) {}



    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupon_serial.update', compact('coupon'));
    }


    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->status = $request->status;
        $coupon->save();

        return redirect()->route('couponserials.index')->with([
            'message' => 'successfully update !',
            'alert-type' => 'success'
        ]);
    }


    public function destroy(Coupon $coupon)
    {
        //
    }
}
