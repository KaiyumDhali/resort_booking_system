<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductSerial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;


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

    public function productSerialSearch(Request $request) {
        if ($request->ajax()) {
            $output = "";

            $query = ProductSerial::query();

            if ($request->has('productserialsearch')) {
                $query->where('product_serial', 'LIKE', '%' . $request->search . "%");
            }

            if ($request->has('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('category_name', 'LIKE', '%' . $request->category . "%");
                });
            }

            if ($request->has('subcategory')) {
                $query->whereHas('subCategory', function ($q) use ($request) {
                    $q->where('sub_category_name', 'LIKE', '%' . $request->subcategory . "%");
                });
            }

            $products = $query->with(['category', 'subCategory', 'brand', 'color', 'size', 'unit'])->get();

            foreach ($products as $product) {
                $output .= '<tr>' .
                        '<td>' . $product->id . '</td>' .
                        '</tr>';
            }

            return $output;
        }
    }

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

    

    public function getProductSerials() {
        $productSerials = ProductSerial::with(['product.category','product.subCategory'])->get();
        return response()->json($productSerials);
    }

    public function index() {
//        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
//        $allProducts = Product::pluck('product_name', 'id')->all();
        
//        $productSerials = ProductSerial::with('product')->get();
//        dd($productSerials);

        $allCategories = Category::pluck('category_name', 'id')->all();
        $productSerials = ProductSerial::with('product')->get();
        return view('admin.product_serial.index', compact('productSerials', 'allCategories'));
    }

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
