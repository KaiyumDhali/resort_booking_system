<?php

namespace App\Http\Controllers\Admin;

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

class ProductSerialController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSubCategories($category_id) {
        $subCategories = SubCategory::where('category_id', $category_id)->pluck('sub_category_name', 'id')->toArray();
        return response()->json($subCategories);
    }

    public function getProducts($subcategory_id) {
        $products = Product::where('sub_category_id', $subcategory_id)->pluck('product_name', 'id')->toArray();
        return response()->json($products);
    }

//    public function productSerialSearch(Request $request) {
//        if ($request->ajax()) {
//            $output = "";
//
//            $query = ProductSerial::query();
//
//            if ($request->has('productserialsearch')) {
//                $query->where('product_serial', 'LIKE', '%' . $request->search . "%");
//            }
//
//            if ($request->has('category')) {
//                $query->whereHas('category', function ($q) use ($request) {
//                    $q->where('category_name', 'LIKE', '%' . $request->category . "%");
//                });
//            }
//
//            if ($request->has('subcategory')) {
//                $query->whereHas('subCategory', function ($q) use ($request) {
//                    $q->where('sub_category_name', 'LIKE', '%' . $request->subcategory . "%");
//                });
//            }
//
//            $products = $query->with(['category', 'subCategory', 'brand', 'color', 'size', 'unit'])->get();
//
//            foreach ($products as $product) {
//                $output .= '<tr>' .
//                        '<td>' . $product->id . '</td>' .
//                        '</tr>';
//            }
//
//            return $output;
//        }
//    }
//    public function getRecipesMixFilter($cid = null, $pid = null, $tid = null, $did = null) {
//        $recipes = self::with(['member', 'platform', 'member.platform', 'member.duration', 'member.type'])
//                        ->whereActive(1)
//                        ->whereHas('member', function ($query) use ($cid, $pid, $tid, $did) {
//                            if ($cid) {
//                                $query->where('country_id', '=', $cid);
//                            }
//                            if ($pid) {
//                                $query->where('platform_id', '=', $pid);
//                            }
//                            if ($tid) {
//                                $query->where('type_id', '=', $tid);
//                            }
//                            if ($did) {
//                                $query->where('duration_id', '=', $did);
//                            }
//                        })->orderBy('updated_at', 'desc')->paginate(24);
//
//        return $recipes;
//    }



    public function getProductSerials($cid, $sid, $pid) {

//        dd($cid);
//        $productSerials = ProductSerial::with(['product.category','product.subCategory'])->get();

        $query = "SELECT 
	ps.id AS product_serial_id,
    ps.product_id AS product_id,
    ps.product_serial,
    p.product_name,
    p.product_code,
    c.category_name,
    sc.sub_category_name,
    ps.status
FROM 
    product_serials AS ps
JOIN 
    products AS p ON ps.product_id = p.id
JOIN 
    categories AS c ON p.category_id = c.id
JOIN 
    sub_categories AS sc ON p.sub_category_id = sc.id
WHERE (c.id='$cid' OR $cid=0) and (sc.id='$sid' OR $sid=0) and (p.id='$pid' OR $pid=0)";

//        WHERE (c.id='$cid' OR $cid=0) and (sc.id='$sid' OR $sid=0) and (p.id='$pid' OR $pid=0)

        $productSerials = DB::table(DB::raw("($query) AS subquery"))
                ->select('product_serial_id', 'product_id', 'product_serial', 'product_name', 'product_code', 'category_name', 'sub_category_name', 'status')
                ->get();
//        return view('admin.product_serial.index', compact('productSerials', 'allCategories'));
        return response()->json($productSerials);
    }

    public function index() {
//        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
//        $allProducts = Product::pluck('product_name', 'id')->all();
//        $productSerials = ProductSerial::with('product')->get();


        $allCategories = Category::pluck('category_name', 'id')->all();
        $productSerials = ProductSerial::with('product')->get();
        return view('admin.product_serial.index', compact('productSerials', 'allCategories'));
    }

//    public function serialcodePrint(Request $request, $productSerialID) {
//
//        $product_serial_qty = $request->quantity;
//        $product_serials = ProductSerial::find($productSerialID);
////        dd($product1);
////        return view('admin.product_serial.print', compact('product_serials', 'product_serial_qty'));
//
//
//        $pdf = PDF::loadView('admin.product_serial.print', compact('product_serials', 'product_serial_qty'));
//
//        return $pdf->download('product_barcode_qrcode.pdf');
//    }



    public function serialcodePrint(Request $request) {
        
        $productSerialID = $request->product_serial;
        $product_serials = ProductSerial::where('product_serial',$productSerialID)->first();
        $product_serial_qty = $request->quantity;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('admin.product_serial.print', compact('product_serials', 'product_serial_qty'))->render();
        $dompdf->loadHtml($html);

        // (Optional) Set the paper size and orientation
//        $dompdf->setPaper('A6', 'portrait');
        
        $dompdf->setPaper(array(2.4, 2.4), 'in', 'portrait');


        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        return $dompdf->stream('product_barcode_qrcode.pdf');
    }

    public function rangeserialcodePrint(Request $request) {

        $rangeStart = $request->rangestart;
        $rangeEnd = $request->rangeend;
        $product_serial_qty = $request->quantity;
        $product_serials = ProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->get();
//        return view('admin.product_serial.rangeprint', compact('product_serials', 'product_serial_qty'));
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('admin.product_serial.rangeprint', compact('product_serials', 'product_serial_qty'))->render();
        $dompdf->loadHtml($html);

        // (Optional) Set the paper size and orientation
//        $dompdf->setPaper('A6', 'portrait');
        
        $dompdf->setPaper(array(2.4, 2.4), 'in', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        return $dompdf->stream('product_barcode_qrcode_range.pdf');

    }
    
//    public function rangeserialcodePrint(Request $request) {
//
////        dd($request->all());
//
//        $rangeStart = $request->rangestart;
//        $rangeEnd = $request->rangeend;
//        $product_serial_qty = $request->quantity;
//        $product_serials = ProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->get();
//
//        return view('admin.product_serial.rangeprint', compact('product_serials', 'product_serial_qty'));
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $allProducts = Product::pluck('product_name', 'id')->all();
        return view('admin.product_serial.add', compact('allProducts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|integer|max:255',
            'quantity' => 'required|numeric',
//            'begin_number' => 'required|numeric',
//            'end_number' => 'required|numeric|gt:begin_number',
        ]);

        $productId = $request->input('product_id');
        $productSerial = new ProductSerial();
        $product = Product::select('products.*')->where('id', $productId)->first();

        // Get form inputs
        $productId = $productId;
        $serialQty = $request->input('quantity');
//        $beginNumber = $request->input('begin_number');
//        $endNumber = $request->input('end_number');
        $productcode = $product->product_code;

        // Get the last inserted serial number
        $lastSerialNumber = $productSerial->where('product_id', $productId)->orderBy('id', 'desc')->value('product_serial');

        // If there's a last serial number, extract the numeric part
        $lastSerialNumberNumericPart = ($lastSerialNumber) ? intval(substr($lastSerialNumber, strrpos($lastSerialNumber, '-') + 1)) : 0;

        // Generate unique product serial numbers
        $serialNumbers = $this->generateSerialNumbers($productId, $productcode, $serialQty, $lastSerialNumberNumericPart);

        $dataToInsert = [];
        foreach ($serialNumbers as $serialNumber) {
            $dataToInsert[] = [
                'product_id' => $productId,
                'product_serial' => $serialNumber,
                'product_code' => $productcode,
            ];
        }

        $productSerial->insert($dataToInsert);

        return redirect()->route('productserials.index')->with([
                    'message' => 'Successfully created!',
                    'alert-type' => 'success'
        ]);
    }

    private function generateSerialNumbers($productId, $productcode, $serialQty, $lastSerialNumberNumericPart) {
        $serialNumbers = [];

        for ($i = 1; $i <= $serialQty; $i++) {
            $serialNumbers[] = $productId . '-' . $productcode . '-SN-' . str_pad($lastSerialNumberNumericPart + $i, 5, '0', STR_PAD_LEFT);
        }

        return $serialNumbers;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductSerial  $productSerial
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSerial $productSerial) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductSerial  $productSerial
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSerial $productSerial) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductSerial  $productSerial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductSerial $productSerial) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductSerial  $productSerial
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSerial $productSerial) {
        //
    }

}
