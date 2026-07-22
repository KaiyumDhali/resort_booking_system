<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductSerial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\OtherProduct;
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
use App\Models\OtherProductSerial;



class OtherProductSerialController extends Controller
{




    public function index()
    {
        $allCategories = Category::pluck('category_name', 'id')->all();
        $otherProductSerials = OtherProductSerial::with('otherproduct')->get();
        // dd($productSerials);
        return view('admin.product_serial.other_product_serial_list', compact('otherProductSerials', 'allCategories'));
    }

    public function create()
    {

        $allCategories = Category::pluck('category_name', 'id')->all();


        $allProducts = OtherProduct::pluck('product_name', 'id')->all();
        $allProducts2 = OtherProduct::where('status', 1)->select('id', 'product_name', 'product_code')->get()->toArray();
        // dd($allProducts);
        return view('admin.product_serial.other_product_serial_add', compact('allProducts', 'allProducts2', 'allCategories'));
    }



    public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'product_id' => 'required|integer|max:255',
            'quantity' => 'required|numeric',
        ]);

        $productId = $request->input('product_id');
        $productiondate = $request->input('productiondate');

        $carbonDate = Carbon::parse($productiondate);
        $production_date = $carbonDate->format('m-Y');

        $productSerial = new OtherProductSerial();

        $other_product = OtherProduct::select('other_products.*')
            ->where('other_products.id', $productId)
            ->first();

        $serialQty = $request->input('quantity');
        $productcode = $other_product->product_code;

        $lastSerialNumber = $productSerial->where('product_id', $productId)->orderBy('id', 'desc')->value('product_serial');
        $lastSerialNumberNumericPart = ($lastSerialNumber) ? intval(substr($lastSerialNumber, strrpos($lastSerialNumber, '-') + 1)) : 0;

        $serialNumbers = $this->generateSerialNumbers($productcode, $serialQty, $lastSerialNumberNumericPart);

        $dataToInsert = [];
        foreach ($serialNumbers as $serialNumber) {
            $dataToInsert[] = [
                'product_id' => $productId,
                'product_serial' => $serialNumber,
                'product_code' => $productcode,
            ];
        }

        $productSerial->insert($dataToInsert);

        return redirect()->route('other_product_serial_list')->with([
            'message' => 'Product Serial Successfully Generated!',
            'alert-type' => 'success'
        ]);
    }

    private function generateSerialNumbers($product_code, $serialQty, $lastSerialNumberNumericPart)
    {
        $serialNumbers = [];

        for ($i = 1; $i <= $serialQty; $i++) {
            $serialNumbers[] = $product_code . '-' . str_pad($lastSerialNumberNumericPart + $i, 5, '0', STR_PAD_LEFT);
        }
        return $serialNumbers;
    }


    // single barcode qrcode print

    public function otherSerialcodePrint(Request $request)
    {

        $print_type = $request->print_type;
        
        $productSerialID = $request->product_serial;
        
        // dd($productSerialID);

        $product_serials = OtherProductSerial::where('product_serial', $productSerialID)->first();
        $productSerialStatus = OtherProductSerial::where('product_serial', $productSerialID)->update(['status' => 2]);
        $product_serial_qty = $request->quantity;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        if ($print_type == '1') {
            $html1 = view('admin.product_serial.print', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html1);
            // $dompdf->setPaper(array(2.4, 1.25), 'in', 'portrait');
            // $dompdf->setPaper(array(2, 3), 'in', 'landscape');
            $dompdf->setPaper(array(2, 3), 'portrait');
            // $dompdf->setPaper(array(2, 3), 'in', 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_qrcode.pdf');
        } elseif ($print_type == '2') {
            $html2 = view('admin.product_serial.other_barcodeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html2);
            // $dompdf->setPaper(array(2.4, 0.50), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode.pdf');
        } elseif ($print_type == '3') {
            $html3 = view('admin.product_serial.qrcodeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html3);
            // $dompdf->setPaper(array(2.4, 1.15), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_qrcode.pdf');
        }
    }



    // multiple barcode qrcode print

    public function otherRangeserialcodePrint(Request $request)
    {

        $print_type2 = $request->print_type2;

        $rangeStart = $request->rangestart;
        $rangeEnd = $request->rangeend;
        $product_serial_qty = $request->quantity;
        $product_serials = OtherProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->get();
        $productSerialStatus = OtherProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->update(['status' => 2]);
        //        return view('admin.product_serial.rangeprint', compact('product_serials', 'product_serial_qty'));
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        if ($print_type2 == '1') {
            $html1 = view('admin.product_serial.rangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html1);
            // $dompdf->setPaper(array(2.4, 1.25), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_qrcode_range.pdf');
        } elseif ($print_type2 == '2') {
            $html2 = view('admin.product_serial.other_barcoderangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html2);
            // $dompdf->setPaper(array(2.4, 0.50), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_barcode_range.pdf');
        } elseif ($print_type2 == '3') {
            $html3 = view('admin.product_serial.qrcoderangeprint', compact('product_serials', 'product_serial_qty'))->render();
            $dompdf->loadHtml($html3);
            // $dompdf->setPaper(array(2.4, 1.15), 'in', 'portrait');
            $dompdf->setPaper(array(2, 3), 'portrait');
            $dompdf->render();
            return $dompdf->stream('product_qrcode_range.pdf');
        }
    }

    public function show(OtherProductSerial $otherProductSerial)
    {
        //
    }


    public function edit(OtherProductSerial $otherProductSerial)
    {
        //
    }



    public function update(Request $request, OtherProductSerial $otherProductSerial)
    {
        //
    }



    public function destroy(OtherProductSerial $otherProductSerial)
    {
        //
    }
}
