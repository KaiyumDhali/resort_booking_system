<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Productmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//use App\Http\Controllers\Admin\STR_PAD_LEFT;

class ProductmodelController extends Controller {

    public function __construct() {
        $this->middleware('permission:view productmodel', ['only' => ['index']]);
        $this->middleware('permission:create productmodel', ['only' => ['create', 'store']]);
        $this->middleware('permission:update productmodel', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete productmodel', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $productmodels = Productmodel::all();
        return view('admin.productmodel.index', compact('productmodels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.productmodel.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'model_name' => 'required|string|max:255',
        ]);
        $productmodel = new Productmodel();
        $productmodel->model_name = $request->model_name;
        $productmodel->model_code = str_pad((Productmodel::max('model_code') + 1), 4, '0', STR_PAD_LEFT);
        $productmodel->status = $request->status;

        $productmodel->save();

        return redirect()->route('productmodels.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Productmodel  $productmodel
     * @return \Illuminate\Http\Response
     */
    public function show(Productmodel $productmodel) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Productmodel  $productmodel
     * @return \Illuminate\Http\Response
     */
    public function edit(Productmodel $productmodel) {
        return view('admin.productmodel.update', compact('productmodel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Productmodel  $productmodel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $request->validate([
            'model_name' => 'required|string|max:255',
        ]);
        $productmodel = Productmodel::find($id);
        $productmodel->model_name = $request->model_name;
        $productmodel->status = $request->status;

        $productmodel->save();

        return redirect()->route('productmodels.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Productmodel  $productmodel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Productmodel $productmodel) {
        //
    }

}
