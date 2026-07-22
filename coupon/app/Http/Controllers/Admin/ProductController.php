<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Productmodel;
use App\Models\Color;
use App\Models\OtherProduct;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;



class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view product', ['only' => ['index']]);
        $this->middleware('permission:create product', ['only' => ['create', 'store']]);
        $this->middleware('permission:update product', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete product', ['only' => ['destroy']]);
    }


    public function search(Request $request)
    {
        if ($request->ajax()) {
            $output = "";

            $query = Product::query();

            if ($request->has('search')) {
                $query->where('product_name', 'LIKE', '%' . $request->search . "%");
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
                    '<td>' . $product->product_code . '</td>' .
                    '<td>' . $product->product_name . '</td>' .
                    '<td>' . optional($product->category)->category_name . '</td>' .
                    '<td>' . optional($product->subCategory)->sub_category_name . '</td>' .
                    '<td>' . optional($product->brand)->brand_name . '</td>' .
                    '<td>' . optional($product->color)->color_name . '</td>' .
                    '<td>' . optional($product->size)->size_name . '</td>' .
                    '<td>' . optional($product->unit)->unit_name . '</td>' .
                    '<td>' . $product->purchase_price . '</td>' .
                    '<td>' . $product->sales_price . '</td>' .
                    '<td class="table__td"><span>' . ($product->status == 1 ? 'Active' : 'Disabled') . '</span></td>' .
                    '<td class="table__td">' .
                    '<span>' .
                    '<a href="' . route('products.edit', Crypt::encrypt($product->id)) . '" title="Edit">' .
                    '<span class="dropdown-items__link-icon">' .
                    '<svg class="icon-icon-task-notes">' .
                    '<use xlink:href="#icon-task-notes"></use>' .
                    '</svg>' .
                    '</span>' .
                    '</a>' .
                    '</span>' .
                    '<span>' .
                    '<a href="' . route('products.show', Crypt::encrypt($product->id)) . '" title="View">' .
                    '<span class="dropdown-items__link-icon">' .
                    '<svg class="icon-icon-view">' .
                    '<use xlink:href="#icon-view"></use>' .
                    '</svg>' .
                    '</span>' .
                    '</a>' .
                    '</span>' .
                    '<span>' .
                    '<a data-productPrint="' . json_encode($product) . '" ' .
                    'onclick="openEditModalPromotion(this)" data-bs-toggle="tooltip" ' .
                    'data-bs-placement="top" title="Print">' .
                    '<span class="dropdown-items__link-icon">' .
                    '<svg class="icon-icon-text-left">' .
                    '<use xlink:href="#icon-text-left"></use>' .
                    '</svg>' .
                    '</span>' .
                    '</a>' .
                    '</span>' .
                    '</td>' .
                    '</tr>';
            }


            return $output;
        }
    }

    public function index()
    {
        $products = Product::all();
        return view('admin.product.index', compact('products'));
    }


    public function otherProductList()
    {
        $products = OtherProduct::all();
        return view('admin.product.other_product_list', compact('products'));
    }

    public function create()
    {

        $allCategories = Category::where('status', 1)->pluck('category_name', 'id')->all();
        $allSubCategories = SubCategory::where('status', 1)->pluck('sub_category_name', 'id')->all();

        //        $allBrands = Brand::where('status', 1)->pluck('brand_name', 'id')->all();

        $allBrands = Brand::where('status', 1)->select('id', 'brand_name', 'brand_code')->get()->toArray();

        //        $allProductmodel = Productmodel::where('status', 1)->pluck('model_name', 'id')->all();

        $allProductmodel = Productmodel::where('status', 1)->select('id', 'model_code', 'model_name')->get()->toArray();

        //        dd($allProductmodel);

        $allColors = Color::where('status', 1)->pluck('color_name', 'id')->all();
        $allSizes = Size::where('status', 1)->pluck('size_name', 'id')->all();
        $allUnits = Unit::where('status', 1)->pluck('unit_name', 'id')->all();

        return view('admin.product.add', compact('allCategories', 'allSubCategories', 'allBrands', 'allProductmodel', 'allColors', 'allSizes', 'allUnits'));
    }


    public function otherProductAdd()
    {

        $allCategories = Category::where('status', 1)->pluck('category_name', 'id')->all();
        $allSubCategories = SubCategory::where('status', 1)->pluck('sub_category_name', 'id')->all();

        //        $allBrands = Brand::where('status', 1)->pluck('brand_name', 'id')->all();

        $allBrands = Brand::where('status', 1)->select('id', 'brand_name', 'brand_code')->get()->toArray();

        //        $allProductmodel = Productmodel::where('status', 1)->pluck('model_name', 'id')->all();

        $allProductmodel = Productmodel::where('status', 1)->select('id', 'model_code', 'model_name')->get()->toArray();

        //        dd($allProductmodel);

        $allColors = Color::where('status', 1)->pluck('color_name', 'id')->all();
        $allSizes = Size::where('status', 1)->pluck('size_name', 'id')->all();
        $allUnits = Unit::where('status', 1)->pluck('unit_name', 'id')->all();

        return view('admin.product.other_product_add', compact('allCategories', 'allSubCategories', 'allBrands', 'allProductmodel', 'allColors', 'allSizes', 'allUnits'));
    }



    public function otherProductStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => [
                'required',
                Rule::unique('other_products')->ignore($request->id),
            ],
        ]);

        $otherProduct = new OtherProduct();

        $otherProduct->product_code = $request->product_code;
        $otherProduct->product_name = $request->product_name;
        $otherProduct->status = $request->status;
        $otherProduct->save();

        return redirect()->route('other_product_list')->with([
            'message' => 'add product successfully!',
            'alert-type' => 'success'
        ]);
    }

    public function store(Request $request)
    {

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
        $product->productmodel_id = $request->productmodel_id;
        $product->size_id = $request->size_id;
        $product->unit_id = $request->unit_id;
        $product->purchase_price = $request->purchase_price;
        $product->sales_price = $request->sales_price;
        $product->product_description = $request->product_description;
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
                $image_path = $image->storeAs('public/images/products/', $name);
                $productImage = new ProductDetail();
                $productImage->product_id = $product->id;
                $productImage->image_path = 'storage/images/products/' . $name;
                $productImage->save();
            }
        }

        //        dd($product);


        return redirect()->route('products.index')->with([
            'message' => 'successfully create !',
            'alert-type' => 'success'
        ]);
    }



    public function otherProductView($id)
    {

        // $allUnits = Unit::pluck('unit_name', 'id')->all();

        // $productDetails = ProductDetail::select('product_details.id', 'product_details.image_path')->where('product_id', Crypt::decrypt($id))->get();

        $other_product = OtherProduct::select('other_products.*')
            // ->with(['productimage:product_id,image_path'])
            ->where('other_products.id', Crypt::decrypt($id))
            ->first();

        return view('admin.product.other_product_view', compact('other_product'));
    }

    public function show($id)
    {

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

    public function barcodePrint(Request $request, $productID)
    {


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
        //        $pdf = PDF::loadView('admin.product.print', compact('product', 'product_qty'));
        //        $pdf = PDF::loadView('admin.product.print', array('product' => $product, 'product_qty' => $product_qty));
        //        return $pdf->stream(Carbon::now() . '-recentstat.pdf');
    }


    public function otherProductEdit($id)
    {

        $other_product = OtherProduct::select('other_products.*')->where('id', Crypt::decrypt($id))->first();

        return view('admin.product.other_product_update', compact('other_product'));
    }


    public function edit($id)
    {

        $allCategories = Category::pluck('category_name', 'id')->all();
        $allSubCategories = SubCategory::pluck('sub_category_name', 'id')->all();
        //        $allBrands = Brand::pluck('brand_name', 'id')->all();

        $allBrands = Brand::where('status', 1)->select('id', 'brand_name', 'brand_code')->get()->toArray();

        //        $allProductmodel = Productmodel::where('status', 1)->pluck('model_name', 'id')->all();
        $allProductmodel = Productmodel::where('status', 1)->select('id', 'model_code', 'model_name')->get()->toArray();
        //                dd($allProductmodel);


        $allColors = Color::pluck('color_name', 'id')->all();
        $allSizes = Size::pluck('size_name', 'id')->all();
        $allUnits = Unit::pluck('unit_name', 'id')->all();

        $product = Product::select('products.*')->where('id', Crypt::decrypt($id))->first();
        $productDetails = ProductDetail::select('product_details.id', 'product_details.image_path')->where('product_id', Crypt::decrypt($id))->get();
        //        dd($productDetails);

        return view('admin.product.update', compact('productDetails', 'product', 'allCategories', 'allSubCategories', 'allBrands', 'allProductmodel', 'allColors', 'allSizes', 'allUnits'));
    }

    // imageDestroy
    public function imageDestroy($id)
    {
        //        $productImage = ProductDetail::find($id);
        //        dd($productImage->image_path);
        //        if ($productImage->image_path != Null) {
        //            Storage::delete('public' . $productImage->image_path);
        //            Storage::disk('public')->delete($productImage->image_path);
        //            $productImage->delete();
        //        }

        $productImage = ProductDetail::find($id);
        //        dd($productImage);
        if (file_exists(public_path($productImage->image_path))) {
            unlink(public_path($productImage->image_path));
            $productImage->delete();
            /*
              Delete Multiple File like this way
              Storage::delete(['upload/test.png', 'upload/test2.png']);
             */
        } else {
            //            dd('File does not exists.');
        }

        return redirect()->route('products.edit', Crypt::encrypt($productImage->product_id))->with([
            'message' => 'Image successfully deleted. !',
            'alert-type' => 'danger'
        ]);

        //        $productImage = ProductDetail::find(Crypt::decrypt($id));
        //        if ($productImage->image_path !== null) {
        //            $filePath = storage_path($productImage->image_path);
        //            try {
        //                if (file_exists($filePath)) {
        //                    unlink($filePath);
        //                }
        //            } catch (\Exception $e) {
        //                Log::error('Error deleting file using unlink: ' . $e->getMessage());
        //            }
        //        }
        //        $productImage->delete();
        //        return redirect()->route('products.edit', Crypt::encrypt($productImage->product_id))->with([
        //                    'message' => 'Image and record successfully deleted!',
        //                    'alert-type' => 'danger'
        //        ]);
    }



    public function otherProductUpdate(Request $request, $id)
    {

        // dd($request->all());

        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
        ]);

        $other_product = OtherProduct::find($id);
        $other_product->product_code = $request->product_code;
        $other_product->product_name = $request->product_name;
        $other_product->status = $request->status;

        $other_product->save();

        return redirect()->route('other_product_list')->with([
            'message' => 'successfully update !',
            'alert-type' => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
        ]);

        $product = Product::find($id);
        $product->product_code = $request->product_code;
        $product->product_name = $request->product_name;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id = $request->brand_id;
        $product->color_id = $request->color_id;
        $product->productmodel_id = $request->productmodel_id;
        $product->size_id = $request->size_id;
        $product->unit_id = $request->unit_id;
        $product->purchase_price = $request->purchase_price;
        $product->sales_price = $request->sales_price;
        $product->product_description = $request->product_description;
        $product->status = $request->status;

        //        dd($product);

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
                $productImage->image_path = 'storage/images/products/' . $name;
                $productImage->save();
            }
        }

        return redirect()->route('products.index')->with([
            'message' => 'successfully update !',
            'alert-type' => 'success'
        ]);
    }



    public function destroy(Product $product)
    {
        //
    }
}
