<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller {

    public function __construct() {
        $this->middleware('permission:view unit', ['only' => ['index']]);
        $this->middleware('permission:create unit', ['only' => ['create', 'store']]);
        $this->middleware('permission:update unit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete unit', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $units = Unit::all();
        return view('admin.unit.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.unit.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);
        $unit = new Unit();
        $unit->unit_name = $request->unit_name;
        $unit->unit_value = $request->unit_value;
        $unit->status = $request->status;
        $unit->save();

        return redirect()->route('units.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit) {

        return view('admin.unit.update', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);
        $unit = Unit::find($id);
        $unit->unit_name = $request->unit_name;
        $unit->unit_value = $request->unit_value;
        $unit->status = $request->status;
        $unit->save();

        return redirect()->route('units.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit) {
        //
    }

}
