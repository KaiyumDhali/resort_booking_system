<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller {

    public function __construct() {
        $this->middleware('permission:view brand', ['only' => ['index']]);
        $this->middleware('permission:create brand', ['only' => ['create', 'store']]);
        $this->middleware('permission:update brand', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete brand', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $brands = Brand::all();
        return view('admin.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.brand.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'brand_name' => 'required|string|max:255',
        ]);
        $brand = new Brand();
        $brand->brand_name = $request->brand_name;
        $brand->brand_code = strtoupper($request->brand_code);
        $brand->status = $request->status;
        $brand->save();

        return redirect()->route('brands.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand) {
        return view('admin.brand.update', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $request->validate([
            'brand_name' => 'required|string|max:255',
        ]);
        $brand = Brand::find($id);
        $brand->brand_name = $request->brand_name;
        $brand->brand_code = strtoupper($request->brand_code);
        $brand->status = $request->status;

        $brand->save();

        return redirect()->route('brands.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand) {
        //
    }

}
