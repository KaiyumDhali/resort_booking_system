<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ColorController extends Controller {

    public function __construct() {
        $this->middleware('permission:view color', ['only' => ['index']]);
        $this->middleware('permission:create color', ['only' => ['create', 'store']]);
        $this->middleware('permission:update color', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete color', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $colors = Color::all();
        return view('admin.color.index', compact('colors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.color.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'color_name' => 'required|string|max:255',
        ]);
        $color = new Color();
        $color->color_name = $request->color_name;
        $color->status = $request->status;
        $color->save();
        return redirect()->route('colors.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function show(Color $color) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function edit(Color $color) {
        return view('admin.color.update', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate([
            'color_name' => 'required|string|max:255',
        ]);
        $color = Color::find($id);
        $color->color_name = $request->color_name;
        $color->status = $request->status;
        $color->save();

        return redirect()->route('colors.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function destroy(Color $color) {
        //
    }

}
