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
use Carbon\Carbon;

class ProductSerialController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view productserial', ['only' => ['index']]);
        $this->middleware('permission:create productserial', ['only' => ['create', 'store']]);
        $this->middleware('permission:update productserial', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete productserial', ['only' => ['destroy']]);
    }


    public function getSubCategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', $category_id)->pluck('sub_category_name', 'id')->toArray();
        return response()->json($subCategories);
    }


    public function getProducts($subcategory_id)
    {
        $products = Product::where('sub_category_id', $subcategory_id)->pluck('product_name', 'id')->toArray();
        // $products = Product::where('status', 1)->select('id', 'product_name', 'product_code')->get()->toArray();
        return response()->json($products);
    }


    public function getProductSerials($cid, $sid, $pid, $pris)
    {

        $query = "SELECT 
        ps.id AS product_serial_id,
        ps.product_id AS product_id,
        ps.product_serial,
        p.product_name,
        p.product_code,
        c.category_name,
        sc.sub_category_name,
        ps.status,
        DATE(ps.created_at) as createdDate
    FROM 
        product_serials AS ps
    JOIN 
        products AS p ON ps.product_id = p.id
    JOIN 
        categories AS c ON p.category_id = c.id
    JOIN 
        sub_categories AS sc ON p.sub_category_id = sc.id
    WHERE (c.id='$cid' OR $cid=0) and (sc.id='$sid' OR $sid=0) and (p.id='$pid' OR $pid=0) and (ps.status='$pris' OR $pris=0)";


        $productSerials = DB::table(DB::raw("($query) AS subquery"))
            ->select('product_serial_id', 'product_id', 'product_serial', 'product_name', 'product_code', 'category_name', 'sub_category_name', 'status', 'createdDate')
            ->orderBy('subquery.product_serial_id', 'desc')
            ->get();

        //        return view('admin.product_serial.index', compact('productSerials', 'allCategories'));
        return response()->json($productSerials);
    }

    public function index()
    {
        $allCategories = Category::pluck('category_name', 'id')->all();
        $productSerials = ProductSerial::with('product')->get();
        return view('admin.product_serial.index', compact('productSerials', 'allCategories'));
    }


    // single barcode qrcode print

    public function serialcodePrint(Request $request)
    {

        $print_type = $request->print_type;

        $productSerialID = $request->product_serial;
        $product_serials = ProductSerial::where('product_serial', $productSerialID)->first();
        $productSerialStatus = ProductSerial::where('product_serial', $productSerialID)->update(['status' => 2]);
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
            $html2 = view('admin.product_serial.barcodeprint', compact('product_serials', 'product_serial_qty'))->render();
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

    public function rangeserialcodePrint(Request $request)
    {

        $print_type2 = $request->print_type2;

        $rangeStart = $request->rangestart;
        $rangeEnd = $request->rangeend;
        $product_serial_qty = $request->quantity;
        $product_serials = ProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->get();
        $productSerialStatus = ProductSerial::whereBetween('product_serial', [$rangeStart, $rangeEnd])->update(['status' => 2]);
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
            $html2 = view('admin.product_serial.barcoderangeprint', compact('product_serials', 'product_serial_qty'))->render();
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
    public function create()
    {

        $allCategories = Category::pluck('category_name', 'id')->all();


        $allProducts = Product::pluck('product_name', 'id')->all();
        $allProducts2 = Product::where('status', 1)->select('id', 'product_name', 'product_code')->get()->toArray();
        //        dd($allProducts2);
        return view('admin.product_serial.add', compact('allProducts', 'allProducts2', 'allCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|max:255',
            'quantity' => 'required|numeric',
            //            'begin_number' => 'required|numeric',
            //            'end_number' => 'required|numeric|gt:begin_number',
        ]);

        $productId = $request->input('product_id');
        //        dd($productId);
        $productiondate = $request->input('productiondate');

        $carbonDate = Carbon::parse($productiondate);
        $production_date = $carbonDate->format('m-Y');

        $productSerial = new ProductSerial();
        //        $product = Product::select('products.*')->where('id', $productId)->first();
        //        $productbrand = $product->brand->brand_name;
        $product = Product::select('products.*', 'brands.brand_code', 'categories.id', 'sub_categories.sub_category_code', 'productmodels.model_code')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('productmodels', 'products.productmodel_id', '=', 'productmodels.id')
            ->where('products.id', $productId)
            ->first();
        //        dd($product);
        // Get form inputs
        $productId = $productId;
        $serialQty = $request->input('quantity');
        //        $beginNumber = $request->input('begin_number');
        //        $endNumber = $request->input('end_number');

        $productbrand = substr($product->brand_name, 0, 3);

        $productcode = $product->product_code;

        $categorieId = $product->category_id;
        $subCategoryCode = $product->sub_category_code;
        $brandCode = strtoupper($product->brand_code);
        $modelCode = $product->model_code;

        // Get the last inserted serial number
        $lastSerialNumber = $productSerial->where('product_id', $productId)->orderBy('id', 'desc')->value('product_serial');

        // If there's a last serial number, extract the numeric part
        $lastSerialNumberNumericPart = ($lastSerialNumber) ? intval(substr($lastSerialNumber, strrpos($lastSerialNumber, '-') + 1)) : 0;

        // Generate unique product serial numbers
        //        $serialNumbers = $this->generateSerialNumbers($productbrand, $production_date, $productcode, $serialQty, $lastSerialNumberNumericPart);

        $serialNumbers = $this->generateSerialNumbers($categorieId, $subCategoryCode, $brandCode, $modelCode, $production_date, $serialQty, $lastSerialNumberNumericPart);

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
            'message' => 'Product Serial Successfully Generated!',
            'alert-type' => 'success'
        ]);
    }

    private function generateSerialNumbers($categorieId, $subCategoryCode, $brandCode, $modelCode, $production_date, $serialQty, $lastSerialNumberNumericPart)
    {
        $serialNumbers = [];

        for ($i = 1; $i <= $serialQty; $i++) {
            $serialNumbers[] = $categorieId . '-' . $subCategoryCode . '-' . $brandCode . '-' . $modelCode . '-' . $production_date . '-' . str_pad($lastSerialNumberNumericPart + $i, 5, '0', STR_PAD_LEFT);
        }

        return $serialNumbers;
    }

    public function show(ProductSerial $productSerial)
    {
        //
    }

    public function edit(ProductSerial $productSerial)
    {
        //
    }

    public function update(Request $request, ProductSerial $productSerial)
    {
        //
    }

    public function destroy(ProductSerial $productSerial)
    {
        //
    }
}
