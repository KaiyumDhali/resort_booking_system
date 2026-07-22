<?php
namespace App\Http\Controllers;
use App\Models\ProductComplain;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;


class ProductComplainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productComplains = ProductComplain::all();
        return view('front.productcomplainlist', compact('productComplains'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
            'product_reg_id' => 'required',
            'product_serial' => 'required',
            'complain' => 'required|string|max:255',
        ]);

        $product_serial = $request->product_serial;
        $productComplain = new ProductComplain();

        $productComplain->product_id = $request->product_id;
        $productComplain->product_reg_id = $request->product_reg_id;
        $productComplain->product_serial = $product_serial;
        $productComplain->complain = $request->complain;
        $productComplain->complain_date =  Carbon::now()->format('Y-m-d');

        if ($request->hasfile('image_path')) {
            $request->validate([
                'image_path' => 'required',
                'image_path.*' => 'mimes:jpg,png,jpeg,gif,svg|max:5120'
            ]);
            $name = date('d-m-Y-H-i-s') . '-' . $request->file('image_path')->getClientOriginalName();
            $image_path = $request->file('image_path')->storeAs('public/images/productscomplains', $name);
            $productComplain->image_path = 'storage/images/productscomplains/' . $name;  
        }
        //dd($productComplain);
        
//        ProductComplain::create($request->all());
        
        $productComplain->save();
        return redirect()->route('findproduct',$product_serial)->with([
            'message' => 'Complain Successfully Placed.',
            'alert-type' => 'success'
        ]);
    }

    // ----------------------- MD MAsum Work -----------------------------
   
     // productComplainList 
     public function productComplainList()
     {
         $categories = Category::where('status',1)->pluck('category_name', 'id')->all();
         return view('admin.product.complain_list', compact('categories'));
     }
    // productComplainListSearch
    public function productComplainListSearch($category_id, $subcategory_id, $product_id, $status, $pdf)
    {
        $query = "
            SELECT
                p.category_id,
                p.sub_category_id,
                c.category_name,
                sc.sub_category_name,
                p.product_name,
                pc.id,
                pc.product_id,
                pc.product_serial,
                pc.complain_date,
                pc.complain,
                pc.image_path,
                pc.status,
                pr.name,
                pr.mobile,
                pr.memo_no,
                pr.customer_address,
                pr.shop_address,
                pr.created_at as registrations_date
            FROM
                product_complains pc
                JOIN products as p ON p.id = pc.product_id
                JOIN categories as c ON c.id = p.category_id
                JOIN sub_categories as sc ON sc.id = p.sub_category_id
                JOIN product_registrations as pr ON pr.id = pc.product_reg_id
            WHERE 
                pc.status = $status
                AND (p.category_id = $category_id OR $category_id='0')
                AND (p.sub_category_id = $subcategory_id OR $subcategory_id='0')
                AND (p.id = $product_id OR $product_id='0')
        ";

        $productComplainLists = DB::table(DB::raw("($query) AS subquery"))
            ->select('*')
            // ->select('category_name','sub_category_name','product_name','product_serial', 'complain_date', 'complain')
            ->orderByDesc('id')
            ->get();

        if ($pdf == "list") {
            return response()->json($productComplainLists);
        }
        if ($pdf == "pdfurl") {
            $data['category_id'] = $category_id;
            $data['subcategory_id'] = $subcategory_id;
            $data['product_id'] = $product_id;
            $pdf = PDF::loadView('admin.pdf.products_complain_list_pdf', array('productComplainLists' => $productComplainLists, 'data' => $data));
            return $pdf->stream(Carbon::now().'-recentstat.pdf');
        }
    }
    // complaintSolve
    public function complaintSolve(Request $request, $id)
    {
        try {
            $complaint = ProductComplain::findOrFail($id);
            $complaint->status = $request->status;
            $complaint->save();
            return response()->json(['message' => 'Complaint Solve successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while Solving the complaint'], 500);
        }
    }

    // productComplainList 
    public function productComplainSolveList()
    {
        $categories = Category::where('status',1)->pluck('category_name', 'id')->all();
        return view('admin.product.complain_solve_list', compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductComplain $productComplain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductComplain $productComplain)
    {
        //
    }
}
