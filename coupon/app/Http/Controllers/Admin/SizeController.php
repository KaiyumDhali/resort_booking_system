<?php

namespace App\Http\Controllers\Admin;

use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SizeController extends Controller {

    public function __construct() {
        $this->middleware('permission:view size', ['only' => ['index']]);
        $this->middleware('permission:create size', ['only' => ['create', 'store']]);
        $this->middleware('permission:update size', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete size', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $sizes = Size::all();
        return view('admin.size.index', compact('sizes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.size.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'size_name' => 'required|string|max:255',
        ]);
        $size = new Size();
        $size->size_name = $request->size_name;
        $size->status = $request->status;
        $size->save();
        return redirect()->route('sizes.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function show(Size $size) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function edit(Size $size) {
        return view('admin.size.update', compact('size'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate([
            'size_name' => 'required|string|max:255',
        ]);
        $size = Size::find($id);
        $size->size_name = $request->size_name;
        $size->status = $request->status;
        $size->save();

        return redirect()->route('sizes.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function destroy(Size $size) {
        //
    }

}
