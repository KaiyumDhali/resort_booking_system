<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;


class ProductController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $products = Product::all();
        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $allCategories = Category::pluck('category_name', 'id')->all();
        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
        $allBrands = Brand::pluck('brand_name', 'id')->all();
        $allColors = Color::pluck('color_name', 'id')->all();
        $allSizes = Size::pluck('size_name', 'id')->all();
        $allUnits = Unit::pluck('unit_name', 'id')->all();

        return view('admin.product.add', compact('allCategories', 'allSubCategories', 'allBrands', 'allColors', 'allSizes', 'allUnits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

//        $request->validate([
//            'product_name' => 'required|string|max:255',
//            'product_code' => 'required|unique:products',
//        ]);

        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => [
                'required',
                Rule::unique('products')->ignore($request->id),
            ],
        ]);

        $product = new Product();

        $product->product_code = $request->product_code;
        $product->product_name = $request->product_name;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id = $request->brand_id;
        $product->color_id = $request->color_id;
        $product->size_id = $request->size_id;
        $product->unit_id = $request->unit_id;
        $product->purchase_price = $request->purchase_price;
        $product->sales_price = $request->sales_price;
        $product->status = $request->status;
        $product->save();

        if ($request->hasfile('image')) {
            $request->validate([
                'image' => 'required',
                'image.*' => 'mimes:jpg,png,jpeg,gif,svg|max:5120'
                    //'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            foreach ($request->file('image') as $image) {
                $name = date('d-m-Y-H-i-s') . '-' . $image->getClientOriginalName();
                $image_path = $image->storeAs('public/images/products', $name);

                $productImage = new ProductDetail();
                $productImage->product_id = $product->id;
                $productImage->image_path = $image_path;
                $productImage->save();
            }
        }

//        dd($product);


        return redirect()->route('products.index')->with([
                    'message' => 'successfully create !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

//        $allCategories = Category::pluck('category_name', 'id')->all();
//        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
//        $allBrands = Brand::pluck('brand_name', 'id')->all();
//        $allColors = Color::pluck('color_name', 'id')->all();
//        $allSizes = Size::pluck('size_name', 'id')->all();
//        $allUnits = Unit::pluck('unit_name', 'id')->all();

        $productDetails = ProductDetail::select('product_details.id', 'product_details.image_path')->where('product_id', Crypt::decrypt($id))->get();
//        $product = Product::find($product->id);

        $product = Product::select('products.*')
                ->with(['productimage:product_id,image_path'])
                ->where('products.id', Crypt::decrypt($id))
                ->first();

// Access product details using $product->image_path
//        dd($product);

        return view('admin.product.view', compact('productDetails', 'product'));
    }

    public function barcodePrint(Request $request, $productID) {
        //$productID = Product::where('')
//        $allCategories = Category::pluck('category_name', 'id')->all();
//        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
//        $allBrands = Brand::pluck('brand_name', 'id')->all();
//        $allColors = Color::pluck('color_name', 'id')->all();
//        $allSizes = Size::pluck('size_name', 'id')->all();
//        $allUnits = Unit::pluck('unit_name', 'id')->all();
//        $productDetails = ProductDetail::select('product_details.id', 'product_details.image_path')->where('product_id', $product->id)->get();

        $product_qty = $request->quantity;
        $product = Product::find($productID);
//        dd($product1);

        return view('admin.product.print', compact('product', 'product_qty'));
//        return view('admin.product.view');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

//        dd($product);

        $allCategories = Category::pluck('category_name', 'id')->all();
        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
        $allBrands = Brand::pluck('brand_name', 'id')->all();
        $allColors = Color::pluck('color_name', 'id')->all();
        $allSizes = Size::pluck('size_name', 'id')->all();
        $allUnits = Unit::pluck('unit_name', 'id')->all();

        $product = Product::select('products.*')->where('id', Crypt::decrypt($id))->first();
        $productDetails = ProductDetail::select('product_details.id', 'product_details.image_path')->where('product_id', Crypt::decrypt($id))->get();
//        dd($productDetails);

        return view('admin.product.update', compact('productDetails', 'product', 'allCategories', 'allSubCategories', 'allBrands', 'allColors', 'allSizes', 'allUnits'));
    }

    // imageDestroy
    public function imageDestroy($id) {
        $productImage = ProductDetail::find(Crypt::decrypt($id));
        if ($productImage->image_path != Null) {
            Storage::delete($productImage->image_path);
            $productImage->delete();
        }
        return redirect()->route('products.edit', Crypt::encrypt($productImage->product_id))->with([
                    'message' => 'Image successfully deleted. !',
                    'alert-type' => 'danger'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
//        $request->validate([
//            'product_name' => 'required|string|max:255',
//            'product_code' => 'required|unique:products',
//        ]);

        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
        ]);

//        $request->validate([
//            'product_name' => 'required|string|max:255',
//            'product_code' => 'required|unique:products|unique:products,The product code has already been taken.',
//                ]);

        $product = Product::find($id);
//        $product->product_code = $request->product_code;
        $product->product_code = $request->product_code;
        $product->product_name = $request->product_name;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id = $request->brand_id;
        $product->color_id = $request->color_id;
        $product->size_id = $request->size_id;
        $product->unit_id = $request->unit_id;
        $product->purchase_price = $request->purchase_price;
        $product->sales_price = $request->sales_price;
        $product->status = $request->status;
        $product->save();

        if ($request->hasfile('image')) {
            $request->validate([
                'image' => 'required',
                'image.*' => 'mimes:jpg,png,jpeg,gif,svg|max:5120'
                    //'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            foreach ($request->file('image') as $image) {
                $name = date('d-m-Y-H-i-s') . '-' . $image->getClientOriginalName();
                $image_path = $image->storeAs('public/images/products', $name);

                $productImage = new ProductDetail();
                $productImage->product_id = $product->id;
                $productImage->image_path = $image_path;
                $productImage->save();
            }
        }

        return redirect()->route('products.index')->with([
                    'message' => 'successfully update !',
                    'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) {
        //
    }

}
