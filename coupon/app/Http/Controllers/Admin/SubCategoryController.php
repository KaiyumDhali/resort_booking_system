<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubCategoryController extends Controller {

    public function __construct() {
        $this->middleware('permission:view subcategory', ['only' => ['index']]);
        $this->middleware('permission:create subcategory', ['only' => ['create', 'store']]);
        $this->middleware('permission:update subcategory', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete subcategory', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
//        $subCategorys = SubCategory::all();
        $subCategorys = SubCategory::with('category')->get();

//        dd($subCategorys);

        return view('admin.subcategory.index', compact('subCategorys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $allCategories = Category::pluck('category_name', 'id')->all();
        return view('admin.subcategory.add', compact('allCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'sub_category_name' => 'required|string|max:255',
        ]);
        $subCategory = new SubCategory();

        $subCategory->category_id = $request->category_id;
        $subCategory->sub_category_name = $request->sub_category_name;
        $subCategory->sub_category_code = $request->sub_category_code;
        $subCategory->status = $request->status;

        // dd($subCategory);

        $subCategory->save();
        return redirect()->route('sub_categorys.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $subCategory) {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $subCategory) {
        $allCategories = Category::pluck('category_name', 'id')->all();
        return view('admin.subcategory.update', compact('subCategory', 'allCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate([
            'sub_category_name' => 'required|string|max:255',
        ]);
        $subCategory = SubCategory::find($id);
        $subCategory->category_id = $request->category_id;
        $subCategory->sub_category_name = $request->sub_category_name;
        $subCategory->sub_category_code = $request->sub_category_code;
        $subCategory->status = $request->status;
        $subCategory->save();

        return redirect()->route('sub_categorys.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subCategory) {
        //
    }

}
