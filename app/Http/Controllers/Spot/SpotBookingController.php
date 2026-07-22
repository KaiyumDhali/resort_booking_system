<?php

namespace App\Http\Controllers\Spot;


use App\Http\Controllers\Controller;
use App\Models\SpotBooking;
use Illuminate\Http\Request;
use App\Models\SpotPackage;
use App\Models\Spot;
use App\Models\AdditionalService;
use App\Models\CommonFacility;
use App\Models\SpotDetail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Gallery;
use App\Models\Booking;
use App\Models\TermsCondition;
use App\Models\CustomerType;
use App\Models\FinanceGroup;
use App\Models\FinanceAccount;
use App\Models\AdditionalServicePrice;
use App\Models\FinanceTransaction;
use App\Models\CompanySetting;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Validator;
use Carbon\Carbon;
use Auth;
use App\Models\ProductService;
use App\Models\ProductServiceDetail;
use Illuminate\Support\Facades\View;
use NumberFormatter;
use Rmunate\Utilities\SpellNumber;
use Illuminate\Pagination\LengthAwarePaginator;




class SpotBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $bookings = SpotBooking::with(['spot', 'package'])->latest()->paginate(20);
    //     return view('pages.spot_booking.index', compact('bookings'));
    // }

public function updateStatus(Request $request)
{
    $request->validate([
        'invoice_number' => 'required|exists:spot_bookings,invoice_number',
        'status' => 'required|in:0,1,2'
    ]);

    // Update all bookings under this invoice
    \DB::table('spot_bookings')
        ->where('invoice_number', $request->invoice_number)
        ->update(['status' => $request->status]);

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);
}


public function index(Request $request)
{
    $invoice   = $request->invoice;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid;
    $dateFrom  = $request->date_from;
    $dateTo    = $request->date_to;
    $status    = $request->status;

    // ✅ অন্য কোনো filter apply হয়েছে কিনা — এটা জানা দরকার "today default" এর
    // সিদ্ধান্ত নেওয়ার জন্য (যেমন booking details report-এ করা হয়েছিল)
    $hasOtherFilters = filled($invoice)
        || filled($mobile)
        || filled($nid)
        || ($status !== null && $status !== '');

    // ===== Spot totals =====
    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            'booking_date',
            DB::raw('SUM(CASE WHEN spot_id IS NOT NULL THEN total_price ELSE 0 END) as spot_total'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent'),
            DB::raw('MAX(discount_percent) as discount_percent'),
            DB::raw('SUM(total_persons) as total_persons'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_mobile) as customer_mobile'),
            DB::raw('MAX(status) as status')
        );

    if ($invoice) {
        $spotTotals->where('invoice_number', 'like', '%'.$invoice.'%');
    }

    if ($mobile) {
        $spotTotals->where('customer_mobile', 'like', '%'.$mobile.'%');
    }

    // ✅ Date filter এখন booking_date না, created_at (booking কবে তৈরি হয়েছে) অনুযায়ী।
    // কিছুই filter করা না থাকলে, page load-এ default হিসেবে আজকে তৈরি হওয়া bookings দেখানো হবে।
    if ($dateFrom && $dateTo) {
        $spotTotals->whereDate('created_at', '>=', $dateFrom)
                   ->whereDate('created_at', '<=', $dateTo);
    } elseif (!$hasOtherFilters && !$dateFrom && !$dateTo) {
        $spotTotals->whereDate('created_at', now()->toDateString());
    }

    if ($status !== null && $status !== '') {
        $spotTotals->where('status', $status);
    }

    // Filter by NID — match against customers table via mobile number
    if ($nid) {
        $mobiles = DB::table('customers')
            ->where('nid_number', 'like', '%'.$nid.'%')
            ->pluck('customer_mobile');

        $spotTotals->whereIn('customer_mobile', $mobiles);
    }

    $spotTotals = $spotTotals->groupBy('invoice_number', 'booking_date');

    // ===== Package totals =====
    $packageTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total')
        )
        ->groupBy('invoice_number');

    // ===== Service totals =====
    $serviceTotals = DB::table('spot_booking_details')
        ->select(
            'invoice_number',
            DB::raw('SUM(total_price) as service_total')
        )
        ->groupBy('invoice_number');

    // ===== Paid totals =====
    $paidTotals = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Room totals =====
    $roomTotals = DB::table('bookings')
        ->select(
            'booking_no as invoice_number',
            DB::raw('SUM(total_amount) as room_total')
        )
        ->groupBy('booking_no');

    // ===== Invoice Amount (Finance Transaction Dr, no payment_type) =====
    $invoiceAmounts = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as invoice_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Final booking list =====
    $bookings = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb', fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($paidTotals, 'ft', fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->leftJoinSub($roomTotals, 'rb', fn($j) => $j->on('sb.invoice_number', '=', 'rb.invoice_number'))
        ->leftJoinSub($invoiceAmounts, 'ia', fn($j) => $j->on('sb.invoice_number', '=', 'ia.invoice_number'))
        ->leftJoin('spot_bookings as d', fn($j) => $j->on('sb.invoice_number', '=', 'd.invoice_number'))
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.total_persons',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.discount_percent',
            'sb.spot_discount_percent',
            DB::raw('sb.spot_total'),
            DB::raw('COALESCE(pb.package_total, 0) as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            DB::raw('COALESCE(rb.room_total, 0) as room_total'),
            DB::raw('(sb.spot_total * sb.spot_discount_percent / 100) as spot_discount_amount'),
            DB::raw('
                (sb.spot_total - (sb.spot_total * sb.spot_discount_percent / 100))
                + COALESCE(pb.package_total, 0)
                + COALESCE(sbd.service_total, 0)
                + COALESCE(rb.room_total, 0)
                as sub_total
            '),
            DB::raw('MAX(d.discount_amount) as manual_discount_amount'),
            DB::raw('COALESCE(ia.invoice_amount, 0) as invoice_amount'),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->groupBy(
            'sb.invoice_number', 'sb.booking_date', 'sb.total_persons',
            'sb.customer_name', 'sb.customer_mobile', 'sb.status',
            'sb.discount_percent', 'sb.spot_discount_percent', 'sb.spot_total',
            'pb.package_total', 'sbd.service_total', 'rb.room_total',
            'ft.paid_amount',
            'ia.invoice_amount'
        )
        ->orderByDesc('sb.invoice_number')
        ->paginate(20)
        ->withQueryString();

    // ===== Get advance return rules =====
    $rules = DB::table('advance_return_rules')->orderBy('max_day', 'asc')->get();
    $maxRuleDay = $rules->max('max_day'); // highest max_day

    $today = Carbon::today();

    $bookings->getCollection()->transform(function ($booking) use ($rules, $maxRuleDay, $today) {
        $bookingDate = Carbon::parse($booking->booking_date);

        // If booking date is in the past, no refund
        if ($bookingDate->lt($today)) {
            $booking->refundable_amount = 0;
            $booking->daysBeforeBooking = 0;
            return $booking;
        }

        // Days before booking
        $daysBeforeBooking = $bookingDate->diffInDays($today);

        // If days_before_booking exceeds max rule, refund = 0
        if ($daysBeforeBooking > $maxRuleDay) {
            $booking->refundable_amount = 0;
        } else {
            $rule = $rules->firstWhere('max_day', '>=', $daysBeforeBooking);
            $refundPercent = $rule ? $rule->refund_percent : 0;
            $booking->refundable_amount = $booking->paid_amount * $refundPercent / 100;
        }

        $booking->daysBeforeBooking = $daysBeforeBooking;
        return $booking;
    });

    return view('pages.spot_booking.index', compact('bookings'));
}

public function spotBookingsReportPdf(Request $request)
{
    $invoice   = $request->invoice;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid;
    $dateFrom  = $request->date_from;
    $dateTo    = $request->date_to;
    $status    = $request->status;
 
    $hasOtherFilters = filled($invoice)
        || filled($mobile)
        || filled($nid)
        || ($status !== null && $status !== '');
 
    // ===== Spot totals =====
    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            'booking_date',
            DB::raw('SUM(CASE WHEN spot_id IS NOT NULL THEN total_price ELSE 0 END) as spot_total'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent'),
            DB::raw('MAX(discount_percent) as discount_percent'),
            DB::raw('SUM(total_persons) as total_persons'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_mobile) as customer_mobile'),
            DB::raw('MAX(status) as status')
        );
 
    if ($invoice) {
        $spotTotals->where('invoice_number', 'like', '%'.$invoice.'%');
    }
 
    if ($mobile) {
        $spotTotals->where('customer_mobile', 'like', '%'.$mobile.'%');
    }
 
    // ✅ created_at ভিত্তিক date filter, আর কিছু filter না থাকলে আজকের ডিফল্ট — index() এর সাথে সামঞ্জস্যপূর্ণ
    if ($dateFrom && $dateTo) {
        $spotTotals->whereDate('created_at', '>=', $dateFrom)
                   ->whereDate('created_at', '<=', $dateTo);
    } elseif (!$hasOtherFilters && !$dateFrom && !$dateTo) {
        $spotTotals->whereDate('created_at', now()->toDateString());
    }
 
    if ($status !== null && $status !== '') {
        $spotTotals->where('status', $status);
    }
 
    if ($nid) {
        $mobiles = DB::table('customers')
            ->where('nid_number', 'like', '%'.$nid.'%')
            ->pluck('customer_mobile');
 
        $spotTotals->whereIn('customer_mobile', $mobiles);
    }
 
    $spotTotals = $spotTotals->groupBy('invoice_number', 'booking_date');
 
    // ===== Package totals =====
    $packageTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total')
        )
        ->groupBy('invoice_number');
 
    // ===== Service totals =====
    $serviceTotals = DB::table('spot_booking_details')
        ->select(
            'invoice_number',
            DB::raw('SUM(total_price) as service_total')
        )
        ->groupBy('invoice_number');
 
    // ===== Paid totals =====
    $paidTotals = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->groupBy('invoice_no');
 
    // ===== Room totals =====
    $roomTotals = DB::table('bookings')
        ->select(
            'booking_no as invoice_number',
            DB::raw('SUM(total_amount) as room_total')
        )
        ->groupBy('booking_no');
 
    // ===== Invoice Amount (Finance Transaction Dr, no payment_type) =====
    $invoiceAmounts = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as invoice_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNull('payment_type')
        ->groupBy('invoice_no');
 
    // ===== Final booking list — NO pagination, সব row দরকার PDF এর জন্য =====
    $bookings = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb', fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($paidTotals, 'ft', fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->leftJoinSub($roomTotals, 'rb', fn($j) => $j->on('sb.invoice_number', '=', 'rb.invoice_number'))
        ->leftJoinSub($invoiceAmounts, 'ia', fn($j) => $j->on('sb.invoice_number', '=', 'ia.invoice_number'))
        ->leftJoin('spot_bookings as d', fn($j) => $j->on('sb.invoice_number', '=', 'd.invoice_number'))
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.total_persons',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.discount_percent',
            'sb.spot_discount_percent',
            DB::raw('sb.spot_total'),
            DB::raw('COALESCE(pb.package_total, 0) as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            DB::raw('COALESCE(rb.room_total, 0) as room_total'),
            DB::raw('(sb.spot_total * sb.spot_discount_percent / 100) as spot_discount_amount'),
            DB::raw('
                (sb.spot_total - (sb.spot_total * sb.spot_discount_percent / 100))
                + COALESCE(pb.package_total, 0)
                + COALESCE(sbd.service_total, 0)
                + COALESCE(rb.room_total, 0)
                as sub_total
            '),
            DB::raw('MAX(d.discount_amount) as manual_discount_amount'),
            DB::raw('COALESCE(ia.invoice_amount, 0) as invoice_amount'),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->groupBy(
            'sb.invoice_number', 'sb.booking_date', 'sb.total_persons',
            'sb.customer_name', 'sb.customer_mobile', 'sb.status',
            'sb.discount_percent', 'sb.spot_discount_percent', 'sb.spot_total',
            'pb.package_total', 'sbd.service_total', 'rb.room_total',
            'ft.paid_amount',
            'ia.invoice_amount'
        )
        ->orderByDesc('sb.invoice_number')
        ->get();
 
    // ===== Refundable amount per booking (same rule engine as index()) =====
    $rules = DB::table('advance_return_rules')->orderBy('max_day', 'asc')->get();
    $maxRuleDay = $rules->max('max_day');
    $today = Carbon::today();
 
    $bookings = $bookings->map(function ($booking) use ($rules, $maxRuleDay, $today) {
        $bookingDate = Carbon::parse($booking->booking_date);
 
        if ($bookingDate->lt($today)) {
            $booking->refundable_amount = 0;
            return $booking;
        }
 
        $daysBeforeBooking = $bookingDate->diffInDays($today);
 
        if ($daysBeforeBooking > $maxRuleDay) {
            $booking->refundable_amount = 0;
        } else {
            $rule = $rules->firstWhere('max_day', '>=', $daysBeforeBooking);
            $refundPercent = $rule ? $rule->refund_percent : 0;
            $booking->refundable_amount = $booking->paid_amount * $refundPercent / 100;
        }
 
        return $booking;
    });
 
    // ===== Summary =====
    $summary = [
        'total_bookings'    => $bookings->count(),
        'total_amount'      => $bookings->sum(fn($b) => $b->invoice_amount + $b->manual_discount_amount),
        'total_discount'    => $bookings->sum('manual_discount_amount'),
        'net_total'         => $bookings->sum('invoice_amount'),
        'total_paid'        => $bookings->sum('paid_amount'),
        'total_refundable'  => $bookings->sum('refundable_amount'),
    ];
 
    $customers = DB::table('customers')->orderBy('customer_name')->select('id', 'customer_name')->get();
 
    $startDate = $dateFrom ?: (!$hasOtherFilters ? now()->toDateString() : null);
    $endDate   = $dateTo   ?: (!$hasOtherFilters ? now()->toDateString() : null);
 
    $setting = \App\Models\CompanySetting::first();
    $data = [
        'company_name'     => $setting->company_name     ?? config('app.name'),
        'company_logo_one' => $setting->company_logo_one ?? '',
        'start_date'       => $startDate ? Carbon::parse($startDate)->format('d M Y') : '—',
        'end_date'         => $endDate   ? Carbon::parse($endDate)->format('d M Y')   : '—',
    ];
 
    $pdf = Pdf::loadView('pages.spot_booking.index-spot_booking_report_pdf', compact('bookings', 'summary', 'data'))
        ->setPaper('a4', 'potrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'dpi'                  => 120,
        ]);
 
    return $pdf->stream('spot-bookings-report-' . now()->format('Ymd-His') . '.pdf');
}
public function spotBookingDetailsReport(Request $request)
{
    $invoice   = $request->invoice;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid;
    $dateFrom  = $request->date_from;
    $dateTo    = $request->date_to;
    $status    = $request->status;

    $hasOtherFilters = filled($invoice)
        || filled($mobile)
        || filled($nid)
        || ($status !== null && $status !== '');

    // ===== Spot totals (+ created_at, প্রতিটা invoice কবে তৈরি হয়েছে সেটা ধরার জন্য) =====
    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            'booking_date',
            DB::raw('MAX(created_at) as created_at'),
            DB::raw('SUM(CASE WHEN spot_id IS NOT NULL THEN total_price ELSE 0 END) as spot_total'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent'),
            DB::raw('MAX(discount_percent) as discount_percent'),
            DB::raw('SUM(total_persons) as total_persons'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_mobile) as customer_mobile'),
            DB::raw('MAX(status) as status')
        );

    if ($invoice) {
        $spotTotals->where('invoice_number', 'like', '%'.$invoice.'%');
    }

    if ($mobile) {
        $spotTotals->where('customer_mobile', 'like', '%'.$mobile.'%');
    }

    // ✅ created_at ভিত্তিক date filter, কিছু filter না থাকলে আজকের ডিফল্ট — index()/PDF report এর সাথে সামঞ্জস্যপূর্ণ
    if ($dateFrom && $dateTo) {
        $spotTotals->whereDate('created_at', '>=', $dateFrom)
                   ->whereDate('created_at', '<=', $dateTo);
    } elseif (!$hasOtherFilters && !$dateFrom && !$dateTo) {
        $spotTotals->whereDate('created_at', now()->toDateString());
    }

    if ($status !== null && $status !== '') {
        $spotTotals->where('status', $status);
    }

    if ($nid) {
        $mobiles = DB::table('customers')
            ->where('nid_number', 'like', '%'.$nid.'%')
            ->pluck('customer_mobile');

        $spotTotals->whereIn('customer_mobile', $mobiles);
    }

    $spotTotals = $spotTotals->groupBy('invoice_number', 'booking_date');

    // ===== Package totals =====
    $packageTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total')
        )
        ->groupBy('invoice_number');

    // ===== Service totals =====
    $serviceTotals = DB::table('spot_booking_details')
        ->select(
            'invoice_number',
            DB::raw('SUM(total_price) as service_total')
        )
        ->groupBy('invoice_number');

    // ===== Paid totals =====
    $paidTotals = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Room totals =====
    $roomTotals = DB::table('bookings')
        ->select(
            'booking_no as invoice_number',
            DB::raw('SUM(total_amount) as room_total')
        )
        ->groupBy('booking_no');

    // ===== Invoice Amount (Finance Transaction Dr, no payment_type) =====
    $invoiceAmounts = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as invoice_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Final booking list — NO pagination, details report সব row একসাথে দেখাবে =====
    $bookings = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb', fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($paidTotals, 'ft', fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->leftJoinSub($roomTotals, 'rb', fn($j) => $j->on('sb.invoice_number', '=', 'rb.invoice_number'))
        ->leftJoinSub($invoiceAmounts, 'ia', fn($j) => $j->on('sb.invoice_number', '=', 'ia.invoice_number'))
        ->leftJoin('spot_bookings as d', fn($j) => $j->on('sb.invoice_number', '=', 'd.invoice_number'))
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.created_at',
            'sb.total_persons',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.discount_percent',
            'sb.spot_discount_percent',
            DB::raw('sb.spot_total'),
            DB::raw('COALESCE(pb.package_total, 0) as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            DB::raw('COALESCE(rb.room_total, 0) as room_total'),
            DB::raw('(sb.spot_total * sb.spot_discount_percent / 100) as spot_discount_amount'),
            DB::raw('MAX(d.discount_amount) as manual_discount_amount'),
            DB::raw('COALESCE(ia.invoice_amount, 0) as invoice_amount'),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->groupBy(
            'sb.invoice_number', 'sb.booking_date', 'sb.created_at', 'sb.total_persons',
            'sb.customer_name', 'sb.customer_mobile', 'sb.status',
            'sb.discount_percent', 'sb.spot_discount_percent', 'sb.spot_total',
            'pb.package_total', 'sbd.service_total', 'rb.room_total',
            'ft.paid_amount',
            'ia.invoice_amount'
        )
        ->orderByDesc('sb.invoice_number')
        ->get();

    // ===== Refundable amount per booking (same rule engine as index()) =====
    $rules      = DB::table('advance_return_rules')->orderBy('max_day', 'asc')->get();
    $maxRuleDay = $rules->max('max_day');
    $today      = Carbon::today();

    $bookings = $bookings->map(function ($booking) use ($rules, $maxRuleDay, $today) {
        $bookingDate = Carbon::parse($booking->booking_date);

        if ($bookingDate->lt($today)) {
            $booking->refundable_amount = 0;
        } else {
            $daysBeforeBooking = $bookingDate->diffInDays($today);
            if ($daysBeforeBooking > $maxRuleDay) {
                $booking->refundable_amount = 0;
            } else {
                $rule = $rules->firstWhere('max_day', '>=', $daysBeforeBooking);
                $refundPercent = $rule ? $rule->refund_percent : 0;
                $booking->refundable_amount = $booking->paid_amount * $refundPercent / 100;
            }
        }

        return $booking;
    });

    // ===== Summary =====
    $summary = [
        'total_bookings'   => $bookings->count(),
        'total_amount'     => $bookings->sum(fn($b) => $b->invoice_amount + $b->manual_discount_amount),
        'total_discount'   => $bookings->sum('manual_discount_amount'),
        'net_total'        => $bookings->sum('invoice_amount'),
        'total_paid'       => $bookings->sum('paid_amount'),
        'total_due'        => max(0, $bookings->sum('invoice_amount') - $bookings->sum('paid_amount')),
        'total_refundable' => $bookings->sum('refundable_amount'),
    ];

    return view('pages.spot_booking.details_report', compact('bookings', 'summary'));
}


public function spotBookingDetailsReportPdf(Request $request)
{
    $invoice   = $request->invoice;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid;
    $dateFrom  = $request->date_from;
    $dateTo    = $request->date_to;
    $status    = $request->status;

    $hasOtherFilters = filled($invoice)
        || filled($mobile)
        || filled($nid)
        || ($status !== null && $status !== '');

    // ===== Spot totals (+ created_at) =====
    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            'booking_date',
            DB::raw('MAX(created_at) as created_at'),
            DB::raw('SUM(CASE WHEN spot_id IS NOT NULL THEN total_price ELSE 0 END) as spot_total'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent'),
            DB::raw('MAX(discount_percent) as discount_percent'),
            DB::raw('SUM(total_persons) as total_persons'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_mobile) as customer_mobile'),
            DB::raw('MAX(status) as status')
        );

    if ($invoice) {
        $spotTotals->where('invoice_number', 'like', '%'.$invoice.'%');
    }

    if ($mobile) {
        $spotTotals->where('customer_mobile', 'like', '%'.$mobile.'%');
    }

    if ($dateFrom && $dateTo) {
        $spotTotals->whereDate('created_at', '>=', $dateFrom)
                   ->whereDate('created_at', '<=', $dateTo);
    } elseif (!$hasOtherFilters && !$dateFrom && !$dateTo) {
        $spotTotals->whereDate('created_at', now()->toDateString());
    }

    if ($status !== null && $status !== '') {
        $spotTotals->where('status', $status);
    }

    if ($nid) {
        $mobiles = DB::table('customers')
            ->where('nid_number', 'like', '%'.$nid.'%')
            ->pluck('customer_mobile');

        $spotTotals->whereIn('customer_mobile', $mobiles);
    }

    $spotTotals = $spotTotals->groupBy('invoice_number', 'booking_date');

    // ===== Package totals =====
    $packageTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total')
        )
        ->groupBy('invoice_number');

    // ===== Service totals =====
    $serviceTotals = DB::table('spot_booking_details')
        ->select(
            'invoice_number',
            DB::raw('SUM(total_price) as service_total')
        )
        ->groupBy('invoice_number');

    // ===== Paid totals =====
    $paidTotals = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Room totals =====
    $roomTotals = DB::table('bookings')
        ->select(
            'booking_no as invoice_number',
            DB::raw('SUM(total_amount) as room_total')
        )
        ->groupBy('booking_no');

    // ===== Invoice Amount (Finance Transaction Dr, no payment_type) =====
    $invoiceAmounts = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as invoice_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNull('payment_type')
        ->groupBy('invoice_no');

    // ===== Final booking list — NO pagination =====
    $bookings = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb', fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($paidTotals, 'ft', fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->leftJoinSub($roomTotals, 'rb', fn($j) => $j->on('sb.invoice_number', '=', 'rb.invoice_number'))
        ->leftJoinSub($invoiceAmounts, 'ia', fn($j) => $j->on('sb.invoice_number', '=', 'ia.invoice_number'))
        ->leftJoin('spot_bookings as d', fn($j) => $j->on('sb.invoice_number', '=', 'd.invoice_number'))
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.created_at',
            'sb.total_persons',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.discount_percent',
            'sb.spot_discount_percent',
            DB::raw('sb.spot_total'),
            DB::raw('COALESCE(pb.package_total, 0) as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            DB::raw('COALESCE(rb.room_total, 0) as room_total'),
            DB::raw('(sb.spot_total * sb.spot_discount_percent / 100) as spot_discount_amount'),
            DB::raw('MAX(d.discount_amount) as manual_discount_amount'),
            DB::raw('COALESCE(ia.invoice_amount, 0) as invoice_amount'),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->groupBy(
            'sb.invoice_number', 'sb.booking_date', 'sb.created_at', 'sb.total_persons',
            'sb.customer_name', 'sb.customer_mobile', 'sb.status',
            'sb.discount_percent', 'sb.spot_discount_percent', 'sb.spot_total',
            'pb.package_total', 'sbd.service_total', 'rb.room_total',
            'ft.paid_amount',
            'ia.invoice_amount'
        )
        ->orderByDesc('sb.invoice_number')
        ->get();

    // ===== Refundable amount per booking =====
    $rules      = DB::table('advance_return_rules')->orderBy('max_day', 'asc')->get();
    $maxRuleDay = $rules->max('max_day');
    $today      = Carbon::today();

    $bookings = $bookings->map(function ($booking) use ($rules, $maxRuleDay, $today) {
        $bookingDate = Carbon::parse($booking->booking_date);

        if ($bookingDate->lt($today)) {
            $booking->refundable_amount = 0;
        } else {
            $daysBeforeBooking = $bookingDate->diffInDays($today);
            if ($daysBeforeBooking > $maxRuleDay) {
                $booking->refundable_amount = 0;
            } else {
                $rule = $rules->firstWhere('max_day', '>=', $daysBeforeBooking);
                $refundPercent = $rule ? $rule->refund_percent : 0;
                $booking->refundable_amount = $booking->paid_amount * $refundPercent / 100;
            }
        }

        return $booking;
    });

    // ===== Summary =====
    $summary = [
        'total_bookings'   => $bookings->count(),
        'total_amount'     => $bookings->sum(fn($b) => $b->invoice_amount + $b->manual_discount_amount),
        'total_discount'   => $bookings->sum('manual_discount_amount'),
        'net_total'        => $bookings->sum('invoice_amount'),
        'total_paid'       => $bookings->sum('paid_amount'),
        'total_due'        => max(0, $bookings->sum('invoice_amount') - $bookings->sum('paid_amount')),
        'total_refundable' => $bookings->sum('refundable_amount'),
    ];

    $startDate = $dateFrom ?: (!$hasOtherFilters ? now()->toDateString() : null);
    $endDate   = $dateTo   ?: (!$hasOtherFilters ? now()->toDateString() : null);

    $setting = \App\Models\CompanySetting::first();
    $data = [
        'company_name'     => $setting->company_name     ?? config('app.name'),
        'company_logo_one' => $setting->company_logo_one ?? '',
        'start_date'       => $startDate ? Carbon::parse($startDate)->format('d M Y') : '—',
        'end_date'         => $endDate   ? Carbon::parse($endDate)->format('d M Y')   : '—',
    ];

    $pdf = Pdf::loadView('pages.spot_booking.details_report_pdf', compact('bookings', 'summary', 'data'))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'dpi'                  => 120,
        ]);

    return $pdf->stream('spot-booking-details-report-' . now()->format('Ymd-His') . '.pdf');
}


    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $spots = Spot::where('status', 1)
    //         ->orderBy('title')
    //         ->get();

    //     $packages = SpotPackage::where('status', 1)
    //         ->orderBy('id')
    //         ->get();

    //     return view('pages.spot_booking.create', compact('spots', 'packages'));
    // }

 public function create()
{
    // Active spots & packages
    $spots = Spot::where('status', 1)
        ->orderBy('spot_order', 'asc')
        ->get();

    $packages = SpotPackage::where('status', 1)->get();

    // Fetch bookings (excluding cancelled)
    $bookings = SpotBooking::where('status', '!=', 2)->get();
    $company = CompanySetting::where('status', 1)->first();

    $calendarData = []; // Calendar yellow/red date info
    $bookedSpots = [];  // Spot block info per date

    foreach ($bookings as $b) {
        // ✅ spot_id na thakle (package-only booking) skip — eta kono spot block korbe na
        if (empty($b->spot_id)) {
            continue;
        }

        $date = $b->booking_date;

        $bookedSpots[$date][] = $b->spot_id;
    }

    // ✅ booked count = distinct spot_id per date (duplicate spot_id thakleo double count hobe na)
    foreach ($bookedSpots as $date => $spotIds) {
        $calendarData[$date] = [
            'booked' => count(array_unique($spotIds)),
        ];
    }

    $bookedRoomIds = Booking::where('status', 1)
        ->where('check_out_date', '>', now()->format('Y-m-d')) // এখনো চলছে এমন
        ->pluck('room_id')
        ->toArray();

    return view('pages.spot_booking.create', [
        'discountLimit' => $company->discount_limit ?? 5,
        'multipleSpotDiscountLimit' => $company->multiple_spot_discount_limit ?? 5,
        'spots' => $spots,
        'packages' => $packages,
        'calendarData' => $calendarData,
        'bookedSpots' => $bookedSpots,
        'rooms' => Room::where('status', 1)
            ->whereNotIn('id', $bookedRoomIds)
            ->orderBy('room_order', 'asc')
            ->get(),
        'additionalServices' => AdditionalService::with(['spots', 'prices'])
            ->where('status', 1)
            ->where('is_backend', 1)
            ->get(),
        'customers' => Customer::where('status', 1)->get(),
        'customerAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020001')->get(),
        'toAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020002')->get(),
        'customerTypes' => CustomerType::where('status', 1)
            ->pluck('type_name', 'id')->all(),
    ]);
}
public function getAvailableRooms(Request $request)
{
    $date = $request->date;
    $excludeInvoice = $request->exclude_invoice;

    // current invoice এর room ids বের করো
    $currentInvoiceRoomIds = $excludeInvoice
        ? Booking::where('booking_no', $excludeInvoice)->pluck('room_id')->toArray()
        : [];

    $bookedRoomIds = Booking::where('status', 1)
        ->where('check_in_date', '<=', $date)
        ->where('check_out_date', '>', $date)
        ->when($excludeInvoice, fn($q) => $q->where('booking_no', '!=', $excludeInvoice))
        ->pluck('room_id')
        ->toArray();

    $rooms = Room::where('status', 1)
        ->where(function($q) use ($bookedRoomIds, $currentInvoiceRoomIds) {
            $q->whereNotIn('id', $bookedRoomIds)
              ->orWhereIn('id', $currentInvoiceRoomIds); // force include
        })
        ->orderBy('room_order', 'asc')
        ->get(['id', 'room_name', 'room_number', 'price_per_night']);

    return response()->json($rooms);
}
 public function create1()
{
    // Active spots & packages
$spots = Spot::with('facilities')
    ->where('status', 1)
    ->orderBy('spot_order', 'asc')
    ->get();

    $packages = SpotPackage::where('status', 1)->get();

    // Fetch bookings (excluding cancelled)
    $bookings = SpotBooking::where('status', '!=', 2)
    ->whereNotNull('spot_id')  // package bookings বাদ
    ->get();
    $company = CompanySetting::where('status', 1)->first();
    $gallery_footer = Gallery::orderBy('id', 'asc')->take(6)->get();
    $calendarData = []; // Calendar yellow date info
    $bookedSpots = []; // Spot block info per date
    $commonFacilities = CommonFacility::where('status',1)->get();
    foreach ($bookings as $b) {
        $date = $b->booking_date;

        // Calendar yellow date
        if (!isset($calendarData[$date])) {
            $calendarData[$date] = ['booked' => 0];
        }
        $calendarData[$date]['booked']++;

        // Block booked spots
        $bookedSpots[$date][] = $b->spot_id;
    }

    return view('pages.frontend.checkzone', [
        'discountLimit' => $company->discount_limit ?? 5,
        'multipleSpotDiscountLimit' => $company->multiple_spot_discount_limit ?? 5, // new multiple spot discount
        'spots' => $spots,
        'company' => $company,
        'gallery_footer' => $gallery_footer,
        'commonFacilities' => $commonFacilities,
        'packages' => $packages,
        'calendarData' => $calendarData,   // yellow date info
        'bookedSpots' => $bookedSpots,     // blocked spots
        'additionalServices' => AdditionalService::with(['spots','prices'])
    ->where('status', 1)
    ->where('is_frontend', 1)
    ->get(),
        'customers' => Customer::where('status', 1)->get(),
        'customerAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020001')->get(),
        'toAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020002')->get(),
        'customerTypes' => CustomerType::where('status', 1)
            ->pluck('type_name', 'id')->all(),
    ]);
}


    #start

    // dd($this->$request->all());

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {


    //     // Validate incoming request
    //     $request->validate([
    //         'booking_date' => 'required|date',
    //         'items' => 'required|json',
    //         'total_price' => 'required|numeric',
    //     ]);

    //     // Decode JSON items
    //     $items = json_decode($request->input('items'), true);

    //     // Create main booking
    //     $booking = SpotBooking::create([
    //         'booking_date' => $request->input('booking_date'),
    //         'total_price' => $request->input('total_price'),
    //     ]);

    //     // Loop through selected items and save details
    //     foreach ($items as $item) {
    //         $booking->details()->create([
    //             'spot_id' => $item['spot_id'],
    //             'package_id' => $item['package_id'],
    //             'persons' => $item['persons'],
    //             'price' => $item['price'],
    //         ]);
    //     }

    //     return redirect()->route('spot-bookings.index')
    //         ->with('success', 'Booking created successfully!');
    // }

    // =================================================================

    // public function store(Request $request)
    // {
    //     dd($request->all());

    //     // Validate incoming data
    //     $request->validate([
    //         'booking_date' => 'required|date',
    //         'items' => 'required|json',
    //         'total_price' => 'required|numeric',
    //     ]);

    //     // Decode JSON items
    //     $items = json_decode($request->input('items'), true);

    //     if (!$items || count($items) == 0) {
    //         return back()->withErrors('No packages selected!');
    //     }


    //     // Save each selected spot + package
    //     foreach ($items as $item) {
    //         SpotBooking::create([
    //             'spot_id' => $item['spot_id'],
    //             'package_id' => $item['package_id'],
    //             'booking_date' => $request->input('booking_date'),
    //             'total_persons' => $item['persons'],
    //             'total_price' => $item['price'],
    //         ]);
    //     }

    //     return redirect()->route('pages.spot-bookings')
    //         ->with('success', 'Booking created successfully!');

    //     // temporarily store services in session

    //     // session([
    //     //     'additional_services' => json_decode($request->additional_services, true)
    //     // ]);

    //     // return redirect()->route('spot-bookings.services', $booking->id);
    // }

    // ====================================================================================


    #end

   public function store(Request $request)
{
    // dd($request);
    // ================= VALIDATION =================
    $request->validate([
        'booking_date' => 'required|date',
        'items' => 'required|json',
        'total_price' => 'required|numeric',
        'additional_services' => 'nullable|json',
        'invoice_adjustment_discount' => 'nullable|json',
    ]);
     
    $items = json_decode($request->items, true);
    $additionalServices = json_decode($request->additional_services, true);
// dd($additionalServices);
    if (empty($items)) {
        return back()->withErrors('No items selected!');
    }
    // ================= CUSTOMER INFO =================
    $customer = FinanceAccount::select('account_name', 'account_mobile')
        ->where('id', $request->customer_id)
        ->first();

    $customerName = $customer->account_name ?? 'N/A';
    $customerMobile = $customer->account_mobile ?? 'N/A';


    // ================= INVOICE NUMBER =================
    // $lastInvoice = SpotBooking::latest('id')->first();
    // $lastNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0;
    // $newInvoiceNumber = 'INV-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

      $salesNo = DB::table('invoiceno')->first('sales_no');
            $getSalesNo = $salesNo->sales_no;
            $salesNumber = 'INV' . str_pad($getSalesNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update([
                'sales_no' => $getSalesNo + 1,
            ]);
    // ================= SAVE BOOKINGS =================
    // dd($request);
    foreach ($items as $item) {

        // 🔹 Spot only
        if ($item['type'] === 'spot') {
            SpotBooking::create([
                'invoice_number'   => $salesNumber,
                'spot_id'          => $item['id'],
                'package_id'       => null,
                'booking_date'     => $request->booking_date,
                'total_persons'    => $item['max_capacity'] ?? 0,
                'total_price'      => $item['price'],
                'discount_percent' => $request->discount_percent ?? 0,
                'spot_discount_percent' => $request->spot_discount_percent ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'invoice_adjustment_discount' => $request->invoice_adjustment_discount ?? 0,
                'customer_name' => $customerName,
                'customer_mobile' => $customerMobile,
                'status'           => 0,
            ]);
        }

        // items loop এর ভেতরে
if ($item['type'] === 'room') {
    Booking::create([
        'booking_no'      => $salesNumber,
        'customer_id'     => $request->customer_id, // customer_id লাগবে
        'room_id'         => $item['id'],
        'check_in_date'   => $request->booking_date,
        'check_out_date'  => Carbon::parse($request->booking_date)->addDay()->format('Y-m-d'),
        'check_in_datetime'      => '10:59:59',
        'check_out_datetime'      => '10:59:59',
        'total_days'      => 1,
        'total_amount'    => $item['price'],
        //  'total_amount'    =>0,
        'payment_status'  => 0,
        'Booking_status'  => 0,
        'status'          => 1,
    ]);
}

        // 🔹 Package (with or without spot)
        if ($item['type'] === 'package') {
            SpotBooking::create([
                'invoice_number'   => $salesNumber,
                'spot_id'          => null,
                'package_id'       => $item['id'],
                'booking_date'     => $request->booking_date,
                'total_persons'    => $item['persons'] ?? 0,
                'total_price'      => $item['price'],
                'discount_percent' => $request->discount_percent ?? 0,
                'status'           => 0,
            ]);
        }
    }

    // ================= ADDITIONAL SERVICES =================
    if (!empty($additionalServices)) {
        foreach ($additionalServices as $service) {
            DB::table('spot_booking_details')->insert([
                'invoice_number'         => $salesNumber,
                'additional_service_id'  => $service['service_id'],
                'price'                  => $service['price'],
                'quantity'               => $service['qty'] ?? 1,
                'total_price'            => $service['total'],
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    // ================= FINANCE PART =================
    $netTotalAmount = $request->total_price;
    $receive_amount = $request->receive_amount ?? 0;
    $remarks = $request->remarks;
    $done_by = Auth::user()->name;

    $customerId = $request->customer_id;
    $customerName = FinanceAccount::where('id', $customerId)->value('account_name');

    $payAccountId = $request->receive_account;
    $payAccName = FinanceAccount::where('id', $payAccountId)->value('account_name');

    $payment_type = FinanceAccount::leftJoin('finance_groups', 'finance_accounts.account_group_code', '=', 'finance_groups.group_code')
        ->where('finance_accounts.id', $payAccountId)
        ->value('finance_groups.group_name');

    // ================= VOUCHER =================
    $voucher = DB::table('invoiceno')->first('voucher_no');
    $voucherNo = '01SV' . str_pad($voucher->voucher_no, 6, '0', STR_PAD_LEFT);

    DB::table('invoiceno')->update([
        'voucher_no' => $voucher->voucher_no + 1
    ]);

    // ================= FINANCE TRANSACTIONS =================

    // Customer Dr
    FinanceTransaction::create([
        'company_code' => '01',
        'invoice_no' => $salesNumber,
        'voucher_no' => $voucherNo,
        'voucher_date' => $request->booking_date,
        'acid' => $customerId,
        'to_acc_name' => $GLOBALS['SalesAccountName'],
        'type' => 'SV',
        'amount' => $netTotalAmount,
        'balance_type' => 'Dr',
        'transaction_date' => $request->booking_date,
        'narration' => 'Zone Booking',
        'transaction_by' => $done_by,
        'done_by' => $done_by,
    ]);

    // Sales Cr
    FinanceTransaction::create([
        'company_code' => '01',
        'invoice_no' => $salesNumber,
        'voucher_no' => $voucherNo,
        'voucher_date' => $request->booking_date,
        'acid' => $GLOBALS['SalesAccountID'],
        'to_acc_name' => $customerName,
        'type' => 'SV',
        'amount' => $netTotalAmount,
        'balance_type' => 'Cr',
        'transaction_date' => $request->booking_date,
        'narration' => 'Zone Booking',
        'transaction_by' => $done_by,
        'done_by' => $done_by,
    ]);

    // Payment received
    if ($receive_amount > 0) {

        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $salesNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $customerId,
            'to_acc_name' => $payAccName,
            'type' => 'SV',
            'amount' => $receive_amount,
            'balance_type' => 'Cr',
            'payment_type' => $payment_type,
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking Payment',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $salesNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $payAccountId,
            'to_acc_name' => $customerName,
            'type' => 'SV',
            'amount' => $receive_amount,
            'balance_type' => 'Dr',
            'payment_type' => $payment_type,
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking Payment',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);
    }

    // ================= REDIRECT =================
    return redirect()
        ->route('spot-bookings.index')
        ->with('success', 'Booking created successfully!');
}
   public function store1(Request $request)
{
    // dd($request);
    // ================= VALIDATION =================
     $validated = $request->validate([
         'customer_name' => 'required|string',
        'customer_mobile' => 'required|string',
       
    ]);

    $request->validate([
        'booking_date' => 'required|date',
        'items' => 'required|json',
        'total_price' => 'required|numeric',
        'additional_services' => 'nullable|json',
        'invoice_adjustment_discount' => 'nullable|json',
    ]);
     

            // ================= CUSTOMER & FINANCE ACCOUNT =================
    $financeAccount = FinanceAccount::updateOrCreate(
    ['account_mobile' => $validated['customer_mobile']], // 🔑 mobile দিয়ে check
    [
        'account_name' => $validated['customer_name'],
        'financegroup_id' => '7',
        'account_group_code' => $GLOBALS['CustomerGroupCode'],
        'account_address' => $request['customer_address'],
        'account_company_code' => '01',
        'account_status' => 1,
        'account_done_by' => $validated['customer_name'],
    ]
);

        // 🔥 Customer create/update
       $customer = Customer::updateOrCreate(
    ['customer_mobile' => $validated['customer_mobile']],
    [
        'ac_id' => $financeAccount->id,
        'customer_type' => 1,
        'customer_name' => $validated['customer_name'],
        'customer_address' => $request['customer_address'],
        'status' => 1,
        'done_by' => $validated['customer_name'],
    ]
);

        $customerId = $financeAccount->id;
    $items = json_decode($request->items, true);
    $additionalServices = json_decode($request->additional_services, true);

    if (empty($items)) {
        return back()->withErrors('No items selected!');
    }
    // ================= CUSTOMER INFO =================
    $customer = FinanceAccount::select('account_name', 'account_mobile')
        ->where('id', $customerId)
        ->first();

    $customerName = $customer->account_name ?? 'N/A';
    $customerMobile = $customer->account_mobile ?? 'N/A';


    // ================= INVOICE NUMBER =================
    // $lastInvoice = SpotBooking::latest('id')->first();
    // $lastNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0;
    // $newInvoiceNumber = 'INV-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

      $salesNo = DB::table('invoiceno')->first('sales_no');
            $getSalesNo = $salesNo->sales_no;
            $salesNumber = 'INV' . str_pad($getSalesNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update([
                'sales_no' => $getSalesNo + 1,
            ]);
    // ================= SAVE BOOKINGS =================
    foreach ($items as $item) {

        // 🔹 Spot only
        if ($item['type'] === 'spot') {
            SpotBooking::create([
                'invoice_number'   => $salesNumber,
                'spot_id'          => $item['id'],
                'package_id'       => null,
                'booking_date'     => $request->booking_date,
                'total_persons'    => $item['max_capacity'] ?? 0,
                'total_price'      => $item['price'],
                'discount_percent' => $request->discount_percent ?? 0,
                'spot_discount_percent' => $request->spot_discount_percent ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'invoice_adjustment_discount' => $request->invoice_adjustment_discount ?? 0,
                'customer_name' => $customerName,
                'customer_mobile' => $customerMobile,
                'status'           => 0,
            ]);
        }

        // 🔹 Package (with or without spot)
        if ($item['type'] === 'package') {
            SpotBooking::create([
                'invoice_number'   => $salesNumber,
                'spot_id'          => null,
                'package_id'       => $item['id'],
                'booking_date'     => $request->booking_date,
                'total_persons'    => $item['persons'] ?? 0,
                'total_price'      => $item['price'],
                'discount_percent' => $request->discount_percent ?? 0,
                'status'           => 0,
            ]);
        }
    }

    // ================= ADDITIONAL SERVICES =================
    if (!empty($additionalServices)) {
        foreach ($additionalServices as $service) {
            DB::table('spot_booking_details')->insert([
                'invoice_number'         => $salesNumber,
                'additional_service_id'  => $service['service_id'],
                'price'                  => $service['price'],
                'quantity'               => $service['qty'] ?? 1,
                'total_price'            => $service['total'],
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    // ================= FINANCE PART =================
    $netTotalAmount = $request->total_price;
    $receive_amount = $request->receive_amount ?? 0;
    $remarks = $request->remarks;
    $done_by = $validated['customer_name'];

     $customerId = $financeAccount->id;
    $customerName = FinanceAccount::where('id', $customerId)->value('account_name');

    $payAccountId = $request->receive_account;
    $payAccName = FinanceAccount::where('id', $payAccountId)->value('account_name');

    $payment_type = FinanceAccount::leftJoin('finance_groups', 'finance_accounts.account_group_code', '=', 'finance_groups.group_code')
        ->where('finance_accounts.id', $payAccountId)
        ->value('finance_groups.group_name');

    // ================= VOUCHER =================
    $voucher = DB::table('invoiceno')->first('voucher_no');
    $voucherNo = '01SV' . str_pad($voucher->voucher_no, 6, '0', STR_PAD_LEFT);

    DB::table('invoiceno')->update([
        'voucher_no' => $voucher->voucher_no + 1
    ]);

    // ================= FINANCE TRANSACTIONS =================
 $salesAccountName = DB::table('finance_accounts')
            ->where('id', $GLOBALS['SalesAccountID'])
            ->value('account_name') ?? 'Sales Account';
    // Customer Dr
    FinanceTransaction::create([
        'company_code' => '01',
        'invoice_no' => $salesNumber,
        'voucher_no' => $voucherNo,
        'voucher_date' => $request->booking_date,
        'acid' => $customerId,
        'to_acc_name' => $salesAccountName,
        'type' => 'SV',
        'amount' => $netTotalAmount,
        'balance_type' => 'Dr',
        'transaction_date' => $request->booking_date,
        'narration' => 'Zone Booking',
        'transaction_by' => $done_by,
        'done_by' => $done_by,
    ]);

    // Sales Cr
    FinanceTransaction::create([
        'company_code' => '01',
        'invoice_no' => $salesNumber,
        'voucher_no' => $voucherNo,
        'voucher_date' => $request->booking_date,
       'acid' => $GLOBALS['SalesAccountID'],
        'to_acc_name' => $customerName,
        'type' => 'SV',
        'amount' => $netTotalAmount,
        'balance_type' => 'Cr',
        'transaction_date' => $request->booking_date,
        'narration' => 'Zone Booking',
        'transaction_by' => $done_by,
        'done_by' => $done_by,
    ]);

    // Payment received
    if ($receive_amount > 0) {

        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $salesNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $customerId,
            'to_acc_name' => $payAccName,
            'type' => 'SV',
            'amount' => $receive_amount,
            'balance_type' => 'Cr',
            'payment_type' => $payment_type,
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking Payment',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $salesNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $payAccountId,
            'to_acc_name' => $customerName,
            'type' => 'SV',
            'amount' => $receive_amount,
            'balance_type' => 'Dr',
            'payment_type' => $payment_type,
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking Payment',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);
    }

    // ================= REDIRECT =================
      return redirect()->back()->with('booking_success', 'Booking Successful!');
}

public function edit1($invoiceNumber)
{
    $bookings = SpotBooking::where('invoice_number', $invoiceNumber)->get();

    if ($bookings->isEmpty()) {
        return redirect()->route('spot-bookings.index')
            ->with('error', 'Booking not found!');
    }

    $firstBooking = $bookings->first();

    $spots = Spot::where('status', 1)->orderBy('spot_order', 'asc')->get();
    $packages = SpotPackage::where('status', 1)->get();
    $company = CompanySetting::where('status', 1)->first();

    $services = DB::table('spot_booking_details')
        ->where('invoice_number', $invoiceNumber)
        ->get();

    $selectedServices = $services->pluck('additional_service_id')->toArray();

    // Calendar data for highlighting
    $calendarData = [];
    $bookedSpots = [];

    $allBookings = SpotBooking::where('status', '!=', 2)
        ->where('invoice_number', '!=', $invoiceNumber) // ✅ exclude current invoice
        ->get();

    // ✅ null/0 spot_id (package-only row) বাদ দিয়ে current invoice এর spot গুলো নাও
    $currentInvoiceSpots = $bookings->pluck('spot_id')
        ->filter()                 // null/0/empty বাদ
        ->map(fn($id) => (int)$id)
        ->values()
        ->toArray();

    $receivedAmount = FinanceTransaction::where('invoice_no', $invoiceNumber)
        ->where('status', 0)
        ->where('balance_type', 'Cr')
        ->whereNotNull('payment_type')
        ->sum('amount');

    $financeTransaction = DB::table('finance_transactions')
        ->where('invoice_no', $invoiceNumber)
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->first();

    // ✅ spot_id null (package-only) booking বাদ দিয়ে bookedSpots বানাও
    foreach ($allBookings as $b) {
        if (empty($b->spot_id)) {
            continue;
        }

        $date = $b->booking_date;
        $bookedSpots[$date][] = $b->spot_id;
    }

    // ✅ booked count = distinct spot_id per date
    foreach ($bookedSpots as $date => $spotIds) {
        $calendarData[$date] = [
            'booked' => count(array_unique($spotIds)),
        ];
    }

    $packagePriceRules = AdditionalServicePrice::all()
        ->groupBy('additional_service_id')
        ->map(function ($items) {
            $result = [];
            foreach ($items as $item) {
                $key = $item->min_person . '-' . $item->max_person;
                $result[$key] = (float) $item->price;
            }
            return $result;
        });

    $serviceSpotMap = DB::table('additional_service_spot')->get()
        ->groupBy('additional_service_id')
        ->map(fn($items) => $items->pluck('spot_id')->map(fn($id) => (int)$id)->toArray());

    $bookedRoomBookings = Booking::where('booking_no', $invoiceNumber)->get();
    $currentInvoiceRoomIds = $bookedRoomBookings->pluck('room_id')->map(fn($id) => (int)$id)->toArray();

    $bookedRoomIds = Booking::where('status', 1)
        ->where('booking_no', '!=', $invoiceNumber)
        ->where('check_out_date', '>', now()->format('Y-m-d'))
        ->pluck('room_id')
        ->toArray();

    return view('pages.spot_booking.edit1', [
        'invoiceNumber' => $invoiceNumber,
        'booking' => $bookings,
        'serviceSpotMap' => $serviceSpotMap,
        'packagePriceRules' => $packagePriceRules,
        'firstBooking' => $firstBooking,
        'receivedAmount' => $receivedAmount,
        'financeTransaction' => $financeTransaction,
        'services' => $services,
        'currentInvoiceSpots' => $currentInvoiceSpots,
        'discountLimit' => $company->discount_limit ?? 5,
        'multipleSpotDiscountLimit' => $company->multiple_spot_discount_limit ?? 5,
        'spots' => $spots,
        'selectedServices' => $selectedServices,
        'packages' => $packages,
        'additionalServices' => AdditionalService::with(['spots', 'prices'])
            ->where('status', 1)
            ->where('is_backend', 1)
            ->get(),
        'customers' => Customer::where('status', 1)->get(),
        'customerAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020001')->get(),
        'toAccounts' => FinanceAccount::where('account_status', 1)
            ->where('account_group_code', '100020002')->get(),
        'customerTypes' => CustomerType::where('status', 1)
            ->pluck('type_name', 'id')->all(),
        'calendarData' => $calendarData,
        'bookedSpots' => $bookedSpots,
        'rooms' => Room::where('status', 1)
            ->where(function ($q) use ($bookedRoomIds, $currentInvoiceRoomIds) {
                $q->whereNotIn('id', $bookedRoomIds)
                  ->orWhereIn('id', $currentInvoiceRoomIds);
            })
            ->orderBy('room_order', 'asc')
            ->get(),
        'currentInvoiceRoomIds' => $currentInvoiceRoomIds,
        'currentInvoiceRooms' => $bookedRoomBookings,
    ]);
}


public function update1(Request $request, $invoiceNumber)
{
    DB::beginTransaction();

    try {

        // ================= VALIDATION =================
        $request->validate([
            'booking_date' => 'required|date',
            'items' => 'required|json',
            'total_price' => 'required|numeric',
            'additional_services' => 'nullable|json',
        ]);

        $items = json_decode($request->items, true);
        $additionalServices = json_decode($request->additional_services, true);

        if (empty($items)) {
            return back()->withErrors('No items selected!');
        }

        // ================= DELETE OLD DATA =================
        SpotBooking::where('invoice_number', $invoiceNumber)->delete();

        DB::table('spot_booking_details')
            ->where('invoice_number', $invoiceNumber)
            ->delete();
        // Old room bookings delete
        Booking::where('booking_no', $invoiceNumber)->delete();
        FinanceTransaction::where('invoice_no', $invoiceNumber)->delete();

        // ================= CUSTOMER =================
        $customer = FinanceAccount::find($request->customer_id);

        $customerName = $customer->account_name ?? 'N/A';
        $customerMobile = $customer->account_mobile ?? 'N/A';

        // ================= SAVE NEW BOOKING ROWS =================
        foreach ($items as $item) {

            if ($item['type'] === 'spot') {

                SpotBooking::create([
                    'invoice_number' => $invoiceNumber,
                    'spot_id' => $item['id'],
                    'package_id' => null,
                    'booking_date' => $request->booking_date,
                    'total_persons' => $item['max_capacity'] ?? 0,
                    'total_price' => $item['price'],
                    'discount_percent' => $request->discount_percent ?? 0,
                    'spot_discount_percent' => $request->spot_discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'invoice_adjustment_discount' => $request->invoice_adjustment_discount ?? 0,
                    'customer_name' => $customerName,
                    'customer_mobile' => $customerMobile,
                    'status' => 0,
                ]);
            }
if ($item['type'] === 'room') {
    Booking::create([
        'booking_no'     => $invoiceNumber,
        'customer_id'    => $request->customer_id,
        'room_id'        => $item['id'],
        'check_in_date'  => $request->booking_date,
        'check_out_date' => Carbon::parse($request->booking_date)->addDay()->format('Y-m-d'),
        'total_days'     => 1,
        'total_amount'   => $item['price'],
        'payment_status' => 0,
        'Booking_status' => 0,
        'status'         => 1,
    ]);
}
            if ($item['type'] === 'package') {

                SpotBooking::create([
                    'invoice_number' => $invoiceNumber,
                    'spot_id' => null,
                    'package_id' => $item['id'],
                    'booking_date' => $request->booking_date,
                    'total_persons' => $item['persons'] ?? 0,
                    'total_price' => $item['price'],
                    'discount_percent' => $request->discount_percent ?? 0,
                    'status' => 0,
                ]);
            }
        }

        // ================= ADDITIONAL SERVICES =================
        if (!empty($additionalServices)) {
            foreach ($additionalServices as $service) {

                DB::table('spot_booking_details')->insert([
                    'invoice_number' => $invoiceNumber,
                    'additional_service_id' => $service['service_id'],
                    'price' => $service['price'],
                    'quantity' => $service['qty'] ?? 1,
                    'total_price' => $service['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================= FINANCE RE-INSERT =================
        $netTotalAmount = $request->total_price;
        $receive_amount = $request->receive_amount ?? 0;
        $done_by = Auth::user()->name;

        $customerId = $request->customer_id;
        $payAccountId = $request->receive_account;

        $payAccName = FinanceAccount::where('id', $payAccountId)
            ->value('account_name');

        $payment_type = FinanceAccount::leftJoin(
                'finance_groups',
                'finance_accounts.account_group_code',
                '=',
                'finance_groups.group_code'
            )
            ->where('finance_accounts.id', $payAccountId)
            ->value('finance_groups.group_name');

        $voucher = DB::table('invoiceno')->first('voucher_no');
        $voucherNo = '01SV' . str_pad($voucher->voucher_no, 6, '0', STR_PAD_LEFT);

        DB::table('invoiceno')->update([
            'voucher_no' => $voucher->voucher_no + 1
        ]);

        // Customer Dr
        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $invoiceNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $customerId,
            'type' => 'SV',
            'amount' => $netTotalAmount,
            'balance_type' => 'Dr',
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        // Sales Cr
        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $invoiceNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $request->booking_date,
            'acid' => $GLOBALS['SalesAccountID'],
            'type' => 'SV',
            'amount' => $netTotalAmount,
            'balance_type' => 'Cr',
            'transaction_date' => $request->booking_date,
            'narration' => 'Zone Booking',
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        // Payment
        if ($receive_amount > 0) {

            FinanceTransaction::create([
                'company_code' => '01',
                'invoice_no' => $invoiceNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => $request->booking_date,
                'acid' => $customerId,
                'type' => 'SV',
                'amount' => $receive_amount,
                'balance_type' => 'Cr',
                'payment_type' => $payment_type,
                'transaction_date' => $request->booking_date,
                'narration' => 'Zone Booking Payment',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);

            FinanceTransaction::create([
                'company_code' => '01',
                'invoice_no' => $invoiceNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => $request->booking_date,
                'acid' => $payAccountId,
                'type' => 'SV',
                'amount' => $receive_amount,
                'balance_type' => 'Dr',
                'payment_type' => $payment_type,
                'transaction_date' => $request->booking_date,
                'narration' => 'Zone Booking Payment',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);
        }

        DB::commit();

        return redirect()
            ->route('spot-bookings.index')
            ->with('success', 'Booking updated successfully!');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
    #start


    // public function store(Request $request)
    // {
    //     // Validate incoming data
    //     $request->validate([
    //         'booking_date' => 'required|date',
    //         'items' => 'required|json',
    //         'total_price' => 'required|numeric',
    //     ]);

    //     // Decode JSON items
    //     $items = json_decode($request->input('items'), true);

    //     if (!$items || count($items) == 0) {
    //         return back()->withErrors('No packages selected!');
    //     }

    //     $totalPrice = 0;
    //     $firstBookingId = null;

    //     // Save each selected spot + package
    //     foreach ($items as $item) {
    //         $booking = SpotBooking::create([
    //             'spot_id' => $item['spot_id'],
    //             'package_id' => $item['package_id'],
    //             'booking_date' => $request->input('booking_date'),
    //             'total_persons' => $item['persons'],
    //             'total_price' => $item['price'],
    //         ]);

    //         $totalPrice += $item['price'];

    //         if (!$firstBookingId) {
    //             $firstBookingId = $booking->id;
    //         }
    //     }

    //     // Save additional services temporarily in session for next page
    //     session([
    //         'additional_services' => json_decode($request->additional_services, true),
    //         'booking_total_price' => $totalPrice
    //     ]);

    //     // Redirect to additional services page for first booking (or you can modify as needed)
    //     return redirect()->route('spot-bookings.services', $firstBookingId)
    //         ->with('success', 'Booking created successfully! Please select additional services.');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'booking_date' => 'required|date',
    //         'items' => 'required|json',
    //     ]);

    //     $items = json_decode($request->items, true);

    //     if (!is_array($items) || count($items) === 0) {
    //         return back()->withErrors('No packages selected!');
    //     }

    //     $totalPrice = 0;
    //     $bookingIds = [];

    //     foreach ($items as $item) {

    //         // safety check
    //         if (
    //             empty($item['spot_id']) ||
    //             empty($item['package_id']) ||
    //             empty($item['persons']) ||
    //             empty($item['price'])
    //         ) {
    //             continue;
    //         }

    //         $booking = SpotBooking::create([
    //             'spot_id'        => $item['spot_id'],
    //             'package_id'     => $item['package_id'],
    //             'booking_date'   => $request->booking_date,
    //             'total_persons'  => $item['persons'],
    //             'total_price'    => $item['price'],
    //             'status'         => 0, // pending
    //         ]);

    //         $bookingIds[] = $booking->id;
    //         $totalPrice += (float) $item['price'];
    //     }

    //     if (count($bookingIds) === 0) {
    //         return back()->withErrors('Booking failed!');
    //     }

    //     // store services + booking ids in session
    //     session([
    //         'additional_services' => json_decode($request->additional_services ?? '[]', true),
    //         'booking_total_price' => $totalPrice,
    //         'booking_ids'         => $bookingIds
    //     ]);

    //     // redirect using first booking id
    //     return redirect()
    //         ->route('spot-bookings.services', $bookingIds[0])
    //         ->with('success', 'Booking created successfully! Please select additional services.');
    // }

    #end


    /**
     * Display the specified resource.
     */
    public function show(SpotBooking $spotBooking)
    {
        //
    }

    public function showInvoice($invoice)
{
    /* ================= Spot & Package Bookings ================= */
    $bookings = DB::table('spot_bookings as sb')
        ->leftJoin('spots as s', 's.id', '=', 'sb.spot_id')
        ->leftJoin('spot_packages as sp', 'sp.id', '=', 'sb.package_id')
        ->where('sb.invoice_number', $invoice)
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.total_price',
            'sb.discount_percent',
            'sb.spot_discount_percent',
            'sb.invoice_adjustment_discount',
            'sb.spot_id',
            'sb.package_id',
            DB::raw("s.title as spot_title"),
            DB::raw("sp.name as package_name"),
            DB::raw("sp.persons as package_persons"),
            DB::raw("sp.price as package_price")
        )
        ->get();

    if ($bookings->isEmpty()) {
        abort(404);
    }
//dd($bookings);
    /* ================= Additional Services ================= */
    $services = \DB::table('spot_booking_details as bd')
        ->join('additional_services as a', 'a.id', '=', 'bd.additional_service_id')
        ->where('bd.invoice_number', $invoice)
        ->select(
            'a.title as service_title',
            'bd.price',
            'bd.quantity',
            'bd.total_price'
        )
        ->get();

    /* ================= Spot & Package Totals ================= */
    // Spot total with spot discount
    $spotTotal = $bookings->whereNotNull('spot_id')->sum(function($b) {
        return $b->total_price - ($b->total_price * ($b->spot_discount_percent ?? 0) / 100);
    });

    // Package total (no additional discount)
    $packageTotal = $bookings->whereNotNull('package_id')->sum('total_price');

    // Service total
    $serviceTotal = $services->sum('total_price');

    /* ================= Grand Total & Discount ================= */
$subTotal = $spotTotal + $packageTotal + $serviceTotal;

// 🔥 DB থেকে flat discount নাও (same invoice → same discount)
$discountAmount = DB::table('spot_bookings')
    ->where('invoice_number', $invoice)
    ->max('discount_amount');
$discountAmountAdjust = DB::table('spot_bookings')
    ->where('invoice_number', $invoice)
    ->max('invoice_adjustment_discount');

$discountAmount = $discountAmount ?? 0;

$netTotal = $subTotal - $discountAmount;


$invoiceSummary = [
    'invoice'          => $invoice,
    'date'             => $bookings->first()->booking_date,
    'customer_name'    => $bookings->first()->customer_name,
    'customer_mobile'  => $bookings->first()->customer_mobile,
    'status'           => $bookings->first()->status,

    'spot_total'       => $spotTotal,
    'package_total'    => $packageTotal,
    'service_total'    => $serviceTotal,

    'sub_total'        => $subTotal,
    'discount_percent' => $bookings->first()->discount_percent, // info only
    'discount_amount'  => $discountAmount,
    'invoice_adjustment_discount'  => $discountAmountAdjust,

    'net_total'        => $netTotal,
];
// dd($invoiceSummary);
    /* ================= Payment Info ================= */
    $invoiceNo = $invoiceSummary['invoice'];

    $invoiceAmount = DB::table('finance_transactions')
        ->where('invoice_no', $invoiceNo)
        ->where('type', 'SV')
        ->where('balance_type', 'Cr')
        ->whereNull('payment_type')
        ->sum('amount');

    $receivedAmount = DB::table('finance_transactions')
        ->where('invoice_no', $invoiceNo)
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->sum('amount');

    $dueAmount = $invoiceAmount - $receivedAmount;

    $invoiceSummary['invoice_amount']  = $invoiceAmount;
    $invoiceSummary['received_amount'] = $receivedAmount;
    $invoiceSummary['due_amount']      = $dueAmount;

    /* ================= Customer Info ================= */
    $customer = \DB::table('finance_transactions as ft')
        ->join('finance_accounts as fa', 'fa.id', '=', 'ft.acid')
        ->where('ft.invoice_no', $invoice)
        ->where('ft.balance_type', 'Dr')
        ->select('fa.account_name', 'fa.account_mobile')
        ->first();

    $invoiceSummary['customer_name']   = $customer->account_name ?? 'N/A';
    $invoiceSummary['customer_mobile'] = $customer->account_mobile ?? 'N/A';

    return view('pages.spot_booking.invoice_show', compact(
        'bookings', 'services', 'invoiceSummary', 'invoiceAmount', 'receivedAmount', 'dueAmount'
    ));
}



   public function exportInvoicePdf($invoice)
{
    /* ===== Spot & Package Bookings ===== */
    $bookings = DB::table('spot_bookings as sb')
        ->leftJoin('spots as s', 's.id', '=', 'sb.spot_id')
        ->leftJoin('spot_packages as sp', 'sp.id', '=', 'sb.package_id')
        ->where('sb.invoice_number', $invoice)
        ->select(
            'sb.invoice_number',
            'sb.booking_date',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.status',
            'sb.total_price',
            'sb.discount_percent',
            'sb.spot_id',
            'sb.package_id',
            DB::raw("COALESCE(s.title, 'N/A') as spot_title"),
            DB::raw("COALESCE(sp.name, '') as package_name"),
            DB::raw("COALESCE(sp.persons, 0) as package_persons"),
            DB::raw("COALESCE(sp.price, 0) as package_price"),
            DB::raw("sb.spot_discount_percent")
        )
        ->get();

    if ($bookings->isEmpty()) {
        abort(404, 'Invoice not found');
    }

    /* ===== Additional Services ===== */
    $services = DB::table('spot_booking_details as bd')
        ->join('additional_services as a', 'a.id', '=', 'bd.additional_service_id')
        ->where('bd.invoice_number', $invoice)
        ->select(
            'bd.additional_service_id',
            'a.title as service_title',
            'bd.price',
            'bd.quantity',
            'bd.total_price'
        )
        ->get();

    /* ===== Spot, Package, Service Totals ===== */
    $spotTotal = $bookings->whereNotNull('spot_id')
        ->sum(fn($b) => $b->total_price - ($b->total_price * ($b->spot_discount_percent ?? 0)/100));

    $packageTotal = $bookings->whereNotNull('package_id')
        ->sum('total_price'); // যদি package discount না থাকে, সরাসরি sum

    $serviceTotal = $services->sum('total_price');
    $discountAmountAdjust = DB::table('spot_bookings')
    ->where('invoice_number', $invoice)
    ->max('invoice_adjustment_discount');

 /* ===== Sub Total ===== */
$subTotal = $spotTotal + $packageTotal + $serviceTotal;

/* ===== Flat Discount (DB) ===== */
$discountAmount = DB::table('spot_bookings')
    ->where('invoice_number', $invoice)
    ->max('discount_amount');

$discountAmount = $discountAmount ?? 0;

/* ===== Net Total ===== */
$netTotal = $subTotal - $discountAmount;


   $invoiceSummary = [
    'invoice'          => $invoice,
    'date'             => $bookings->first()->booking_date,
    'customer_name'    => $bookings->first()->customer_name,
    'customer_mobile'  => $bookings->first()->customer_mobile,
    'status'           => $bookings->first()->status,

    'spot_total'       => $spotTotal,
    'package_total'    => $packageTotal,
    'service_total'    => $serviceTotal,

    'sub_total'        => $subTotal,
    'discount_percent' => $bookings->first()->discount_percent, // info only
    'discount_amount'  => $discountAmount,
      'invoice_adjustment_discount'  => $discountAmountAdjust,
    'net_total'        => $netTotal,
];

    /* ===== Payment Summary ===== */
    $invoiceAmount = DB::table('finance_transactions')
        ->where('invoice_no', $invoice)
        ->where('balance_type', 'Cr')
        ->whereNull('payment_type')
        ->sum('amount');

    $receivedAmount = DB::table('finance_transactions')
        ->where('invoice_no', $invoice)
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->sum('amount');

    $invoiceSummary['invoice_amount']  = $invoiceAmount;
    $invoiceSummary['received_amount'] = $receivedAmount;
    $invoiceSummary['due_amount']      = $invoiceAmount - $receivedAmount;

    /* ===== Customer Info ===== */
    $customer = DB::table('finance_transactions as ft')
        ->join('finance_accounts as fa', 'fa.id', '=', 'ft.acid')
        ->where('ft.invoice_no', $invoice)
        ->where('ft.balance_type', 'Dr')
        ->select('fa.account_name', 'fa.account_mobile')
        ->first();

    $invoiceSummary['customer_name']   = $customer->account_name ?? 'N/A';
    $invoiceSummary['customer_mobile'] = $customer->account_mobile ?? 'N/A';

    /* ===== Spot & Service Facilities ===== */
    $spotIds = $bookings->pluck('spot_id')->unique()->filter()->values();
    $serviceIds = $services->pluck('additional_service_id')->unique()->filter()->values();

    $spotFacilities = DB::table('spot_facilities')
        ->whereIn('spot_id', $spotIds)
        ->pluck('facility');

    $commonFacilities = CommonFacility::where('status', 1)
        ->pluck('facility_name');

    $allFacilities = $spotFacilities->merge($commonFacilities)->unique();

    /* ===== Terms & Conditions ===== */
  $terms = DB::table('terms_conditions')
    ->where('is_active', 1)
    ->where(function ($q) use ($spotIds, $serviceIds) {

        // 1️⃣ Spot Included
        if ($spotIds->isNotEmpty()) {
            $q->orWhere(function ($sub) use ($spotIds) {
                $sub->where('term_type', 'included')
                    ->where('term_type1', 'spot')
                    ->whereIn('spot_id', $spotIds);
            });
        }

        // 2️⃣ Spot Common
        $q->orWhere(function ($sub) {
            $sub->where('term_type', 'common')
                ->where('term_type1', 'spot');
        });

        // 3️⃣ Service Included
        if ($serviceIds->isNotEmpty()) {
            $q->orWhere(function ($sub) use ($serviceIds) {
                $sub->where('term_type', 'included')
                    ->where('term_type1', 'service')
                    ->whereIn('additional_service_id', $serviceIds);
            });
        }

        // 4️⃣ Service Common
        $q->orWhere(function ($sub) {
            $sub->where('term_type', 'common')
                ->where('term_type1', 'service');
        });

        // 5️⃣ Global Common
        $q->orWhere(function ($sub) {
            $sub->where('term_type', 'common')
                ->where('term_type1', 'common');
        });
    })

    // 🔥 Group Priority
    ->orderByRaw("
        CASE
            WHEN term_type='included' AND term_type1='spot' THEN 1
            WHEN term_type='common'   AND term_type1='spot' THEN 2
            WHEN term_type='included' AND term_type1='service' THEN 3
            WHEN term_type='common'   AND term_type1='service' THEN 4
            WHEN term_type='common'   AND term_type1='common' THEN 5
            ELSE 6
        END
    ")

    // 🔥 Inside Group Ordering
    ->orderBy('sort_order', 'asc')
    ->orderBy('id', 'asc')

    ->get();
/* ===== Room Bookings ===== */
/* ===== Room Bookings ===== */
$roomBookings = DB::table('bookings as b')
    ->leftJoin('rooms as r', 'r.id', '=', 'b.room_id')
    ->where('b.booking_no', $invoice)
    ->select(
        'b.id',
        'b.room_id',
        'b.total_amount',
        'b.total_days',
        'b.check_in_date',
        'b.check_out_date',
        'b.discount',
        DB::raw("COALESCE(r.room_name, 'N/A') as room_name"),
        DB::raw("COALESCE(r.room_number, '') as room_number"),
        DB::raw("COALESCE(r.price_per_night, 0) as price_per_night")
    )
    ->get();

$roomTotal = $roomBookings->sum('total_amount');

// ✅ sub_total আবার recalculate করুন (room সহ)
$subTotal = $spotTotal + $packageTotal + $serviceTotal + $roomTotal;

// ✅ net_total ও আবার recalculate করুন
$netTotal = $subTotal - $discountAmount;

// ✅ invoiceSummary UPDATE করুন (room এবং corrected totals সহ)
$invoiceSummary['room_total'] = $roomTotal;
$invoiceSummary['sub_total']  = $subTotal;  // ✅ override করুন
$invoiceSummary['net_total']  = $netTotal;  // ✅ override করুন

/* ===== Generate PDF ===== */
$pdf = Pdf::loadView(
    'pages.spot_booking.invoice_pdf',
    compact('bookings', 'services', 'invoiceSummary', 'terms', 'allFacilities', 'roomBookings')
)->setPaper('a4', 'portrait');

return $pdf->stream($invoice . '.pdf'); // ✅ return ছিল না!
    // return $pdf->download($invoice . '.pdf');
}





    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpotBooking $spotBooking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpotBooking $spotBooking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpotBooking $spotBooking)
    {
        //
    }


    public function services(SpotBooking $booking)
    {
        $services = session('additional_services', []);
        $total = $booking->total_price;

        return view('pages.spot_booking.services', compact('booking', 'services', 'total'));
    }

    // public function servicesSave(Request $request, SpotBooking $booking)
    // {
    //     foreach ($request->services as $service) {
    //         AdditionalServiceBooking::create([
    //             'spot_booking_id' => $booking->id,
    //             'service_id' => $service['service_id'],
    //             'quantity' => $service['quantity'],
    //             'price' => $service['price'],
    //         ]);
    //     }

    //     return redirect()->route('spot-bookings.index')
    //         ->with('message', 'Booking confirmed successfully!');
    // }


public function spotReport(Request $request)
{
    $startDate      = $request->start_date ?? Carbon::today()->subMonth()->format('Y-m-d');
    $endDate        = $request->end_date   ?? Carbon::today()->format('Y-m-d');
    $spotId         = $request->spot_id         ?? null;
    $customerId     = $request->customer_id     ?? null;
    $customerMobile = $request->customer_mobile ?? null;
    $nidNumber      = $request->nid_number      ?? null;
    $invoiceFilter  = $request->invoice         ?? null;

    $spots = DB::table('spots')->select('id', 'title')->orderBy('title')->get();

    $customers = DB::table('customers')
        ->select('id', 'customer_name', 'customer_mobile', 'nid_number')
        ->orderBy('customer_name')
        ->get();

    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('MAX(booking_date) as booking_date'),
            DB::raw('SUM(CASE WHEN spot_id IS NOT NULL THEN total_price ELSE 0 END) as raw_spot_total'),
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_persons ELSE 0 END) as total_persons'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_mobile) as customer_mobile'),
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent'),
            DB::raw('MAX(CASE WHEN spot_id IS NOT NULL THEN discount_amount ELSE 0 END) as discount_amount'),
            DB::raw('MAX(CASE WHEN spot_id IS NOT NULL THEN invoice_adjustment_discount ELSE 0 END) as invoice_adjustment_discount')
        )
        ->whereBetween('booking_date', [$startDate, $endDate]);

    if ($spotId)        { $spotTotals->where('spot_id', $spotId); }
    if ($invoiceFilter) { $spotTotals->where('invoice_number', 'like', "%{$invoiceFilter}%"); }
    if ($customerId)    { $spotTotals->where('customer_name', $customerId); }

    $spotTotals->groupBy('invoice_number');

    $packageTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total')
        )
        ->groupBy('invoice_number');

    $serviceTotals = DB::table('spot_booking_details')
        ->select(
            'invoice_number',
            DB::raw('SUM(total_price) as service_total')
        )
        ->groupBy('invoice_number');

    $roomTotals = DB::table('bookings')
        ->select(
            'booking_no as invoice_number',
            DB::raw('SUM(total_amount) as room_total')
        )
        ->groupBy('booking_no');

    $paidTotals = DB::table('finance_transactions')
        ->select(
            'invoice_no as invoice_number',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('type', 'SV')
        ->where('balance_type', 'Dr')
        ->whereNotNull('payment_type')
        ->groupBy('invoice_no');

    // ✅ একটাই query — duplicate নেই
    $report = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb',  fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($roomTotals,    'rb',  fn($j) => $j->on('sb.invoice_number', '=', 'rb.invoice_number'))
        ->leftJoinSub($paidTotals,    'ft',  fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->select(
            'sb.booking_date',
            'sb.invoice_number',
            'sb.customer_name',
            'sb.customer_mobile',
            'sb.total_persons',
            'sb.spot_discount_percent',
            'sb.discount_amount',
            'sb.invoice_adjustment_discount',
            DB::raw('(sb.raw_spot_total - (sb.raw_spot_total * sb.spot_discount_percent / 100)) as spot_total'),
            DB::raw('(sb.raw_spot_total * sb.spot_discount_percent / 100) as spot_discount_amount'),
            DB::raw('COALESCE(pb.package_total, 0)  as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            DB::raw('COALESCE(rb.room_total, 0)     as room_total'),
            DB::raw('
                (sb.raw_spot_total - (sb.raw_spot_total * sb.spot_discount_percent / 100))
                + COALESCE(pb.package_total, 0)
                + COALESCE(sbd.service_total, 0)
                + COALESCE(rb.room_total, 0)
                as sub_total
            '),
            DB::raw('
                (sb.raw_spot_total - (sb.raw_spot_total * sb.spot_discount_percent / 100))
                + COALESCE(pb.package_total, 0)
                + COALESCE(sbd.service_total, 0)
                + COALESCE(rb.room_total, 0)
                - sb.discount_amount
                - sb.invoice_adjustment_discount
                as grand_total
            '),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->orderBy('sb.invoice_number')
        ->get();

    if ($customerMobile || $nidNumber) {
        $matchedNames = DB::table('customers')
            ->when($customerMobile, fn($q) => $q->where('customer_mobile', 'like', "%{$customerMobile}%"))
            ->when($nidNumber,      fn($q) => $q->where('nid_number',      'like', "%{$nidNumber}%"))
            ->pluck('customer_name')
            ->toArray();

        $report = $report->whereIn('customer_name', $matchedNames)->values();
    }

    return view('pages.spot_booking.spot_booking_report', compact(
        'report', 'startDate', 'endDate',
        'spots', 'spotId',
        'customers', 'customerId',
        'customerMobile', 'nidNumber', 'invoiceFilter'
    ));
}
public function spotReportPdf(Request $request)
{
    $startDate      = $request->start_date ?? Carbon::today()->subMonth()->format('Y-m-d');
    $endDate        = $request->end_date   ?? Carbon::today()->format('Y-m-d');
    $spotId         = $request->spot_id         ?? null;
    $customerId     = $request->customer_id     ?? null;
    $customerMobile = $request->customer_mobile ?? null;
    $nidNumber      = $request->nid_number      ?? null;
    $invoiceFilter  = $request->invoice         ?? null;

    $spots     = DB::table('spots')->select('id', 'title')->orderBy('title')->get();
    $customers = DB::table('customers')
        ->select('id', 'customer_name', 'customer_mobile', 'nid_number')
        ->orderBy('customer_name')->get();

    $spotTotals = DB::table('spot_bookings')
        ->select(
            'invoice_number',
            DB::raw('MAX(booking_date)         as booking_date'),
            DB::raw('SUM(total_price)           as spot_total'),
            DB::raw('SUM(total_persons)         as total_persons'),
            DB::raw('MAX(customer_name)         as customer_name'),
            DB::raw('MAX(customer_mobile)       as customer_mobile'),
            DB::raw('MAX(status)                as status'),
            DB::raw('MAX(spot_discount_percent) as spot_discount_percent')
        )
        ->whereBetween('booking_date', [$startDate, $endDate]);

    if ($spotId)        { $spotTotals->where('spot_id', $spotId); }
    if ($invoiceFilter) { $spotTotals->where('invoice_number', 'like', "%{$invoiceFilter}%"); }
    if ($customerId)    { $spotTotals->where('customer_name', $customerId); }

    $spotTotals->groupBy('invoice_number');

    $packageTotals = DB::table('spot_bookings')
        ->select('invoice_number', DB::raw('SUM(CASE WHEN package_id IS NOT NULL THEN total_price ELSE 0 END) as package_total'))
        ->groupBy('invoice_number');

    $serviceTotals = DB::table('spot_booking_details')
        ->select('invoice_number', DB::raw('SUM(total_price) as service_total'))
        ->groupBy('invoice_number');

    $paidTotals = DB::table('finance_transactions')
        ->select('invoice_no as invoice_number', DB::raw('SUM(amount) as paid_amount'))
        ->where('type', 'SV')->where('balance_type', 'Dr')->whereNotNull('payment_type')
        ->groupBy('invoice_no');

    $report = DB::query()
        ->fromSub($spotTotals, 'sb')
        ->leftJoinSub($packageTotals, 'pb',  fn($j) => $j->on('sb.invoice_number', '=', 'pb.invoice_number'))
        ->leftJoinSub($serviceTotals, 'sbd', fn($j) => $j->on('sb.invoice_number', '=', 'sbd.invoice_number'))
        ->leftJoinSub($paidTotals,    'ft',  fn($j) => $j->on('sb.invoice_number', '=', 'ft.invoice_number'))
        ->select(
            'sb.booking_date', 'sb.invoice_number', 'sb.customer_name', 'sb.customer_mobile',
            'sb.total_persons', 'sb.spot_total',
            DB::raw('COALESCE(pb.package_total, 0)  as package_total'),
            DB::raw('COALESCE(sbd.service_total, 0) as service_total'),
            'sb.spot_discount_percent',
            DB::raw('
                (sb.spot_total - (sb.spot_total * sb.spot_discount_percent / 100))
                + COALESCE(pb.package_total, 0)
                + COALESCE(sbd.service_total, 0)
                as grand_total
            '),
            DB::raw('COALESCE(ft.paid_amount, 0) as paid_amount')
        )
        ->orderBy('sb.invoice_number')
        ->get();

    if ($customerMobile || $nidNumber) {
        $matchedNames = DB::table('customers')
            ->when($customerMobile, fn($q) => $q->where('customer_mobile', 'like', "%{$customerMobile}%"))
            ->when($nidNumber,      fn($q) => $q->where('nid_number',      'like', "%{$nidNumber}%"))
            ->pluck('customer_name')->toArray();

        $report = $report->whereIn('customer_name', $matchedNames)->values();
    }

    $settings = DB::table('company_settings')->first();

    $data = [
        'company_name'     => $settings->company_name     ?? 'Company Name',
        'company_logo_one' => $settings->company_logo_one ?? '',
        'start_date'       => \Carbon\Carbon::parse($startDate)->format('d M Y'),
        'end_date'         => \Carbon\Carbon::parse($endDate)->format('d M Y'),
    ];

    $pdf = Pdf::loadView('pages.spot_booking.spot_booking_report_pdf',
        compact('report', 'data', 'spots', 'customers', 'startDate', 'endDate'))
        ->setPaper('a4', 'potrait');

    return $pdf->stream('spot-booking-report.pdf');
}
public function checkAvailability(Request $request)
{
    if($request->view_type == 'room'){
        return redirect()->route('frontend.rooms', $request->only('check_in','check_out'));
    } elseif($request->view_type == 'zone'){
        return redirect()->route('spot.booking.1');
    }

    return back()->with('error','Please select view type.');
}
public function index3(Request $request)
{
    $zones = Spot::all();
    $company = CompanySetting::first();
    $gallery_footer = Gallery::orderBy('id', 'asc')->take(6)->get();

    $availability = [];

    if ($request->check_in && $request->check_out) {

    $start = Carbon::parse($request->check_in);
    $end   = Carbon::parse($request->check_out);

    foreach ($zones as $zone) {

        $bookings = SpotBooking::where('spot_id', $zone->id)
            ->whereBetween('booking_date', [$start, $end])
            ->where('status', 0)
            ->get();

        if($bookings->count() > 0){
            $dates = $bookings->pluck('booking_date')->map(function($d){
                return Carbon::parse($d)->format('d M Y');
            })->toArray();

            $availability[$zone->id] = [
                'status' => 'booked',
                'dates' => $dates, // multiple dates
            ];
        }else{
            $availability[$zone->id] = [
                'status' => 'available',
                'dates' => [],
            ];
        }
    }
}

    return view('pages.frontend.zone_availability',
        compact('zones','availability','company','gallery_footer')
    );
}
    // SpotBookingController.php
public function bookSpot(Request $request)
{
    $validated = $request->validate([
        'spot_id' => 'required|exists:spots,id',
        'customer_name' => 'required|string',
        'customer_mobile' => 'required|string',
        'booking_date' => 'required|date',
        // 'total_persons' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        // 🔥 Spot data
        $spot = Spot::findOrFail($validated['spot_id']);

        // 🔥 Invoice generate using sales_no
        $salesNo = DB::table('invoiceno')->first('sales_no');
        $getSalesNo = $salesNo->sales_no ?? 0;
        $newInvoiceNumber = 'INV' . str_pad($getSalesNo, 6, '0', STR_PAD_LEFT);

        // Update sales_no for next invoice
        DB::table('invoiceno')->update([
            'sales_no' => $getSalesNo + 1,
        ]);

        // ================= CUSTOMER & FINANCE ACCOUNT =================
        $financeAccount = FinanceAccount::updateOrCreate(
            ['account_name' => $validated['customer_name']],
            [
                'financegroup_id' => '7',
                'account_group_code' => $GLOBALS['CustomerGroupCode'],
                'account_mobile' => $validated['customer_mobile'],
                'account_address' => $request['customer_address'],
                'account_company_code' => '01',
                'account_status' => 1,
                'account_done_by' => auth()->user()->name,
            ]
        );

        // 🔥 Customer create/update
        $customer = Customer::updateOrCreate(
            ['ac_id' => $financeAccount->id],
            [
                'customer_type' => 1,
                'customer_name' => $validated['customer_name'],
                'customer_address' => $request['customer_address'],
                'customer_mobile' => $validated['customer_mobile'],
                'status' => 1,
                'done_by' => auth()->user()->name,
            ]
        );

        $customerId = $financeAccount->id;
        $customerName = $financeAccount->account_name;
        $customerMobile = $financeAccount->account_mobile ?? 'N/A';

        // 🔥 Booking create
        $booking = SpotBooking::create([
            'invoice_number'   => $newInvoiceNumber,
            'spot_id'          => $spot->id,
            'customer_name'    => $customerName,
            'customer_mobile'  => $customerMobile,
            'booking_date'     => $validated['booking_date'],
            'total_persons'    => $spot->max_capacity,
            'total_price'      => $spot->price,
            'status'           => 0,
            'discount_percent' => 0,
            'spot_discount_percent' => 0,
            'discount_amount' => 0,
            'Booking_status' => 1,
            'check_in_datetime' => '10:59:59',
            'check_out_datetime' => '10:59:59',
            'invoice_adjustment_discount' => 0,
        ]);

        // ================= FINANCE TRANSACTIONS =================
        $done_by = auth()->user()->name;
        $netTotalAmount = $spot->price;

        // Voucher
        $voucher = DB::table('invoiceno')->first('voucher_no');
        $voucherNo = '01SV' . str_pad($voucher->voucher_no, 6, '0', STR_PAD_LEFT);

        DB::table('invoiceno')->update([
            'voucher_no' => $voucher->voucher_no + 1
        ]);

        // Sales Account Name
        $salesAccountName = DB::table('finance_accounts')
            ->where('id', $GLOBALS['SalesAccountID'])
            ->value('account_name') ?? 'Sales Account';

        // ================= Insert Dr =================
        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $newInvoiceNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $validated['booking_date'],
            'acid' => $customerId,
            'to_acc_name' => $salesAccountName, // Cr account name
            'type' => 'SV',
            'amount' => $netTotalAmount,
            'balance_type' => 'Dr',
            'transaction_date' => $validated['booking_date'],
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        // ================= Insert Cr =================
        FinanceTransaction::create([
            'company_code' => '01',
            'invoice_no' => $newInvoiceNumber,
            'voucher_no' => $voucherNo,
            'voucher_date' => $validated['booking_date'],
            'acid' => $GLOBALS['SalesAccountID'],
            'to_acc_name' => $customerName, // Dr account name
            'type' => 'SV',
            'amount' => $netTotalAmount,
            'balance_type' => 'Cr',
            'transaction_date' => $validated['booking_date'],
            'transaction_by' => $done_by,
            'done_by' => $done_by,
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Booking successful!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
}
