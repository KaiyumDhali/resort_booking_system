<?php
namespace App\Http\Controllers;
use App\Models\ProductFeedback;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;

class ProductFeedbackController extends Controller
{
    // productComplainListSearch
    public function productFeedbackListSearch($category_id, $subcategory_id, $product_id, $pdf)
    {
        $query = "
            SELECT
                p.category_id,
                p.sub_category_id,
                c.category_name,
                sc.sub_category_name,
                p.product_name,
                pf.id,
                pf.product_id,
                pf.product_serial,
                pf.customer_email,
                pf.customer_phone,
                pf.feedback_date,
                pf.feedback
            FROM
                product_feedback pf
                JOIN products as p ON p.id = pf.product_id
                JOIN categories as c ON c.id = p.category_id
                JOIN sub_categories as sc ON sc.id = p.sub_category_id
            WHERE 
                (p.category_id = $category_id OR $category_id='0')
                AND (p.sub_category_id = $subcategory_id OR $subcategory_id='0')
                AND (p.id = $product_id OR $product_id='0')
        ";
        $productFeedbackLists = DB::table(DB::raw("($query) AS subquery"))
            ->select('*')
            ->orderByDesc('id')
            ->get();

        if ($pdf == "list") {
            return response()->json($productFeedbackLists);
        }
        if ($pdf == "pdfurl") {
            $data['category_id'] = $category_id;
            $data['subcategory_id'] = $subcategory_id;
            $data['product_id'] = $product_id;
            $pdf = PDF::loadView('admin.pdf.products_feedback_list_pdf', array('productFeedbackLists' => $productFeedbackLists, 'data' => $data));
            return $pdf->stream(Carbon::now().'-recentstat.pdf');
        }
    }

    public function index()
    {
        $categories = Category::where('status',1)->pluck('category_name', 'id')->all();
        return view('admin.product.feedback_list', compact('categories'));
    }
    
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'product_id' => 'required',
            'product_serial' => 'required',
            'feedback' => 'required',
        ]);
        $product_serial = $request->input('product_serial');
        $productFeedback = new ProductFeedback();
        $productFeedback->product_id = $request->input('product_id');
        $productFeedback->product_serial = $product_serial;
        $productFeedback->customer_email = $request->input('customer_email');
        $productFeedback->customer_phone = $request->input('customer_phone');
        $productFeedback->feedback = $request->input('feedback');
        $productFeedback->feedback_date = Carbon::now()->format('Y-m-d');

        $productFeedback->save();

        // return redirect()->route('product_feedback.index')->with('success', 'Feedback submitted successfully');
        return redirect()->route('findproduct',$product_serial)->with([
            'message' => 'Complain Successfully Placed.',
            'alert-type' => 'success'
        ]);
    }

    public function show(ProductFeedback $productFeedback)
    {
        //
    }

    public function edit(ProductFeedback $productFeedback)
    {
        //
    }

    public function update(Request $request, ProductFeedback $productFeedback)
    {
        //
    }

    public function destroy(ProductFeedback $productFeedback)
    {
        //
    }
}
