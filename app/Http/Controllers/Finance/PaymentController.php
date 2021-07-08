<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Payment;
use App\Models\Scheduling\Reserve;
use App\Models\User;
use Gabievi\Promocodes\Promocodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function confirmation(Request $request)
    {
        DB::beginTransaction();
        try {
            $payment = new Payment();
            $payment->reserve = $request->get('extra2');
            if ($request->get('extra3') != null) {
                $promocode = Promocodes::check($request->get('extra3'));
                if ($promocode instanceof Promocodes) {
                    User::redeemCode($promocode->code, $callback = null);
                    $payment->promocode = $promocode->id;
                }
            }
            $payment->name = $request->get('nickname_buyer');
            $payment->reference = $request->get('extra1');
            $payment->email = $request->get('email_buyer');
            $payment->phone = $request->get('phone');
            $payment->reference_sale = $request->get('reference_sale');
            $payment->state_pol = $request->get('state_pol');
            $payment->payment_method = $request->get('payment_method');
            $payment->payment_method_type = $request->get('payment_method_type');
            $payment->installments_number = $request->get('installments_number');
            $payment->transaction_date = $request->get('transaction_date');
            $payment->cus = $request->get('cus');
            $payment->pse_bank = $request->get('pse_bank');
            $payment->authorization_code = $request->get('authorization_code');
            $payment->ip = $request->get('ip');
            $payment->transaction_id = $request->get('transaction_id');
            $payment->payment_method_name = $request->get('payment_method_name');
            $payment->value = $request->get('value');
            $payment->save();
            $reserve =  Reserve::where('reference', $request->get('extra1'))->first();
            if ($reserve instanceof Reserve) {
                $reserve->status = $request->get('state_pol');
                $reserve->update();
            }
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PaymentController:confirmation', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
