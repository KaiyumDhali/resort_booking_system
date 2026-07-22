<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\PaymentDetail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentDetailController extends Controller
{
    public function index() {}
    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_type' => 'required',
            'accounts_name'=> 'required',
        ]);

        $bookingNo  = $request->booking_no;
        $amount     = $request->amount;
        $date       = now()->format('Y-m-d');
        $done_by    = Auth::user()->name;

        // customer এর ac_id বের করো
        $booking    = Booking::where('booking_no', $bookingNo)->with('customer')->first();
        $customer   = $booking->customer;
        $toAccId    = $customer->ac_id; // customer finance account
        $toAccName  = FinanceAccount::where('id', $toAccId)->value('account_name');

        // selected cash/bank/mbank account
        $acidId     = $request->accounts_name;
        $acidName   = FinanceAccount::where('id', $acidId)->value('account_name');

        // payment group name
        $payAccGroupName = FinanceAccount::leftJoin('finance_groups', 'finance_accounts.account_group_code', '=', 'finance_groups.group_code')
            ->where('finance_accounts.id', $acidId)
            ->value('finance_groups.group_name');

        $payment_type = $payAccGroupName;

        // cheque/mbank details
        $cheque_type      = $request->cheque_type;
        $cheque_no        = $request->cheque_no;
        $cheque_date      = $request->cheque_date;
        $mobile_bank_name = $request->mobile_bank_name;
        $mobile_number    = $request->mobile_number;
        $transaction_id   = $request->transaction_id;
        $remarks          = $request->remarks;

        $formatAmount = number_format($amount, 2);

        // narration build
        if ($payAccGroupName == 'Bank Account') {
            if ($cheque_type == 'Cheque') {
                $narration = "$acidName Received From: $toAccName, Through Bank $cheque_type, Cheque No:$cheque_no, Cheque Date:$cheque_date, Received Amount:$formatAmount TK $remarks";
            } else {
                $narration = "$acidName Received From: $toAccName, Through Bank $cheque_type, Received Amount:$formatAmount TK $remarks";
            }
        } elseif ($payAccGroupName == 'Mobile Bank') {
            $narration = "$acidName Received From: $toAccName, Through Mobile Bank:$mobile_bank_name, Mobile Number: $mobile_number, Transaction ID:$transaction_id, Received Amount:$formatAmount TK $remarks";
        } else {
            $narration = "$acidName Received From: $toAccName, Through Cash Received Amount:$formatAmount TK $remarks";
        }

        DB::beginTransaction();
        try {
            // 1. PaymentDetail save
            $paymentDetail               = new PaymentDetail();
            $paymentDetail->booking_no   = $bookingNo;
            $paymentDetail->amount       = $amount;
            $paymentDetail->save();

            // 2. booking payment_status update
            $bookingTotalAmount  = Booking::where('booking_no', $bookingNo)->sum('total_amount');
            $totalReceivedSum    = PaymentDetail::where('booking_no', $bookingNo)->sum('amount');
            $discount            = Booking::where('booking_no', $bookingNo)->first()->discount ?? 0;

            if (($bookingTotalAmount - $discount - $totalReceivedSum) <= 0) {
                DB::table('bookings')
                    ->where('booking_no', $bookingNo)
                    ->update(['payment_status' => 1]);
            }

            // 3. Voucher No generate
            $crVoucher      = DB::table('invoiceno')->first('voucher_no');
            $getCrVoucherNo = $crVoucher->voucher_no;
            $crVoucherNo    = '01CR' . str_pad($getCrVoucherNo, 6, '0', STR_PAD_LEFT);
            DB::table('invoiceno')->update(['voucher_no' => $getCrVoucherNo + 1]);

            // 4. Finance Transaction — Customer Cr
            FinanceTransaction::create([
                'company_code'     => '01',
                'invoice_no'       => $bookingNo,
                'voucher_no'       => $crVoucherNo,
                'voucher_date'     => $date,
                'acid'             => $toAccId,
                'to_acc_name'      => $acidName,
                'type'             => 'CR',
                'amount'           => $amount,
                'balance_type'     => 'Cr',
                'payment_type'     => $payment_type,
                'narration'        => $narration,
                'cheque_no'        => $cheque_no,
                'cheque_date'      => $cheque_date,
                'cheque_type'      => $cheque_type,
                'transaction_date' => $date,
                'transaction_by'   => $done_by,
                'done_by'          => $done_by,
                'updated_by'       => $done_by,
            ]);

            // 5. Finance Transaction — Cash/Bank Dr
            FinanceTransaction::create([
                'company_code'     => '01',
                'invoice_no'       => $bookingNo,
                'voucher_no'       => $crVoucherNo,
                'voucher_date'     => $date,
                'acid'             => $acidId,
                'to_acc_name'      => $toAccName,
                'type'             => 'CR',
                'amount'           => $amount,
                'balance_type'     => 'Dr',
                'payment_type'     => $payment_type,
                'narration'        => $narration,
                'cheque_no'        => $cheque_no,
                'cheque_date'      => $cheque_date,
                'cheque_type'      => $cheque_type,
                'transaction_date' => $date,
                'transaction_by'   => $done_by,
                'done_by'          => $done_by,
                'updated_by'       => $done_by,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Payment failed: ' . $e->getMessage()]);
        }

        return redirect()->back()->with([
            'message'    => 'Payment saved successfully!',
            'alert-type' => 'success'
        ]);
    }

    public function show(PaymentDetail $paymentDetail) {}

    public function edit($id)
    {
        $bookingNo       = $id;
        $bookingRooms    = Booking::where('booking_no', $id)->get();
        $discount        = $bookingRooms->first()->discount ?? 0;
        $firstBooking    = Booking::where('booking_no', $id)->with('customer', 'room')->first();
        $bookingTotalSum = Booking::where('booking_no', $id)->sum('total_amount');
        $totalReceivedSum= PaymentDetail::where('booking_no', $id)->sum('amount');
        $totalReceived   = PaymentDetail::where('booking_no', $id)->get();

        return view('pages.room.payment.payment-add', compact(
            'discount', 'bookingNo', 'bookingTotalSum',
            'firstBooking', 'totalReceived', 'totalReceivedSum'
        ));
    }

    public function update(Request $request, $id) {}
    public function destroy(PaymentDetail $paymentDetail) {}
}