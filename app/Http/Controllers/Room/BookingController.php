<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\FinanceAccount;
use App\Models\BookingGuest;
use App\Models\CustomerType;
use App\Models\PaymentDetail;
use App\Models\FinanceTransaction;
use App\Models\Room;
use App\Models\TermsCondition;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
// BookingController.php
use PDF; // যদি mpdf/dompdf/laravel-dompdf ব্যবহার করো
class BookingController extends Controller
{
public function index(Request $request)
{
    $query = Booking::query()
        ->leftJoin('customers as c1', 'bookings.customer_id', '=', 'c1.id')
        ->leftJoin('customers as c2', 'bookings.customer_id', '=', 'c2.ac_id')
        ->select(
            'bookings.*',
            DB::raw('COALESCE(c1.customer_name, c2.customer_name) as customer_name'),
            DB::raw('COALESCE(c1.customer_mobile, c2.customer_mobile) as customer_mobile')
        );

    // =========================
    // FILTERS
    // =========================

    // Date range — এখন created_at (কবে বুকিং/ইনভয়েস তৈরি হয়েছে) এর উপর ভিত্তি করে
    if ($request->filled('start_date') && $request->filled('end_date')) {

        $start = $request->start_date;
        $end   = $request->end_date;

        $query->whereDate('bookings.created_at', '>=', $start)
              ->whereDate('bookings.created_at', '<=', $end);

    } elseif (
        !$request->filled('start_date') &&
        !$request->filled('end_date') &&
        !$request->filled('booking_no') &&
        !$request->filled('nid') &&
        $request->status === null
    ) {
        // কোনো ফিল্টার দেওয়া হয়নি — ডিফল্টে আজকের তৈরি হওয়া বুকিং/ইনভয়েস দেখাবে
        $query->whereDate('bookings.created_at', now()->toDateString());
    }

    // Booking No
    if ($request->filled('booking_no')) {
        $query->where('booking_no', 'like', '%' . $request->booking_no . '%');
    }

    // Customer NID
    if ($request->filled('nid')) {
        $query->whereHas('customer', function ($q) use ($request) {
            $q->where('nid_number', 'like', '%' . $request->nid . '%');
        });
    }

    // Status
    if ($request->status !== null && $request->status !== '') {
        $query->where('Booking_status', $request->status);
    }

    // =========================
    // GET DATA
    // =========================

    $bookings = $query
        ->orderBy('booking_no', 'desc')
        ->get()
        ->groupBy('booking_no');

    // payments
    $payments = DB::table('payment_details')
        ->select('booking_no', DB::raw('SUM(amount) as paid_amount'))
        ->groupBy('booking_no')
        ->pluck('paid_amount', 'booking_no');

    return view('pages.room.booking.index', compact('bookings', 'payments'));
}

public function indexPdf(Request $request)
{
    $query = Booking::query()
        ->leftJoin('customers as c1', 'bookings.customer_id', '=', 'c1.id')
        ->leftJoin('customers as c2', 'bookings.customer_id', '=', 'c2.ac_id')
        ->select(
            'bookings.*',
            DB::raw('COALESCE(c1.customer_name, c2.customer_name) as customer_name'),
            DB::raw('COALESCE(c1.customer_mobile, c2.customer_mobile) as customer_mobile')
        );
 
    // =========================
    // FILTERS (same logic as index())
    // =========================
 
    if ($request->filled('start_date') && $request->filled('end_date')) {
 
        $start = $request->start_date;
        $end   = $request->end_date;
 
        $query->whereDate('bookings.created_at', '>=', $start)
              ->whereDate('bookings.created_at', '<=', $end);
 
    } elseif (
        !$request->filled('start_date') &&
        !$request->filled('end_date') &&
        !$request->filled('booking_no') &&
        !$request->filled('nid') &&
        $request->status === null
    ) {
        $query->whereDate('bookings.created_at', now()->toDateString());
    }
 
    if ($request->filled('booking_no')) {
        $query->where('booking_no', 'like', '%' . $request->booking_no . '%');
    }
 
    if ($request->filled('nid')) {
        $query->whereHas('customer', function ($q) use ($request) {
            $q->where('nid_number', 'like', '%' . $request->nid . '%');
        });
    }
 
    if ($request->status !== null && $request->status !== '') {
        $query->where('Booking_status', $request->status);
    }
 
    // =========================
    // GET DATA
    // =========================
 
    $bookings = $query
        ->orderBy('booking_no', 'desc')
        ->get()
        ->groupBy('booking_no');
 
    $payments = DB::table('payment_details')
        ->select('booking_no', DB::raw('SUM(amount) as paid_amount'))
        ->groupBy('booking_no')
        ->pluck('paid_amount', 'booking_no');
 
    $company = DB::table('company_settings')->first();
 
    $data = [
        'company_name'     => $company->company_name ?? config('app.name'),
        'company_logo_one' => $company->company_logo_one,
        'start_date'       => $request->filled('start_date')
            ? Carbon\Carbon::parse($request->start_date)->format('d M Y')
            : ($request->filled('end_date') || $request->filled('booking_no') || $request->filled('nid') || $request->status !== null
                ? '—'
                : now()->format('d M Y')),
        'end_date'         => $request->filled('end_date')
            ? Carbon\Carbon::parse($request->end_date)->format('d M Y')
            : ($request->filled('start_date') || $request->filled('booking_no') || $request->filled('nid') || $request->status !== null
                ? '—'
                : now()->format('d M Y')),
    ];
 
    $pdf = Pdf::loadView(
        'pages.room.booking.booking_list_pdf',
        compact('data', 'bookings', 'payments')
    )->setPaper('a4', 'portrait');
 
    return $pdf->stream('booking-list.pdf');
}


// BookingController.php — bookingDetailsReport method

public function bookingDetailsReport(Request $request)
{
    $query = Booking::query()
        ->leftJoin('customers as c1', 'bookings.customer_id', '=', 'c1.id')
        ->leftJoin('customers as c2', 'bookings.customer_id', '=', 'c2.ac_id')
        ->select(
            'bookings.*',
            DB::raw('COALESCE(c1.customer_name, c2.customer_name) as customer_name'),
            DB::raw('COALESCE(c1.customer_mobile, c2.customer_mobile) as customer_mobile')
        )
        ->where('bookings.Booking_status', '!=', 2)
        ->orderBy('bookings.created_at', 'desc');
 
    // ── Customer filter (by ID from select dropdown) ──
    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }
 
    // ── Customer Mobile filter ──
    if ($request->filled('customer_mobile')) {
        $query->whereHas('customer', fn($c) =>
            $c->where('customer_mobile', 'like', '%'.$request->customer_mobile.'%')
        );
    }
 
    // ── NID filter ──
    if ($request->filled('nid_number')) {
        $query->whereHas('customer', fn($c) =>
            $c->where('nid_number', 'like', '%'.$request->nid_number.'%')
        );
    }
 
    // ── Room filter ──
    if ($request->filled('room_id')) {
        $query->where('room_id', $request->room_id);
    }
 
    // ── Booking status (1=Checked In, 2=Checked Out, 3=Cancelled) ──
    if ($request->filled('booking_status')) {
        $query->where('Booking_status', $request->booking_status);
    }
 
    // ── Payment status filter (needs cross-reference with payments) ──
    if ($request->filled('pay_status')) {
        $query->whereIn('booking_no', function ($sub) use ($request) {
            $sub->select('booking_no')
                ->from('payment_details')
                ->groupBy('booking_no')
                ->havingRaw(match($request->pay_status) {
                    'paid'    => 'SUM(amount) >= (SELECT SUM(total_amount - discount) FROM bookings b2 WHERE b2.booking_no = booking_no)',
                    'unpaid'  => 'SUM(amount) = 0',
                    'partial' => 'SUM(amount) > 0 AND SUM(amount) < (SELECT SUM(total_amount - discount) FROM bookings b2 WHERE b2.booking_no = booking_no)',
                    default   => '1=1',
                });
        });
    }
 
    // ── Date range — now based on created_at (when the booking/invoice was made) ──
    $hasOtherFilters = $request->filled('customer_id')
        || $request->filled('customer_mobile')
        || $request->filled('nid_number')
        || $request->filled('room_id')
        || $request->filled('booking_status')
        || $request->filled('pay_status');
 
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereDate('bookings.created_at', '>=', $request->date_from)
              ->whereDate('bookings.created_at', '<=', $request->date_to);
    } elseif (!$hasOtherFilters && !$request->filled('date_from') && !$request->filled('date_to')) {
        // Nothing filtered at all (first page load) — default to TODAY's created bookings
        $query->whereDate('bookings.created_at', now()->toDateString());
    }
 
    // ── Fetch all matching rows (no pagination — matches Sales Details Report style) ──
    $allRows = $query->get();
 
    $filteredBookingNos = $allRows->pluck('booking_no')->unique()->values()->toArray();
 
    // Exclude spot-booking invoices (INV...) from the payment lookup, same as before
    $filteredBookingNos = array_filter($filteredBookingNos, fn($no) => !str_starts_with($no, 'INV'));
 
    $allPayments = DB::table('payment_details')
    ->select('booking_no', DB::raw('SUM(amount) as paid_amount'))
    ->whereIn('booking_no', $filteredBookingNos)
    ->groupBy('booking_no')
    ->pluck('paid_amount', 'booking_no');
 
    $totalPaid = $allPayments->sum();
 
    $filteredRows = $allRows->filter(fn($b) => !str_starts_with($b->booking_no, 'INV'));
 
    $totalAmount   = $filteredRows->sum('total_amount');
    $totalDiscount = $filteredRows
        ->groupBy('booking_no')
        ->sum(fn($group) => $group->first()->discount ?? 0);
    $netTotal      = $totalAmount - $totalDiscount;
    $totalDue      = $netTotal - $totalPaid;
 
    $summary = [
        'total_bookings' => $allRows->pluck('booking_no')->unique()->count(),
        'total_amount'   => $totalAmount,
        'total_discount' => $totalDiscount,
        'net_total'      => $netTotal,
        'total_paid'     => $totalPaid,
        'total_due'      => $totalDue,
    ];
 
    // Pass the full collection straight to the view — the Blade groups it by
    // created_at date (and then by booking_no) itself, same pattern as the
    // Sales Details Report.
    $bookings = $allRows;
    $payments = $allPayments;
 
    $rooms = \App\Models\Room::orderBy('room_number')->get();
 
    $customers = \App\Models\Customer::orderBy('customer_name')
        ->select('id', 'customer_name', 'customer_mobile')
        ->get();
 
    return view('pages.room.booking.details_report', compact(
        'bookings', 'payments', 'rooms', 'customers', 'summary'
    ));
}


public function bookingDetailsReportPdf(Request $request)
{
    $query = Booking::with(['customer', 'room'])
    ->where('bookings.Booking_status', '!=', 2)
    ->orderBy('bookings.created_at', 'desc');
 
    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }
 
    if ($request->filled('customer_mobile')) {
        $query->whereHas('customer', fn($c) =>
            $c->where('customer_mobile', 'like', '%'.$request->customer_mobile.'%')
        );
    }
 
    if ($request->filled('nid_number')) {
        $query->whereHas('customer', fn($c) =>
            $c->where('nid_number', 'like', '%'.$request->nid_number.'%')
        );
    }
 
    if ($request->filled('room_id')) {
        $query->where('room_id', $request->room_id);
    }
 
    if ($request->filled('booking_status')) {
        $query->where('Booking_status', $request->booking_status);
    }
 
    // ── Date range — now based on created_at (booking/invoice creation date) ──
    $hasOtherFilters = $request->filled('customer_id')
        || $request->filled('customer_mobile')
        || $request->filled('nid_number')
        || $request->filled('room_id')
        || $request->filled('booking_status')
        || $request->filled('pay_status');
 
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereDate('bookings.created_at', '>=', $request->date_from)
              ->whereDate('bookings.created_at', '<=', $request->date_to);
    } elseif (!$hasOtherFilters && !$request->filled('date_from') && !$request->filled('date_to')) {
        // Nothing filtered at all — default to TODAY's created bookings, same as the web report
        $query->whereDate('bookings.created_at', now()->toDateString());
    }
 
    // Get ALL rows
    $bookings = $query->get();
 
    // ✅ payment_details টেবিল থেকে paid amount আনো — Booking List পেজের সাথে সামঞ্জস্যপূর্ণ,
    // finance_transactions এর কড়া শর্ত (narration/to_acc_name/balance_type) আর ব্যবহার হচ্ছে না,
    // যেটা আগে কিছু পেমেন্ট মিস করছিল (যেমন BO000333)।
    $filteredBookingNos = $bookings->pluck('booking_no')->unique()->values()->toArray();
    $filteredBookingNos = array_filter($filteredBookingNos, fn($no) => !str_starts_with($no, 'INV'));
 
    $allPayments = DB::table('payment_details')
        ->select('booking_no', DB::raw('SUM(amount) as paid_amount'))
        ->whereIn('booking_no', $filteredBookingNos)
        ->groupBy('booking_no')
        ->pluck('paid_amount', 'booking_no');
 
    $payments = $allPayments;
 
    // ✅ Pay status filter
    if ($request->filled('pay_status')) {
        $netPerBooking = $bookings->groupBy('booking_no')->map(function ($group) {
            return [
                'total_amount' => $group->sum('total_amount'),
                'discount'     => $group->first()->discount ?? 0,
            ];
        });
 
        $bookings = $bookings->filter(function ($booking) use ($allPayments, $netPerBooking, $request) {
            $net  = ($netPerBooking[$booking->booking_no]['total_amount'] ?? 0)
                  - ($netPerBooking[$booking->booking_no]['discount'] ?? 0);
            $paid = $allPayments[$booking->booking_no] ?? 0;
 
            return match($request->pay_status) {
                'paid'    => $paid >= $net,
                'unpaid'  => $paid <= 0,
                'partial' => $paid > 0 && $paid < $net,
                default   => true,
            };
        })->values();
    }
 
    // ✅ INV বাদ দিয়ে filter করা rows
    $filteredRows = $bookings->filter(fn($b) => !str_starts_with($b->booking_no, 'INV'));
 
    // ✅ Summary — same booking_no এর multiple row এর জন্য groupBy
    $grouped = $filteredRows->groupBy('booking_no');
 
    $totalAmount   = $grouped->sum(fn($group) => $group->sum('total_amount'));
    $totalDiscount = $grouped->sum(fn($group) => $group->first()->discount ?? 0);
    $netTotal      = $totalAmount - $totalDiscount;
 
    $filteredNos = $filteredRows->pluck('booking_no')->unique()->values()->toArray();
    $totalPaid   = $allPayments->only($filteredNos)->sum();
 
    $summary = [
        'total_bookings' => $grouped->count(),
        'total_amount'   => $totalAmount,
        'total_discount' => $totalDiscount,
        'net_total'      => $netTotal,
        'total_paid'     => $totalPaid,
        'total_due'      => max(0, $netTotal - $totalPaid),
    ];
 
    $rooms     = \App\Models\Room::orderBy('room_number')->get();
    $customers = \App\Models\Customer::orderBy('customer_name')
        ->select('id', 'customer_name', 'customer_mobile')
        ->get();
 
    // Filter section header dates now reflect the created_at range actually applied,
    // including the "today" default when nothing was submitted.
    $startDate = $request->date_from ?: ($hasOtherFilters ? null : now()->toDateString());
    $endDate   = $request->date_to   ?: ($hasOtherFilters ? null : now()->toDateString());
 
    $setting = \App\Models\CompanySetting::first();
    $data = [
        'company_name'     => $setting->company_name     ?? config('app.name'),
        'company_logo_one' => $setting->company_logo_one ?? '',
        'start_date'       => $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : '—',
        'end_date'         => $endDate   ? \Carbon\Carbon::parse($endDate)->format('d M Y')   : '—',
    ];
 
    $pdf = Pdf::loadView('pages.room.booking.booking_report_pdf', compact(
        'bookings', 'payments', 'rooms', 'customers', 'summary', 'data'
    ))
    ->setPaper('a4', 'portrait')
    ->setOptions([
        'defaultFont'          => 'DejaVu Sans',
        'isRemoteEnabled'      => false,
        'isHtml5ParserEnabled' => true,
        'dpi'                  => 120,
    ]);
 
    return $pdf->stream('booking-report-' . now()->format('Ymd-His') . '.pdf');
}
 
    public function create()
    {
        // Return a view for creating a new room
        // $allRoomType = RoomType::pluck('type_name', 'id')->all();
        // $customer_mobile='01913865989';
        // $customer = Customer::where('customer_mobile', $customer_mobile)->first();
        // dd($customer);
        // $startDate='2024-01-09';
        // $endDate='2024-01-09';
        // $bookingSearch = DB::connection()->select("CALL sp_GetBookingCheck(?, ?)", array($startDate, $endDate));
        // dd($bookingSearch);
        $room = Room::where('status', 1)->orderBy('room_order', 'asc')->get();
        // dd($room);
        // return view('pages.room.booking.add',compact('room'));
        return view('pages.room.booking.booking_room_list', compact('room'));
    }
    public function multipleBookingRoomList()
    {
        
        $room          = Room::where('status', 1)->orderBy('room_order', 'asc')->get();
        
        $customerTypes = CustomerType::where('status', 1)->pluck('type_name', 'id')->all();
        // dd($room);
        return view('pages.room.booking.multiple_booking_room_list', compact('room', 'customerTypes'));
    }
public function create2(Request $request)
{
    $rooms = $request->input('rooms', []); // array of rooms with id, date, start, end
  $room = Room::where('status', 1)->orderBy('room_order', 'asc')->get();
    // Fetch room_number for each room
    foreach ($rooms as $roomId => &$info) {
        $room = \App\Models\Room::find($roomId);
        if ($room) {
            $info['room_number'] = $room->room_number;
        } else {
            $info['room_number'] = $roomId; // fallback
        }
    }
    unset($info); // avoid reference issue

    $customerTypes = CustomerType::where('status', 1)
                        ->pluck('type_name', 'id');

    return view('pages.room.booking.add', [
        'rooms' => $rooms,
        'room' => $room,
        'customerTypes' => $customerTypes
    ]);
}

// public function searchCustomer(Request $request)
// {
//     $query = $request->get('query'); // ✅ FIX

//     $customer = Customer::where('customer_mobile', $query)
//         ->orWhere('nid_number', $query)
//         ->first();

//     return response()->json($customer);
// }

// public function searchGuest(Request $request)
// {
//     $query = $request->query('query');

//     if (!$query) return response()->json(null);

//     $guest = DB::table('booking_guests')
//         ->where('mobile', $query)
//         ->orWhere('nid', $query)
//         ->first();

//     return response()->json($guest);
// }



public function searchPerson(Request $request)
{
    $query = $request->query('query');

    if (!$query) return response()->json(null);

    // ======================
    // CUSTOMER SEARCH
    // ======================
    $customer = Customer::where('nid_number', $query)
        ->orWhere('customer_mobile', $query)
        ->first();

    // ======================
    // BOOKING GUEST SEARCH
    // ======================
    $guest = BookingGuest::where('nid', $query)
        ->orWhere('mobile', $query)
        ->latest() // latest guest priority
        ->first();

    // ======================
    // PRIORITY LOGIC
    // ======================
    if ($customer && $guest) {
        return response()->json([
            'source' => 'both',
            'customer' => $customer,
            'guest' => $guest
        ]);
    }

    if ($customer) {
        return response()->json([
            'source' => 'customer',
            'data' => $customer
        ]);
    }

    if ($guest) {
        return response()->json([
            'source' => 'guest',
            'data' => $guest
        ]);
    }

    return response()->json(null);
}

//  public function bookingSearch($startDate, $endDate)
//     {
//         $bookingSearch = DB::connection()->select("CALL sp_GetBookingCheck_2(?, ?)", [$startDate, $endDate]);
//         // dd($bookingSearch);
//         // return view('pages.room.booking.booking_room_list', compact('bookingSearch'));
//         return response()->json($bookingSearch);
//     }

public function bookingSearch($startDate, $endDate)
{
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate   = date('Y-m-d', strtotime($endDate));

    // Generate date range
    $dates = [];
    $current = strtotime($startDate);
    $end = strtotime($endDate);
    while ($current <= $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    // Get all rooms
    $rooms = DB::table('rooms')->orderBy('room_order', 'asc')->get();

    // Get all bookings in the range
    $bookings = DB::table('bookings')
        ->where('Booking_status', '!=', 2)
        ->where(function($q) use ($startDate, $endDate) {
            $q->where(function($qb) use ($startDate, $endDate) {
                // Day-wise bookings
                $qb->where('total_days', '>', 0)
                   ->where('check_in_date', '<=', $endDate)
                   ->where('check_out_date', '>=', $startDate);
            })->orWhere(function($qb) use ($startDate, $endDate) {
                // Hourly bookings: only within date range
                $qb->where('total_days', '=', 0)
                   ->where('check_in_date', '>=', $startDate)
                   ->where('check_in_date', '<=', $endDate);
            });
        })->get();

    $result = [];

    foreach ($dates as $date) {
        foreach ($rooms as $room) {
            // Default: available
            $isBooked = false;

            // Check each booking for this room
            foreach ($bookings as $b) {
                if ($b->room_id != $room->id) continue;

                // Hourly booking: only mark booked if current datetime overlaps
               if ((int)$b->total_days === 0 && $b->check_in_date == $date) {

    $today = date('Y-m-d');

    // 🔥 CASE 1: FUTURE DATE → শুধু booked দেখাও
    if ($date > $today) {
        $isBooked = true;
        break;
    }

    // 🔥 CASE 2: TODAY → time overlap check
    if ($date == $today) {
        $checkIn  = strtotime($date . ' ' . $b->check_in_datetime);
        $checkOut = strtotime($date . ' ' . $b->check_out_datetime);
        $now      = time();

        if ($now >= $checkIn && $now <= $checkOut) {
            $isBooked = true;
            break;
        }
    }

    // 🔥 CASE 3: PAST DATE (optional)
    if ($date < $today) {
        $isBooked = true; // or false based on your need
        break;
    }
}

                // Day-wise booking: full date range
                if ((int)$b->total_days > 0) {
    $checkIn  = strtotime($b->check_in_date . ' 00:00:00');

    // শুধুমাত্র total_days অনুযায়ী দিনগুলো block হবে
    $checkOut = strtotime($b->check_in_date . ' +' . ($b->total_days - 1) . ' days 23:59:59');

    $dayStart = strtotime($date . ' 00:00:00');
    $dayEnd   = strtotime($date . ' 23:59:59');

    if (!($checkOut < $dayStart || $checkIn > $dayEnd)) {
        $isBooked = true;
        break;
    }
}

            }

            // Get first image
            $imagePath = DB::table('room_details')
                ->where('room_id', $room->id)
                ->orderBy('id')
                ->value('image_path');

            $result[] = [
                'date'           => $date,
                'room_id'        => $room->id,
                'room_number'    => $room->room_number,
                'floor'          => $room->floor,
                'image_path'     => $imagePath,
                'is_booked'      => $isBooked ? 'Booked' : 'Available',
                'price_per_night'=> $room->price_per_night ?? 0,
            ];
        }
    }

    return response()->json($result);
}


    public function roomBookingSearch($id, $startDate, $endDate)
    {
        $roomBookingSearch = DB::connection()->select("CALL sp_GetRoomAvailability(?, ?, ?)", [$id, $startDate, $endDate]);
        return response()->json($roomBookingSearch);
    }

    // public function bookingSearch(Request $request)
    // {
    //     $startDate = $request->query('startDate');
    //     $endDate = $request->query('endDate');
    //     // Validate the input dates
    //     if (!$startDate || !$endDate) {
    //         return redirect()->back()->withErrors(['error' => 'Both start and end dates are required.']);
    //     }
    //     if (!strtotime($startDate) || !strtotime($endDate)) {
    //         return redirect()->back()->withErrors(['error' => 'Invalid date format.']);
    //     }
    //     // Fetch bookings within the date range
    //     $bookingSearch = DB::connection()->select("CALL sp_GetBookingCheck(?, ?)", array($startDate, $endDate));
    //     // Return the results
    //     return view('pages.room.booking.booking_room_list', compact('bookingSearch'));
    // }
    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
        // dd($request);
    $request->validate([
        'rooms' => 'required|array',
        'rooms.*.id' => 'required|exists:rooms,id',
        'rooms.*.date' => 'required|date',
        'rooms.*.start' => 'required',
        'rooms.*.end' => 'required',
        'customer_name' => 'required|string|max:255',
        'customer_address' => 'required|string|max:255',
        'customer_mobile' => 'required|string|max:15',
        'total_discount' => 'nullable|numeric|min:0',
        'after_discount' => 'nullable|numeric|min:0',
        'total_paid' => 'nullable|numeric|min:0',
        
    ]);

    /* ===============================
       Customer
    =============================== */
  
 $financeAccount = FinanceAccount::updateOrCreate(
    [
        'account_mobile' => $request->customer_mobile
    ],
    [
        'financegroup_id'      => '7',
        'account_group_code'   => $GLOBALS['CustomerGroupCode'] ?? null,
        'account_name'         => $request->customer_name,
        'account_mobile'       => $request->customer_mobile,
        'account_email'        => $request->customer_email,
        'account_address'      => $request->customer_address,
        'account_company_code' => '01',
        'account_status'       => 1,
        'account_done_by'      => auth()->user()?->name,
    ]
);

/* 🔥 IMPORTANT: always use finance account id */
$customer = Customer::updateOrCreate(
    [
        'customer_mobile' => $request->customer_mobile
    ],
    [
        'customer_type'    => 1,
        'customer_name'    => $request->customer_name,
        'customer_gender'  => $request->customer_gender,
        'customer_DOB'     => $request->customer_DOB,
        'customer_mobile'  => $request->customer_mobile,
        'customer_email'   => $request->customer_email,
        'nid_number'       => $request->customer_nid,
        'vat_reg_no'       => $request->vat_reg_no,
        'tin_no'           => $request->tin_no,
        'trade_license'    => $request->trade_license,
        'discount_rate'    => $request->discount_rate,
        'security_deposit' => $request->security_deposit,
        'credit_limit'     => $request->credit_limit,
        'customer_area'    => $request->customer_area,
        'customer_address' => $request->customer_address,
        'shipping_address' => $request->shipping_address,
        'shipping_contact' => $request->shipping_contact,
        'status'           => $request->status ?? 1,
        'done_by'          => auth()->user()?->name,

        // 🔥 KEY LINE (sync relationship)
        'ac_id'            => $financeAccount->id,
    ]
);

        
    /* ===============================
       Booking Number
    =============================== */
    $invoice = DB::table('invoiceno')->lockForUpdate()->first();
    $bookingNumber = 'BO'.str_pad($invoice->booking_no, 6, '0', STR_PAD_LEFT);
    DB::table('invoiceno')->update([
        'booking_no' => $invoice->booking_no + 1
    ]);

    /* ===============================
   Add Customer as Guest (Self)
=============================== */
if ($request->has('as_guest')) {

    // duplicate check (same booking_no + nid/mobile)
    $exists = DB::table('booking_guests')
        ->where('booking_no', $bookingNumber)
        ->where(function($q) use ($request) {
            $q->where('nid', $request->customer_nid)
              ->orWhere('mobile', $request->customer_mobile);
        })
        ->exists();

    if (!$exists) {
        DB::table('booking_guests')->insert([
            'booking_no' => $bookingNumber,
            'name'       => $request->customer_name,
            'nid'        => $request->customer_nid,
            'mobile'     => $request->customer_mobile,
            'address'    => $request->customer_address,
            'relation'   => 'Self',
            'customer_status'   => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
    /* ===============================
   Save Guests
=============================== */
if ($request->has('guests')) {
    foreach ($request->guests as $guest) {
        DB::table('booking_guests')->insert([
            'booking_no' => $bookingNumber,
            'name'       => $guest['name'] ?? null,
            'nid'        => $guest['nid'] ?? null,
            'mobile'     => $guest['mobile'] ?? null,
            'address'    => $guest['address'] ?? null,
            'relation'    => $guest['relation'] ?? null,
            'customer_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
    /* ===============================
       Totals
    =============================== */
    $netTotal = 0;
    foreach($request->rooms as $roomInfo) {
        $netTotal += $roomInfo['price_per_night'];
    }

    $discount = $request->total_discount ?? 0;
    $afterDiscount = $netTotal - $discount;
    $paid = $request->total_paid ?? 0;
    $due = $afterDiscount - $paid;
// dd($afterDiscount);
    /* ===============================
       Loop through rooms
    =============================== */
    foreach($request->rooms as $roomInfo) {

        $checkIn  = Carbon::parse($roomInfo['date'].' '.$roomInfo['start']);
        $checkOut = Carbon::parse($roomInfo['date'].' '.$roomInfo['end']);

        // Overlap protection
      $exists = Booking::where('room_id', $roomInfo['id'])
    ->whereDate('check_in_date', $roomInfo['date']) // ✅ FIX
    ->where(function ($q) use ($checkIn, $checkOut) {
        $q->where('check_in_datetime', '<', $checkOut)
          ->where('check_out_datetime', '>', $checkIn);
    })
    ->exists();

        if ($exists) {
            return back()->withErrors([
                'rooms' => 'Room '.$roomInfo['id'].' is already booked for selected time.'
            ])->withInput();
        }

        $room = Room::findOrFail($roomInfo['id']);

        // Hour calculation
        $totalHours = ceil($checkOut->diffInMinutes($checkIn) / 60);
        $totalAmount = $room->price_per_night;

        // Apply room-level discount if any
        $roomDiscount = $roomInfo['discount'] ?? 0;

        $total_discount = (float) $request->total_discount;
        $roomAmountAfterDiscount = $totalAmount - $roomDiscount- $total_discount;

        $paymentStatus = (($roomAmountAfterDiscount) <= ($paid / count($request->rooms))) ? 1 : 0;
// dd($paymentStatus);
        // Booking save
        $booking = Booking::create([
            'booking_no'         => $bookingNumber,
            'customer_id'        => $customer->id,
            'room_id'            => $room->id,
            'check_in_date'      => $roomInfo['date'],
            'check_out_date'     => $roomInfo['date'], // hourly, same day
            'check_in_datetime'  => $checkIn,
            'check_out_datetime' => $checkOut,
            'total_days'         => 0,
            'total_amount'       => $totalAmount,
            'discount'       => $discount,
            'payment_status'     => $paymentStatus,
            'Booking_status'     => 1,
            'status'             => 1,
        ]);

        // Payment detail per room
       
     

       
    }
    $financeAccount = FinanceAccount::where('account_mobile', $request->customer_mobile)->first();

$financeAccountId = $financeAccount->id;
$financeAccountName = $financeAccount->account_name;
$done_by = Auth::user()->name;
    $voucher = DB::table('invoiceno')->first('voucher_no');
            $getCrVoucherNo = $voucher->voucher_no;
            $voucherNo = '01SV' . str_pad($getCrVoucherNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update([
                'voucher_no' => $getCrVoucherNo + 1,
            ]);
     $financeTransaction = FinanceTransaction::create([
                'company_code' => '01',
                'invoice_no' => $bookingNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => now(),
                'acid' => $financeAccountId,
                'to_acc_name' => $GLOBALS['SalesAccountName'],
                'type' => 'SV',
                'amount' => $afterDiscount,
                'balance_type' => 'Dr',
                'transaction_date' => now(),
                'narration' => 'Room Booking',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);

            $financeTransaction2 = FinanceTransaction::create([
                'company_code' => '01',
                
                'invoice_no' => $bookingNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => now(),
                'acid' => $GLOBALS['SalesAccountID'],
                'to_acc_name' => $financeAccountName,
                'type' => 'SV',
                'amount' => $afterDiscount,
                'balance_type' => 'Cr',
                'transaction_date' => now(),
                'narration' => 'Room Booking',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);
       if ($paid > 0) {
            PaymentDetail::create([
                'booking_no' => $bookingNumber,
                'amount'     => $paid,
            ]);


            $financeTransaction3 = FinanceTransaction::create([
                    'company_code' => '01',
                    'invoice_no' => $bookingNumber,
                    'voucher_no' => $voucherNo,
                    'voucher_date' => now(),
                    'acid' => $financeAccountId,
                    'to_acc_name' => 'Cash',
                    'type' => 'SV',
                    'amount' => $paid,
                    'balance_type' => 'Cr',
                    'transaction_date' => now(),
                    'narration' => 'Room Booking Payment',
                    'transaction_by' => $done_by,
                    'done_by' => $done_by,
                ]);

                $financeTransaction4 = FinanceTransaction::create([
                    'company_code' => '01',
                    'invoice_no' => $bookingNumber,
                    'voucher_no' => $voucherNo,
                    'voucher_date' => now(),
                    'acid' => 11,
                    'to_acc_name' => $financeAccountName,
                    'type' => 'SV',
                    'amount' => $paid,
                    'balance_type' => 'Dr',
                    'transaction_date' => now(),
                    'narration' => 'Room Booking Payment',
                    'transaction_by' => $done_by,
                    'done_by' => $done_by,
                ]);
        }
    return redirect()->route('booking.create')->with([
        'message' => 'Bulk hourly booking successful!',
        'alert-type' => 'success'
    ]);
}
public function editHourly($booking_no)
{
    $bookingGroup = Booking::with([
            'customer',
            'room',
            'payments'
        ])
        ->where('booking_no', $booking_no)
        ->where('total_days', 0)
        ->get();

    // ❌ booking not found
    if ($bookingGroup->isEmpty()) {

        return redirect()
            ->back()
            ->with('error', 'Hourly booking not found');
    }

    // ✅ first booking
    $firstBooking = $bookingGroup->first();

    // ✅ all booking rooms
    $bookingRooms = $bookingGroup;

    // ✅ customer
    $customer = $firstBooking->customer;

    // ✅ paid amount
    $paid = DB::table('payment_details')
        ->where('booking_no', $booking_no)
        ->sum('amount');

    // ✅ guests
    $guests = DB::table('booking_guests')
        ->where('booking_no', $booking_no)
        ->get();

    // ✅ self guest
    $selfGuest = $guests->firstWhere('customer_status', 1);

    // ✅ current selected room ids
    $currentRoomIds = $bookingGroup
        ->pluck('room_id')
        ->toArray();

    // ✅ current date
    $today = now()->format('Y-m-d');

    // ✅ current time
    $currentTime = now()->format('H:i');

    // ✅ end time default
    $defaultEndTime = '23:59';

    // ✅ booking room formatted data
    $formattedRooms = $bookingRooms->map(function ($room) {

        return [

            'booking_id' => $room->id,

            'room_id' => $room->room_id,

            'room_number' => optional($room->room)->room_number,

            'date' => $room->check_in_date,

            'start' => substr($room->check_in_datetime, 0, 5),

            'end' => substr($room->check_out_datetime, 0, 5),

            'price' => $room->total_amount,
            'booked_from' => $room?->check_in_date,

'booked_to' => $room?->check_out_date,

'total_days' => $room?->total_days,

'booking_type' => $room
    ? ($room->total_days > 0 ? 'Day Booking' : 'Hourly Booking')
    : null,
        ];
    });

    return view('pages.room.booking.hourly_edit', compact(
        'bookingGroup',
        'firstBooking',
        'bookingRooms',
        'formattedRooms',
        'customer',
        'paid',
        'guests',
        'selfGuest',
        'currentRoomIds',
        'today',
        'currentTime',
        'defaultEndTime'
    ));
}
public function updateHourly(Request $request, $booking_no)
{
    DB::beginTransaction();

    try {

        /* ===============================
           Validation
        =============================== */
        $request->validate([
            'rooms' => 'required|array',
            'rooms.*.room_id' => 'required|exists:rooms,id',
            'rooms.*.date' => 'required|date',
            'rooms.*.start' => 'required',
            'rooms.*.end' => 'required',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:15',
        ]);

        /* ===============================
           Customer & Finance Account
        =============================== */
        $financeAccount = FinanceAccount::updateOrCreate(
            ['account_mobile' => $request->customer_mobile],
            [
                'financegroup_id'      => '7',
                'account_group_code'   => $GLOBALS['CustomerGroupCode'] ?? null,
                'account_name'         => $request->customer_name,
                'account_mobile'       => $request->customer_mobile,
                'account_email'        => $request->customer_email,
                'account_address'      => $request->customer_address,
                'account_company_code' => '01',
                'account_status'       => 1,
                'account_done_by'      => auth()->user()?->name,
            ]
        );

        $customer = Customer::updateOrCreate(
            ['customer_mobile' => $request->customer_mobile],
            [
                'customer_type'    => 1,
                'customer_name'    => $request->customer_name,
                'customer_gender'  => $request->customer_gender,
                'customer_DOB'     => $request->customer_DOB,
                'customer_mobile'  => $request->customer_mobile,
                'customer_email'   => $request->customer_email,
                'nid_number'       => $request->customer_nid,
                'vat_reg_no'       => $request->vat_reg_no,
                'tin_no'           => $request->tin_no,
                'trade_license'    => $request->trade_license,
                'discount_rate'    => $request->discount_rate,
                'security_deposit' => $request->security_deposit,
                'credit_limit'     => $request->credit_limit,
                'customer_area'    => $request->customer_area,
                'customer_address' => $request->customer_address,
                'shipping_address' => $request->shipping_address,
                'shipping_contact' => $request->shipping_contact,
                'status'           => $request->status ?? 1,
                'done_by'          => auth()->user()?->name,
                'ac_id'            => $financeAccount->id,
            ]
        );

        $financeAccountId   = $financeAccount->id;
        $financeAccountName = $financeAccount->account_name;
        $done_by            = Auth::user()->name;

        $discount = $request->total_discount ?? $request->discount ?? 0;
        $paid     = $request->total_paid    ?? $request->paid     ?? 0;

         
           
        /* ===============================
           Track Existing IDs + Grand Totals
        =============================== */
        $existingIds = [];

        // 🔥 সব রুম মিলিয়ে accumulate করার জন্য
        $grandRoomAmount   = 0;
        $grandRoomDiscount = 0;

        $total_discount = (float) $discount;

        /* ===============================
           LOOP ROOMS
        =============================== */
        foreach ($request->rooms as $roomInfo) {

            $checkIn  = Carbon::parse($roomInfo['date'].' '.$roomInfo['start']);
            $checkOut = Carbon::parse($roomInfo['date'].' '.$roomInfo['end']);

            /* ========= Overlap Check ========= */
            $query = Booking::where('room_id', $roomInfo['room_id'])
                ->where('total_days', 0)
                ->where('Booking_status', '!=', 2)
                ->whereDate('check_in_date', $roomInfo['date'])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in_datetime', '<', $checkOut)
                      ->where('check_out_datetime', '>', $checkIn);
                });

            if (!empty($roomInfo['booking_id'])) {
                $query->where('id', '!=', $roomInfo['booking_id']);
            }

            if ($query->exists()) {
                return back()->withErrors([
                    'rooms' => 'Room already booked for selected time'
                ]);
            }

            $room         = Room::findOrFail($roomInfo['room_id']);
            $roomAmount   = $room->price_per_night;
            $roomDiscount = $roomInfo['discount'] ?? 0;
            //  dd($roomDiscount);

            //  grand total এ যোগ করো (room ভিত্তিক)
            $grandRoomAmount   += $roomAmount;
            $grandRoomDiscount += $roomDiscount;

            $roomAmountAfterDiscount = $roomAmount - $roomDiscount;
            $paymentStatus = ($roomAmountAfterDiscount <= ($paid / count($request->rooms))) ? 1 : 0;

            /* ===============================
               UPDATE OR CREATE BOOKING
            =============================== */
            if (!empty($roomInfo['booking_id'])) {

                Booking::where('id', $roomInfo['booking_id'])->update([
                    'customer_id'        => $customer->id,
                    'room_id'            => $room->id,
                    'check_in_date'      => $roomInfo['date'],
                    'check_out_date'     => $roomInfo['date'],
                    'check_in_datetime'  => $checkIn,
                    'check_out_datetime' => $checkOut,
                    'total_amount'       => $roomAmount,
                    'discount'           => $total_discount,
                    'payment_status'     => $paymentStatus,
                    'Booking_status'     => 1,
                ]);

                $existingIds[] = $roomInfo['booking_id'];

            } else {

                $newBooking = Booking::create([
                    'booking_no'         => $booking_no,
                    'customer_id'        => $customer->id,
                    'room_id'            => $room->id,
                    'check_in_date'      => $roomInfo['date'],
                    'check_out_date'     => $roomInfo['date'],
                    'check_in_datetime'  => $checkIn,
                    'check_out_datetime' => $checkOut,
                    'total_amount'       => $roomAmount,
                    'discount'           => $total_discount,
                    'payment_status'     => $paymentStatus,
                    'Booking_status'     => 1,
                    'status'             => 1,
                    'total_days'         => 0,
                ]);

                $existingIds[] = $newBooking->id;
            }
        }

        /* ===============================
           DELETE REMOVED ROOMS
        =============================== */
        Booking::where('booking_no', $booking_no)
            ->whereNotIn('id', $existingIds)
            ->delete();

        /* ===============================
           Guests Reset
        =============================== */
        DB::table('booking_guests')
            ->where('booking_no', $booking_no)
            ->delete();

        if ($request->has('as_guest')) {
            DB::table('booking_guests')->insert([
                'booking_no'      => $booking_no,
                'name'            => $request->customer_name,
                'nid'             => $request->customer_nid,
                'mobile'          => $request->customer_mobile,
                'address'         => $request->customer_address,
                'relation'        => 'Self',
                'customer_status' => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        if ($request->guests) {
            foreach ($request->guests as $g) {
                DB::table('booking_guests')->insert([
                    'booking_no'      => $booking_no,
                    'name'            => $g['name']     ?? null,
                    'nid'             => $g['nid']      ?? null,
                    'mobile'          => $g['mobile']   ?? null,
                    'address'         => $g['address']  ?? null,
                    'relation'        => $g['relation'] ?? null,
                    'customer_status' => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        /* ===============================
           Payment Update
        =============================== */
        DB::table('payment_details')
            ->where('booking_no', $booking_no)
            ->delete();

        if ($paid > 0) {
            PaymentDetail::create([
                'booking_no' => $booking_no,
                'amount'     => $paid,
            ]);
        }

        /* ===============================
           Finance Transactions — Delete old, recreate
        =============================== */
        // 🔥 সব রুমের total amount - সব রুমের discount - overall invoice discount
        $grandTotalAfterDiscount = $grandRoomAmount - $grandRoomDiscount - $total_discount;

        // existing voucher_no এই booking এর জন্য
        $existingVoucher = DB::table('finance_transactions')
            ->where('invoice_no', $booking_no)
            ->where('type', 'SV')
            ->value('voucher_no');

        // যদি voucher না থাকে নতুন generate করো
        if (!$existingVoucher) {
            $voucher    = DB::table('invoiceno')->first('voucher_no');
            $getCrVoucherNo = $voucher->voucher_no;
            $existingVoucher = '01SV' . str_pad($getCrVoucherNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update([
                'voucher_no' => $getCrVoucherNo + 1,
            ]);
        }

        // পুরনো finance transactions delete
        FinanceTransaction::where('invoice_no', $booking_no)
            ->where('type', 'SV')
            ->delete();

        // SV1 — Customer Dr (booking amount, sum of ALL rooms)
        FinanceTransaction::create([
            'company_code'     => '01',
            'invoice_no'       => $booking_no,
            'voucher_no'       => $existingVoucher,
            'voucher_date'     => now(),
            'acid'             => $financeAccountId,
            'to_acc_name'      => $GLOBALS['SalesAccountName'],
            'type'             => 'SV',
            'amount'           => $grandTotalAfterDiscount,
            'balance_type'     => 'Dr',
            'transaction_date' => now(),
            'narration'        => 'Room Booking',
            'transaction_by'   => $done_by,
            'done_by'          => $done_by,
        ]);

        // SV2 — Sales Account Cr
        FinanceTransaction::create([
            'company_code'     => '01',
            'invoice_no'       => $booking_no,
            'voucher_no'       => $existingVoucher,
            'voucher_date'     => now(),
            'acid'             => $GLOBALS['SalesAccountID'],
            'to_acc_name'      => $financeAccountName,
            'type'             => 'SV',
            'amount'           => $grandTotalAfterDiscount,
            'balance_type'     => 'Cr',
            'transaction_date' => now(),
            'narration'        => 'Room Booking',
            'transaction_by'   => $done_by,
            'done_by'          => $done_by,
        ]);

        // SV3 & SV4 — Paid amount entries
        if ($paid > 0) {

            // Customer Cr (paid)
            FinanceTransaction::create([
                'company_code'     => '01',
                'invoice_no'       => $booking_no,
                'voucher_no'       => $existingVoucher,
                'voucher_date'     => now(),
                'acid'             => $financeAccountId,
                'to_acc_name'      => 'Cash',
                'type'             => 'SV',
                'amount'           => $paid,
                'balance_type'     => 'Cr',
                'transaction_date' => now(),
                'narration'        => 'Room Booking Payment',
                'transaction_by'   => $done_by,
                'done_by'          => $done_by,
            ]);

            // Cash Account Dr
            FinanceTransaction::create([
                'company_code'     => '01',
                'invoice_no'       => $booking_no,
                'voucher_no'       => $existingVoucher,
                'voucher_date'     => now(),
                'acid'             => 11,
                'to_acc_name'      => $financeAccountName,
                'type'             => 'SV',
                'amount'           => $paid,
                'balance_type'     => 'Dr',
                'transaction_date' => now(),
                'narration'        => 'Room Booking Payment',
                'transaction_by'   => $done_by,
                'done_by'          => $done_by,
            ]);
        }

        DB::commit();

        return redirect()->route('booking.index')
            ->with('success', 'Hourly booking updated successfully');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
    }
}

    public function multipleBookingStore(Request $request)
    {

        //  dd($request->all());

        $request->validate([
            'customer_name'        => 'required|string|max:255',
            'customer_mobile'      => 'required|string|max:15',
            'customer_address'     => 'required|string|max:500',
            'paid_amount'          => 'nullable|numeric',
            'discount'          => 'nullable|numeric',
            'table_room_id'        => 'required|array',
            'table_check_in_date'  => 'required|array',
            'table_check_out_date' => 'required|array',
            // 'payment_status' => 'required|integer',
        ]);

        // get sales_no
        $bookingNo     = DB::table('invoiceno')->first('booking_no');
        $getBookingNo  = $bookingNo->booking_no;
        $bookingNumber = 'BO' . str_pad($getBookingNo, 6, '0', STR_PAD_LEFT);
        DB::table('invoiceno')->update([
            'booking_no' => $getBookingNo + 1,
        ]);

        $financeAccount = FinanceAccount::updateOrCreate(
    [
        'account_mobile' => $request->customer_mobile
    ],
    [
        'financegroup_id'      => '7',
        'account_group_code'   => $GLOBALS['CustomerGroupCode'] ?? null,
        'account_name'         => $request->customer_name,
        'account_mobile'       => $request->customer_mobile,
        'account_email'        => $request->customer_email,
        'account_address'      => $request->customer_address,
        'account_company_code' => '01',
        'account_status'       => 1,
        'account_done_by'      => auth()->user()?->name,
    ]
);

/* 🔥 IMPORTANT: always use finance account id */
$customer = Customer::updateOrCreate(
    [
        'customer_mobile' => $request->customer_mobile
    ],
    [
        'customer_type'    => 1,
        'customer_name'    => $request->customer_name,
        'customer_gender'  => $request->customer_gender,
        'customer_DOB'     => $request->customer_DOB,
        'customer_mobile'  => $request->customer_mobile,
        'customer_email'   => $request->customer_email,
        'nid_number'       => $request->nid_number,
        'vat_reg_no'       => $request->vat_reg_no,
        'tin_no'           => $request->tin_no,
        'trade_license'    => $request->trade_license,
        'discount_rate'    => $request->discount_rate,
        'security_deposit' => $request->security_deposit,
        'credit_limit'     => $request->credit_limit,
        'customer_area'    => $request->customer_area,
        'customer_address' => $request->customer_address,
        'shipping_address' => $request->shipping_address,
        'shipping_contact' => $request->shipping_contact,
        'status'           => $request->status ?? 1,
        'done_by'          => auth()->user()?->name,

        // 🔥 KEY LINE (sync relationship)
        'ac_id'            => $financeAccount->id,
    ]
);

    /* ===============================
   Add Customer as Guest (Self)
=============================== */
if ($request->has('as_guest')) {

    // duplicate check (same booking_no + nid/mobile)
    $exists = DB::table('booking_guests')
        ->where('booking_no', $bookingNumber)
        ->where(function($q) use ($request) {
            $q->where('nid', $request->customer_nid)
              ->orWhere('mobile', $request->customer_mobile);
        })
        ->exists();

    if (!$exists) {
        DB::table('booking_guests')->insert([
            'booking_no' => $bookingNumber,
            'name'       => $request->customer_name,
            'nid'        => $request->customer_nid,
            'mobile'     => $request->customer_mobile,
            'address'    => $request->customer_address,
            'relation'   => 'Self',
            'customer_status'   => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
if ($request->has('guests')) {
    foreach ($request->guests as $guest) {
        DB::table('booking_guests')->insert([
            
            'booking_no' => $bookingNumber,
            'name'       => $guest['name'] ?? null,
            'nid'        => $guest['nid'] ?? null,
            'mobile'     => $guest['mobile'] ?? null,
            'address'    => $guest['address'] ?? null,
            'relation'    => $guest['relation'] ?? null,
            'customer_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
        $lastCustomerId = $customer->id;

        // dd($lastCustomerId);

       $grandTotal = 0;

if ($request->has('table_room_id') && is_array($request->input('table_room_id'))) {
    foreach ($request->input('table_room_id') as $key => $table_room_id) {
        $multipleBooking = new Booking();

        $room_id      = $request->input('table_room_id')[$key];
        $checkInDate  = Carbon::parse($request->input('table_check_in_date')[$key]);
        $checkOutDate = Carbon::parse($request->input('table_check_out_date')[$key]);

        $room      = Room::find($room_id);
        $room_rent = $room->price_per_night;

        $multipleBooking->room_id = $room_id;

        if ($checkInDate == $checkOutDate) {
            $multipleBooking->check_in_date  = date('Y-m-d', strtotime($checkInDate));
            $multipleBooking->check_out_date = date('Y-m-d', strtotime('+1 day', strtotime($checkOutDate)));
            $total_days = $checkOutDate->diffInDays($checkInDate) + 1;
        } else {
            $multipleBooking->check_in_date  = date('Y-m-d', strtotime($checkInDate));
            $multipleBooking->check_out_date = date('Y-m-d', strtotime('+1 day', strtotime($checkOutDate)));
            $total_days = $checkOutDate->diffInDays($checkInDate) + 1;
        }

        $total_amount = $room_rent * $total_days;

        $grandTotal += $total_amount; // ✅ প্রতিটা room এর amount যোগ হবে

        $multipleBooking->total_amount   = $total_amount;
        $multipleBooking->discount       = $request->discount;
        $multipleBooking->total_days     = $total_days;

        $paymentStatus                   = $request->paid_amount < $total_amount ? 0 : 1;
        $multipleBooking->payment_status = $paymentStatus;
        $multipleBooking->customer_id    = $lastCustomerId;
        $multipleBooking->booking_no     = $bookingNumber;
        $multipleBooking->Booking_status     = 1;
        $multipleBooking->check_in_datetime  = Carbon::parse($multipleBooking->check_in_date . ' 10:59:59');
        $multipleBooking->check_out_datetime = Carbon::parse($multipleBooking->check_out_date . ' 10:59:59');

        $multipleBooking->save();
    }
} else {
            // Handle error: no data passed for room bookings
            return back()->withErrors(['message' => 'No room bookings provided.']);
        }
          
        // $lastBookingId = $multipleBooking->id;

        $payment = PaymentDetail::create([
            'booking_no' => $bookingNumber,
            'amount'     => $request->paid_amount,
        ]);

            
            
            $financeAccount = FinanceAccount::where('account_mobile', $request->customer_mobile)->first();

            $financeAccountId = $financeAccount->id;
            $financeAccountName = $financeAccount->account_name;
            $done_by = Auth::user()->name;
                $voucher = DB::table('invoiceno')->first('voucher_no');
            $getCrVoucherNo = $voucher->voucher_no;
            $voucherNo = '01SV' . str_pad($getCrVoucherNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update([
                'voucher_no' => $getCrVoucherNo + 1,
            ]);
     $financeTransaction = FinanceTransaction::create([
                'company_code' => '01',
                'invoice_no' => $bookingNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => now(),
                'acid' => $financeAccountId,
                'to_acc_name' => $GLOBALS['SalesAccountName'],
                'type' => 'SV',
                'amount' => $grandTotal - $request->discount,
                'balance_type' => 'Dr',
                'transaction_date' => now(),
                'narration' => 'Room Booking',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);

            $financeTransaction2 = FinanceTransaction::create([
                'company_code' => '01',
                
                'invoice_no' => $bookingNumber,
                'voucher_no' => $voucherNo,
                'voucher_date' => now(),
                'acid' => $GLOBALS['SalesAccountID'],
                'to_acc_name' => $financeAccountName,
                'type' => 'SV',
                'amount' => $grandTotal - $request->discount,
                'balance_type' => 'Cr',
                'transaction_date' => now(),
                'narration' => 'Room Booking',
                'transaction_by' => $done_by,
                'done_by' => $done_by,
            ]);

            $paid = $request->paid_amount;

            if ($paid > 0) {


            $financeTransaction3 = FinanceTransaction::create([
                    'company_code' => '01',
                    'invoice_no' => $bookingNumber,
                    'voucher_no' => $voucherNo,
                    'voucher_date' => now(),
                    'acid' => $financeAccountId,
                    'to_acc_name' => 'Cash',
                    'type' => 'SV',
                    'amount' => $paid,
                    'balance_type' => 'Cr',
                    'transaction_date' => now(),
                    'narration' => 'Room Booking Payment',
                    'transaction_by' => $done_by,
                    'done_by' => $done_by,
                ]);

                $financeTransaction4 = FinanceTransaction::create([
                    'company_code' => '01',
                    'invoice_no' => $bookingNumber,
                    'voucher_no' => $voucherNo,
                    'voucher_date' => now(),
                    'acid' => 11,
                    'to_acc_name' => $financeAccountName,
                    'type' => 'SV',
                    'amount' => $paid,
                    'balance_type' => 'Dr',
                    'transaction_date' => now(),
                    'narration' => 'Room Booking Payment',
                    'transaction_by' => $done_by,
                    'done_by' => $done_by,
                ]);
        }

        return redirect()->route('multiple_booking')->with([
            'message'    => 'Successfully Booked!',
            'alert-type' => 'success',
        ]);
    }

  public function editBooking1($booking_no)
{
    
    $bookingGroup = Booking::with(['customer', 'room', 'payments'])
        ->where('booking_no', $booking_no)
        ->get();
// dd($bookingGroup);
    if ($bookingGroup->isEmpty()) {
        return redirect()->route('booking.index')
            ->with(['message' => 'Booking not found', 'alert-type' => 'danger']);
    }

    $firstBooking = $bookingGroup->first();
    if ($firstBooking->total_days == 0) {
        return $this->editHourly($booking_no);
    }
    // Calculate totals
    $totalAmount = $bookingGroup->sum('total_amount');
    // $totalPaid   = $bookingGroup->sum(fn($b) => $b->payments->sum('amount'));

    $totalPaid = DB::table('finance_transactions')
    ->where('invoice_no', $booking_no)
    ->where('balance_type', 'Cr')
    ->whereIn('to_acc_name', ['Cash', 'Bank'])
    ->sum('amount');
   $discount = $bookingGroup->first()->discount ?? 0;
// dd(
//     $firstBooking->customer_id,
//     $firstBooking->customer
// );
    // Add this line to fix your error
    $customerTypes = CustomerType::where('status', 1)->pluck('type_name', 'id')->all();
$allGuests = DB::table('booking_guests')
    ->where('booking_no', $booking_no)
    ->get();

// 👉 Self guest detect
$selfGuest = $allGuests->firstWhere('customer_status', 1);

// 👉 Only normal guests (exclude self)
$guests = $allGuests->where('customer_status', '!=', 1)->values();

    return view('pages.room.booking.multiple_booking_edit', compact(
        'bookingGroup', 'firstBooking', 'totalAmount', 'totalPaid', 'discount', 'customerTypes','guests','allGuests','selfGuest'
    ));
}


public function updateBooking(Request $request, $booking_no)
{
    // dd($request);
    $bookingGroup = Booking::where('booking_no', $booking_no)->get();

    if ($bookingGroup->isEmpty()) {
        return redirect()->route('booking.index')
            ->with(['message' => 'Booking not found', 'alert-type' => 'danger']);
    }

    // FinanceAccount update
    $financeAccount = FinanceAccount::updateOrCreate(
        ['account_mobile' => $request->customer_mobile],
        [
            'financegroup_id'      => '7',
            'account_group_code'   => $GLOBALS['CustomerGroupCode'] ?? null,
            'account_name'         => $request->customer_name,
            'account_mobile'       => $request->customer_mobile,
            'account_email'        => $request->customer_email,
            'account_address'      => $request->customer_address,
            'account_company_code' => '01',
            'account_status'       => 1,
            'account_done_by'      => auth()->user()?->name,
        ]
    );

    // Customer update
    $customer = Customer::updateOrCreate(
        ['customer_mobile' => $request->customer_mobile],
        [
            'customer_type'    => 1,
            'customer_name'    => $request->customer_name,
            'customer_gender'  => $request->customer_gender,
            'customer_DOB'     => $request->customer_DOB,
            'customer_mobile'  => $request->customer_mobile,
            'customer_email'   => $request->customer_email,
            'nid_number'       => $request->nid_number,
            'customer_address' => $request->customer_address,
            'status'           => $request->status ?? 1,
            'done_by'          => auth()->user()?->name,
            'ac_id'            => $financeAccount->id,
        ]
    );

    // Guest sync - আগে সব delete
    DB::table('booking_guests')->where('booking_no', $booking_no)->delete();

    // Self guest
    if ($request->has('as_guest')) {
        DB::table('booking_guests')->insert([
            'booking_no'      => $booking_no,
            'name'            => $request->customer_name,
            'nid'             => $request->nid_number,
            'mobile'          => $request->customer_mobile,
            'address'         => $request->customer_address,
            'relation'        => 'Self',
            'customer_status' => 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    // Extra guests
    if ($request->has('guests')) {
        foreach ($request->guests as $guest) {
            if (empty($guest['name']) && empty($guest['mobile']) && empty($guest['nid'])) {
                continue;
            }
            DB::table('booking_guests')->insert([
                'booking_no'      => $booking_no,
                'name'            => $guest['name'] ?? null,
                'nid'             => $guest['nid'] ?? null,
                'mobile'          => $guest['mobile'] ?? null,
                'address'         => $guest['address'] ?? null,
                'relation'        => $guest['relation'] ?? null,
                'customer_status' => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    // Booking rows process
    $errors = [];
    $bookingIds = $request->table_booking_id ?? [];
    $roomIds    = $request->table_room_id ?? [];
    $checkIns   = $request->table_check_in_date ?? [];
    $checkOuts  = $request->table_check_out_date ?? [];
    $prices     = $request->table_room_price ?? [];
    $discount   = $request->discount_amount ?? 0;

    $processedBookingIds = [];
    $grandTotal = 0; // ✅ সব room এর total এখানে জমা হবে

    foreach ($roomIds as $index => $roomId) {
        $checkIn  = Carbon::parse($checkIns[$index]);
        $checkOut = Carbon::parse($checkOuts[$index]);

        if ($checkOut <= $checkIn) {
            $errors[] = "Check-out must be after check-in for room {$roomId}";
            continue;
        }

        $totalDays   = $checkOut->diffInDays($checkIn);
        $totalAmount = $prices[$index] * $totalDays;
        $grandTotal += $totalAmount; // ✅ accumulate

        // Overlap check
        $conflict = Booking::where('room_id', $roomId)
            ->where('booking_no', '!=', $booking_no)
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            })->exists();

        if ($conflict) {
            $errors[] = "Room {$roomId} already booked in selected dates";
            continue;
        }

        if (!empty($bookingIds[$index])) {
            $booking = Booking::find($bookingIds[$index]);
            if ($booking) {
                $booking->update([
                    'check_in_date'   => $checkIn,
                    'check_out_date'  => $checkOut,
                    'total_days'      => $totalDays,
                    'price_per_night' => $prices[$index],
                    'total_amount'    => $totalAmount,
                    'Booking_status'  => 1,
                    'discount'        => $discount,
                ]);
                $processedBookingIds[] = $booking->id;
            }
        } else {
            $new = Booking::create([
                'booking_no'      => $booking_no,
                'customer_id'     => $customer->id,
                'room_id'         => $roomId,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'total_days'      => $totalDays,
                'price_per_night' => $prices[$index],
                'total_amount'    => $totalAmount,
                'Booking_status'  => 1,
                'discount'        => $discount,
            ]);
            $processedBookingIds[] = $new->id;
        }
    }

    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // ✅ আগে delete করুন
    $existingIds = $bookingGroup->pluck('id')->toArray();
    $deleteIds   = array_diff($existingIds, $processedBookingIds);
    if (!empty($deleteIds)) {
        Booking::whereIn('id', $deleteIds)->delete();
    }

    // ✅ তারপর fresh total (delete এর পরে DB থেকে নিন)
     $bookingGroup = Booking::with(['customer', 'room', 'payments'])
        ->where('booking_no', $booking_no)
        ->get();

    $newTotal = $bookingGroup->sum('total_amount');

    $netDiscount = $bookingGroup
        ->groupBy('booking_id')
        ->sum(function ($items) {
            return $items->first()->discount;
        });
    $newTotal = $newTotal - $netDiscount;
    
    // $newTotal = Booking::where('booking_no', $booking_no)->sum('total_amount');

    // ✅ Finance transaction update
    DB::table('finance_transactions')
        ->where('invoice_no', $booking_no)
        ->where('balance_type', 'Dr')
        ->where('narration', 'Room Booking')
        ->update(['amount' => $newTotal]);

    DB::table('finance_transactions')
        ->where('invoice_no', $booking_no)
        ->where('balance_type', 'Cr')
        ->where('narration', 'Room Booking')
        ->update(['amount' => $newTotal]);

    // ✅ Payment update
    $paid = $request->paid_amount ?? 0;

    PaymentDetail::updateOrCreate(
        ['booking_no' => $booking_no],
        ['amount'     => $paid]
    );

    // ✅ Payment transaction update (Cash entry)
    if ($paid > 0) {
        DB::table('finance_transactions')
            ->where('invoice_no', $booking_no)
            ->where('narration', 'Room Booking Payment')
            ->update(['amount' => $paid]);
    }

    return redirect()->route('booking.index')
        ->with(['message' => 'Booking updated successfully', 'alert-type' => 'success']);
}

    // public function multipleBookingStore(Request $request)
    // {
    //     $request->validate([
    //         'customer_name' => 'required|string|max:255',
    //         'customer_mobile' => 'required|string|max:15',
    //         'customer_address' => 'required|string|max:500',
    //         'paid_amount' => 'nullable|numeric',
    //         'payment_status' => 'required|integer',
    //     ]);

    //     // Create or update customer
    //     $customer = Customer::updateOrCreate(
    //         [
    //             'customer_type' => $request->customer_type,
    //             'customer_name' => $request->customer_name,
    //             'nid_number' => $request->nid_number,
    //             'customer_mobile' => $request->customer_mobile,
    //             'customer_address' => $request->customer_address,
    //         ]
    //     );
    //     $lastCustomerId = $customer->id;

    //     // Validate array data before looping
    //     $tableRoomIds = $request->get('table_room_id');
    //     $tableCheckInDates = $request->input('table_check_in_date');
    //     $tableCheckOutDates = $request->input('table_check_out_date');
    //     $paymentStatuses = $request->input('payment_status');

    //     if (
    //         !$tableRoomIds || !is_array($tableRoomIds) ||
    //         count($tableRoomIds) !== count($tableCheckInDates) ||
    //         count($tableRoomIds) !== count($tableCheckOutDates) ||
    //         count($tableRoomIds) !== count($paymentStatuses)
    //     ) {
    //         return back()->withErrors(['error' => 'Invalid booking data provided.']);
    //     }

    //     foreach ($tableRoomIds as $key => $room_id) {
    //         $checkInDate = Carbon::parse($tableCheckInDates[$key]);
    //         $checkOutDate = Carbon::parse($tableCheckOutDates[$key]);

    //         $room = Room::find($tableRoomIds);
    //         if (!$room) {
    //             return back()->withErrors(['error' => 'Room not found.']);
    //         }

    //         $total_days = $checkOutDate->diffInDays($checkInDate);
    //         $room_rent = $room->price_per_night;
    //         $total_amount = $room_rent * $total_days;

    //         $multipleBooking = Booking::create([
    //             'room_id' => $room_id,
    //             'check_in_date' => $checkInDate->format('Y-m-d'),
    //             'check_out_date' => $checkOutDate->format('Y-m-d'),
    //             'total_amount' => $total_amount,
    //             'total_days' => $total_days,
    //             'payment_status' => $request->input('payment_status'),
    //             'customer_id' => $lastCustomerId,
    //         ]);

    //         // Store payment details
    //         PaymentDetail::create([
    //             'booking_id' => $multipleBooking->id,
    //             'amount' => $request->paid_amount,
    //         ]);
    //     }

    //     return redirect()->route('multiple_booking')->with([
    //         'message' => 'Successfully Booked!',
    //         'alert-type' => 'success'
    //     ]);
    // }

    public function show(Room $room)
    {
        return view('pages.room.booking.show', compact('room'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return view('pages.room.booking.update', compact('room'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'room_name' => 'required|string|max:255',
        //     'room_type_id' => 'required|exists:room_types,id',
        //     'status' => 'required|in:available,unavailable',
        // ]);
        $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->ignore($id),
            ],
        ]);
        $room = Room::find($id);
        // dd($room);
        $room->update($request->all());
        return redirect()->route('booking.index')->with([
            'message'    => 'Successfully updated!',
            'alert-type' => 'info',
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with([
            'message'    => 'Successfully deleted!',
            'alert-type' => 'danger',
        ]);
    }
    public function getCustomer($customer_mobile)
    {
        $customer = Customer::where('customer_mobile', $customer_mobile)->first();
        // return response()->json($customer);
        if ($customer) {
            return response()->json($customer);
        } else {
            return response()->json(['error' => 'Customer not found'], 404);
        }
    }
    // Fetch lock statuses for rooms
   public function updateStatus(Request $request)
{
    $request->validate([
        'booking_no' => 'required|exists:bookings,booking_no',
        'booking_status' => 'required|in:0,1,2'
    ]);

    Booking::where('booking_no', $request->booking_no)
        ->update(['Booking_status' => $request->booking_status]);

    return response()->json(['success' => true]);
}
  public function hourlySearch(Request $request)
{
    if (!$request->date || !$request->start_time || !$request->end_time) {
        return response()->json([]);
    }

    $date = $request->date;

    $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
    $endDateTime   = Carbon::parse($request->date . ' ' . $request->end_time);

    // ✅ Order rooms by room_order
    $rooms = Room::orderBy('room_order', 'asc')->get();

    $result = [];

    foreach ($rooms as $room) {

        // ✅ get booking info
        $booking = Booking::where('room_id', $room->id)

            ->where('Booking_status', '!=', 2)
            ->where(function ($q) use ($date, $startDateTime, $endDateTime) {

                // DAY-WISE BOOKING
                $q->where(function ($sub) use ($date) {

                    $sub->where('total_days', '>', 0)
                        ->whereDate('check_in_date', '<=', $date)
                        ->whereDate('check_out_date', '>', $date);

                })

                // HOURLY BOOKING
                ->orWhere(function ($sub) use ($startDateTime, $endDateTime, $date) {

                    $sub->where('total_days', 0)
                        ->whereDate('check_in_date', $date)
                        ->where('check_in_datetime', '<', $endDateTime)
                        ->where('check_out_datetime', '>', $startDateTime);

                });

            })

            ->first();

        $result[] = [

            'id'               => $room->id,
            'price_per_night' => $room->price_per_night,
            'room_number'      => $room->room_number,

            'is_booked'        => $booking ? true : false,

            'date'             => $date,
            'start'            => $request->start_time,
            'end'              => $request->end_time,

            'booked_from' => $booking?->check_in_date,

            'booked_to' => $booking
                ? ($booking->total_days > 0 ? $booking->check_out_date : null)
                : null,
            // ✅ booking info
            'booked_date'      => $booking?->check_in_date,

            'booked_start'     => $booking
                ? Carbon::parse($booking->check_in_datetime)->format('h:i A')
                : null,


            'booked_end'       => $booking
                ? Carbon::parse($booking->check_out_datetime)->format('h:i A')
                : null,

            'booking_type'     => $booking
                ? ($booking->total_days > 0 ? 'Day Booking' : 'Hourly Booking')
                : null,

                'total_days'       => $booking?->total_days,
        ];
    }

    return response()->json($result);
}





public function generateInvoice($bookingNo)
{
    // Bookings for this booking number
    $bookings = Booking::with(['customer', 'room'])
        ->where('booking_no', $bookingNo)
        ->get();

    if ($bookings->isEmpty()) {
        return back()->with('error', 'No bookings found for this invoice.');
    }

    // Payment details
    $paymentDetails = PaymentDetail::where('booking_no', $bookingNo)->get();

    // Booking-level discount (take only first booking's discount, not sum)
    $bookingDiscount = $bookings->first()->discount ?? 0;

    // Invoice calculation
    $subTotal = $bookings->sum('total_amount'); // sum of all rooms
    $netTotal = $subTotal - $bookingDiscount;   // apply discount once
    $receivedAmount = $paymentDetails->sum('amount');
    $dueAmount = $netTotal - $receivedAmount;

    $invoiceSummary = [
        'invoice' => $bookingNo,
        'date' => now()->toDateString(),
        'customer_name' => $bookings->first()->customer->customer_name ?? '',
        'customer_mobile' => $bookings->first()->customer->customer_mobile ?? '',
        'nid_number' => $bookings->first()->customer->nid_number ?? '',
        'customer_address' => $bookings->first()->customer->customer_address ?? '',
        'sub_total' => $subTotal,
        'discount_amount' => $bookingDiscount,
        'net_total' => $netTotal,
        'received_amount' => $receivedAmount,
        'due_amount' => $dueAmount,
    ];
$roomIds = $bookings->pluck('room_id')->unique();

$terms = TermsCondition::where('is_active', 1)
    ->where(function ($q) use ($roomIds) {

        // 🔹 Global Common Terms
        $q->where(function ($q1) {
            $q1->where('term_type', 'common')
               ->where('term_type1', 'common');
        })

        // 🔹 OR Room-wise Included Terms
        ->orWhere(function ($q2) use ($roomIds) {
            $q2->where('term_type', 'included')
               ->where('term_type1', 'room')
               ->whereIn('room_id', $roomIds);
        })
        ->orWhere(function ($q3) use ($roomIds) {
            $q3->where('term_type', 'common')
               ->where('term_type1', 'room');
        });
    })
    ->orderBy('sort_order')
    ->get();

    // Generate PDF using dompdf / mpdf
    $pdf = PDF::loadView('pages.room.booking.invoice_pdf', compact('bookings', 'paymentDetails', 'invoiceSummary','terms'));

    return $pdf->stream('Invoice_'.$bookingNo.'.pdf');
}
public function generateInvoicedetails($bookingNo)
{
    $bookings = Booking::with(['customer', 'room'])
        ->where('booking_no', $bookingNo)
        ->get();

    if ($bookings->isEmpty()) {
        return back()->with('error', 'No bookings found for this invoice.');
    }

    $paymentDetails = PaymentDetail::where('booking_no', $bookingNo)->get();

    $firstBooking = $bookings->first();

    // ✅ Safe discount handling
    $bookingDiscount = $firstBooking->discount ?? 0;

    // ✅ Calculations
    $subTotal = $bookings->sum('total_amount');
    $netTotal = $subTotal - $bookingDiscount;
    $receivedAmount = $paymentDetails->sum('amount');
    $dueAmount = $netTotal - $receivedAmount;

    // ✅ Invoice summary (clean structure)
    $invoiceSummary = [
        'invoice' => $bookingNo,
        'date' => now()->toDateString(),

        'customer_name' => optional($firstBooking->customer)->customer_name,
        'customer_mobile' => optional($firstBooking->customer)->customer_mobile,

        'sub_total' => $subTotal,
        'discount_amount' => $bookingDiscount,
        'net_total' => $netTotal,
        'received_amount' => $receivedAmount,
        'due_amount' => $dueAmount,
    ];

    // ✅ Room IDs
    $roomIds = $bookings->pluck('room_id')->filter()->unique();

    // ✅ Terms (cleaned query)
    $terms = TermsCondition::where('is_active', 1)
        ->where(function ($q) use ($roomIds) {

            $q->where(function ($q1) {
                $q1->where('term_type', 'common')
                   ->where('term_type1', 'common');
            })

            ->orWhere(function ($q2) use ($roomIds) {
                $q2->where('term_type', 'included')
                   ->where('term_type1', 'room')
                   ->whereIn('room_id', $roomIds);
            })

            ->orWhere(function ($q3) {
                $q3->where('term_type', 'common')
                   ->where('term_type1', 'room');
            });

        })
        ->orderBy('sort_order')
        ->get();

      $guests = DB::table('booking_guests')
        ->where('booking_no', $bookingNo)
        ->get();

   return view(
        'pages.room.booking.booking_details',
        compact('bookings', 'paymentDetails', 'invoiceSummary', 'terms','guests')
    );

   
}

public function bookingSummaryReport(Request $request)
{
    $invoice   = $request->invoice;
    $customer  = $request->customer_id;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid_number;
    $room      = $request->room_id;
    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    $query = DB::table('bookings as b')
        ->leftJoin('customers as c1', 'b.customer_id', '=', 'c1.id')
        ->leftJoin('customers as c2', 'b.customer_id', '=', 'c2.ac_id')
        ->leftJoin('rooms as r', 'b.room_id', '=', 'r.id')
        ->select(
            'b.booking_no',
            DB::raw('COALESCE(c1.customer_name, c2.customer_name) as customer_name'),
            DB::raw('COALESCE(c1.customer_mobile, c2.customer_mobile) as customer_mobile'),
            DB::raw('COALESCE(c1.nid_number, c2.nid_number) as nid_number'),
            DB::raw('MIN(b.check_in_date) as check_in_date'),
            DB::raw('MAX(b.check_out_date) as check_out_date'),
            DB::raw('SUM(b.total_days) as total_days'),
            DB::raw('SUM(b.total_amount) as total_amount'),
            DB::raw('MAX(b.discount) as discount'),
            DB::raw('GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ", ") as room_numbers')
        )
        ->where('b.Booking_status', '!=', 2)
        ->groupBy(
            'b.booking_no',
            'c1.customer_name', 'c1.customer_mobile', 'c1.nid_number',
            'c2.customer_name', 'c2.customer_mobile', 'c2.nid_number'
        );

    if ($invoice)  $query->where('b.booking_no', $invoice);
    if ($customer) $query->where('b.customer_id', $customer);
    if ($mobile) {
        $query->where(function ($q) use ($mobile) {
            $q->where('c1.customer_mobile', 'like', '%'.$mobile.'%')
              ->orWhere('c2.customer_mobile', 'like', '%'.$mobile.'%');
        });
    }
    if ($nid) {
        $query->where(function ($q) use ($nid) {
            $q->where('c1.nid_number', 'like', '%'.$nid.'%')
              ->orWhere('c2.nid_number', 'like', '%'.$nid.'%');
        });
    }
    if ($room) $query->where('b.room_id', $room);

    // ✅ Occupancy overlap filter — check-out date EXCLUSIVE
    // দেখাবে যদি বুকিং start_date–end_date রেঞ্জের যেকোনো একদিন occupy করে
    if ($startDate && $endDate) {
        $query->where('b.check_in_date', '<=', $endDate)
              ->where('b.check_out_date', '>', $startDate);
    } elseif ($startDate) {
        // শুধু start_date দেওয়া থাকলে: সেদিন occupy করে এমন বুকিং
        $query->where('b.check_in_date', '<=', $startDate)
              ->where('b.check_out_date', '>', $startDate);
    } elseif ($endDate) {
        $query->where('b.check_in_date', '<=', $endDate)
              ->where('b.check_out_date', '>', $endDate);
    }

    $report = $query->orderByRaw('MAX(b.id) desc')->get();

    // ✅ Finance থেকে paid amount
    $bookingNos = $report->pluck('booking_no')->values()->toArray();

    $allPayments = DB::table('finance_transactions')
        ->select('invoice_no', DB::raw('SUM(amount) as paid_amount'))
        ->where('balance_type', 'Cr')
        ->where('narration', 'Room Booking Payment')
        ->whereIn('to_acc_name', ['Cash', 'Bank'])
        ->whereIn('invoice_no', $bookingNos)
        ->groupBy('invoice_no')
        ->pluck('paid_amount', 'invoice_no');

    $totalPaid = $allPayments->sum();

    $customers = DB::table('customers')->select('id', 'customer_name')->get();
    $rooms     = DB::table('rooms')->select('id', 'room_name')->get();

    return view('pages.room.booking.booking_summary_report', compact(
        'report', 'customers', 'rooms', 'totalPaid', 'allPayments'
    ));
}
public function bookingSummaryReportPdf(Request $request)
{
    $invoice   = $request->invoice;
    $customer  = $request->customer_id;
    $mobile    = $request->customer_mobile;
    $nid       = $request->nid_number;
    $room      = $request->room_id;
    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    $query = DB::table('bookings as b')
        ->leftJoin('customers as c1', 'b.customer_id', '=', 'c1.id')
        ->leftJoin('customers as c2', 'b.customer_id', '=', 'c2.ac_id')
        ->leftJoin('rooms as r', 'b.room_id', '=', 'r.id')
        ->select(
            'b.booking_no',
            DB::raw('COALESCE(c1.customer_name, c2.customer_name) as customer_name'),
            DB::raw('COALESCE(c1.customer_mobile, c2.customer_mobile) as customer_mobile'),
            DB::raw('COALESCE(c1.nid_number, c2.nid_number) as nid_number'),
            DB::raw('MIN(b.check_in_date) as check_in_date'),
            DB::raw('MAX(b.check_out_date) as check_out_date'),
            DB::raw('SUM(b.total_days) as total_days'),
            DB::raw('SUM(b.total_amount) as total_amount'),
            DB::raw('MAX(b.discount) as discount'),
            DB::raw('GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ", ") as room_numbers')
        )
        
        ->where('b.Booking_status', '!=', 2)
        ->groupBy(
            'b.booking_no',
            'c1.customer_name', 'c1.customer_mobile', 'c1.nid_number',
            'c2.customer_name', 'c2.customer_mobile', 'c2.nid_number'
        );

    if ($invoice) {
        $query->where('b.booking_no', $invoice);
    }

    if ($customer) {
        $query->where('b.customer_id', $customer);
    }

    if ($mobile) {
        $query->where(function ($q) use ($mobile) {
            $q->where('c1.customer_mobile', 'like', '%' . $mobile . '%')
              ->orWhere('c2.customer_mobile', 'like', '%' . $mobile . '%');
        });
    }

    if ($nid) {
        $query->where(function ($q) use ($nid) {
            $q->where('c1.nid_number', 'like', '%' . $nid . '%')
              ->orWhere('c2.nid_number', 'like', '%' . $nid . '%');
        });
    }

    if ($room) {
        $query->where('b.room_id', $room);
    }

    // ✅ Occupancy overlap filter — check-out date EXCLUSIVE
    // start_date–end_date রেঞ্জের যেকোনো একদিন occupy করলেই দেখাবে
    if ($startDate && $endDate) {
        $query->where('b.check_in_date', '<=', $endDate)
              ->where('b.check_out_date', '>', $startDate);
    } elseif ($startDate) {
        $query->where('b.check_in_date', '<=', $startDate)
              ->where('b.check_out_date', '>', $startDate);
    } elseif ($endDate) {
        $query->where('b.check_in_date', '<=', $endDate)
              ->where('b.check_out_date', '>', $endDate);
    }

    $report = $query->orderByRaw('MAX(b.id) desc')->get();

    $bookingNos = $report->pluck('booking_no')->toArray();

    $allPayments = DB::table('finance_transactions')
        ->select(
            'invoice_no',
            DB::raw('SUM(amount) as paid_amount')
        )
        ->where('balance_type', 'Cr')
        ->where('narration', 'Room Booking Payment')
        ->whereIn('to_acc_name', ['Cash', 'Bank'])
        ->whereIn('invoice_no', $bookingNos)
        ->groupBy('invoice_no')
        ->pluck('paid_amount', 'invoice_no');

    $customers = DB::table('customers')
        ->select('id', 'customer_name')
        ->get();

    $rooms = DB::table('rooms')
        ->select('id', 'room_name')
        ->get();

    $company = DB::table('company_settings')->first();

    $data = [
        'company_name'     => $company->company_name ?? config('app.name'),
        'company_logo_one' => $company->company_logo_one,
        'start_date'       => $startDate
            ? Carbon\Carbon::parse($startDate)->format('d M Y')
            : '—',
        'end_date'         => $endDate
            ? Carbon\Carbon::parse($endDate)->format('d M Y')
            : '—',
    ];

    $pdf = Pdf::loadView(
        'pages.room.booking.booking_summary_report_pdf',
        compact(
            'data',
            'report',
            'customers',
            'rooms',
            'allPayments'
        )
    )->setPaper('a4', 'portrait');

    return $pdf->stream('booking-summary-report.pdf');
}
}
