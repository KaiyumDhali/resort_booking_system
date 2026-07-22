<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductSerial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductSerialController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $productSerials = ProductSerial::all();
        return view('admin.product_serial.index', compact('productSerials'));
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
            'begin_number' => 'required|numeric',
            'end_number' => 'required|numeric|gt:begin_number',
        ]);

        $productId = $request->input('product_id');
        $productSerial = new ProductSerial();
        $product = Product::select('products.*')->where('id', $productId)->first();

        // Get form inputs
        $productId = $productId;
        $beginNumber = $request->input('begin_number');
        $endNumber = $request->input('end_number');
        $productcode = $product->product_code;

        // Get the last inserted serial number
        $lastSerialNumber = $productSerial->where('product_id', $productId)->orderBy('id', 'desc')->value('product_serial');

        // If there's a last serial number, extract the numeric part
        $lastSerialNumberNumericPart = ($lastSerialNumber) ? intval(substr($lastSerialNumber, strrpos($lastSerialNumber, '-') + 1)) : 0;

        // Generate unique product serial numbers
        $serialNumbers = $this->generateSerialNumbers($productId, $productcode, $beginNumber, $endNumber, $lastSerialNumberNumericPart);

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

    private function generateSerialNumbers($productId, $productcode, $beginNumber, $endNumber, $lastSerialNumberNumericPart) {
        $serialNumbers = [];

        for ($i = $beginNumber; $i <= $endNumber; $i++) {
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
