<?php

namespace App\Http\Controllers\frontend;

use App\Models\cr;
use Illuminate\Http\Request;
use App\Models\FrontendModel;
use App\Models\Room;
use App\Models\Customer;
use App\Models\FrontEndUser;
use App\Models\FrontendBookings;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class FrontendBookingController extends Controller
{
    public function frontendbookRoom(Request $request)
{
    $validatedData = $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:15',
        'customer_address' => 'required|string|max:255',
        'arrival_date' => 'required|date',
        'departure_date' => 'required|date|after:arrival_date',
    ]);

    $room_id = $request->input('room_id');

    // Check if the room is already booked
    $isBooked = FrontendBookings::where('room_id', $room_id)
        ->where('Booking_status', '!=', 2)
        ->where(function ($query) use ($request) {
            $query->where('check_in_date', '<', $request->departure_date)
                  ->where('check_out_date', '>', $request->arrival_date);
        })
        ->exists();

    if ($isBooked) {
        return redirect()->back()->withInput()->withErrors([
            'room_id' => 'The selected room is already booked for the given date range.',
        ]);
    }

    try {
        $bookingNumber = DB::transaction(function () use ($request, $room_id) {

            // lockForUpdate prevents two simultaneous bookings from getting
            // the same booking_no (this is what was silently failing/racing before)
            $bookingNo = DB::table('invoiceno')->lockForUpdate()->first();

            if (!$bookingNo) {
                // table row missing — create a starting point instead of a fatal error
                DB::table('invoiceno')->insert(['booking_no' => 1]);
                $getBookingNo = 1;
            } else {
                $getBookingNo = $bookingNo->booking_no;
            }

            $bookingNumber = 'BO' . str_pad($getBookingNo, 6, '0', STR_PAD_LEFT);

            DB::table('invoiceno')->update([
                'booking_no' => $getBookingNo + 1,
            ]);

            $user = Customer::firstOrCreate(
                ['customer_mobile' => $request->customer_phone],
                [
                    'customer_name' => $request->customer_name,
                    'customer_address' => $request->customer_address,
                    'customer_type' => $request->customer_type,
                ]
            );

            $checkInDate = Carbon::parse($request->arrival_date);
            $checkOutDate = Carbon::parse($request->departure_date);
            $total_days = $checkOutDate->diffInDays($checkInDate);
            if ($total_days < 1) {
                $total_days = 1;
            }

            $room = Room::findOrFail($room_id);
            $total_amount = $room->price_per_night * $total_days;

            FrontendBookings::create([
                'room_id' => $room_id,
                'customer_id' => $user->id,
                'total_days' => $total_days,
                'total_amount' => $total_amount,
                'booking_no' => $bookingNumber,
                'check_in_date' => $request->arrival_date,
                'check_out_date' => $request->departure_date,
                'Booking_status' => 0,
                'check_in_datetime' => '10:59:59',
                'check_out_datetime' => '10:59:59',
            ]);

            return $bookingNumber;
        });

        return redirect()->back()->with('success', 'Booking successfully created! Your booking number is ' . $bookingNumber);

    } catch (\Throwable $e) {
        report($e); // logs the real error to laravel.log for debugging
        return redirect()->back()->withInput()->withErrors([
            'room_id' => 'Something went wrong while creating the booking. Please try again.',
        ]);
    }
}
}
