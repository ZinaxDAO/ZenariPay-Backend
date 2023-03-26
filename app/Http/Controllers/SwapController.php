<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Order;
use App\Models\User;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SwapController extends Controller
{
    public function swap(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'from_currency' =>  'required',
                'to_currrency'  =>  'required',
                'amount'        =>  'required',
            ]);

            if($validateUser->fails()){
                return get_error_response($validateUser->errors(), 400);
            }
            
            $r = request();
            $fees = $this->getfees($r->amount);
            $amount = ($r->amount - $fees);
            $exchange = getExchangeVal($r->from_currency, $r->to_currrency);
            $finalAmount = $exchange * $amount;
            if($finalAmount) :
                $result = [
                    "from_amount"   =>  $r->amount,
                    "to_amount"     =>  $finalAmount,
                    "from_currency" =>  $r->from_currency,
                    "to_currrency"  =>  $r->to_currrency,
                    "rate"          =>  $exchange,
                    "fees"          =>  $fees
                ];
                if($request->post()):
                    // perform wallet charging
                endif;
                return get_success_response($result);
            endif;
            
            return get_error_response(['msg' => 'Unable to initiate Transaction at the moment, Please try again later'], 422);
        } catch (\Throwable $th) {
            // Return server error
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    public function process_swap(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'from_currency' =>  'required',
                'to_currrency'  =>  'required',
                'amount'        =>  'required',
            ]);

            if($validateUser->fails()){
                return get_error_response($validateUser->errors(), 400);
            }
            
            $r = request();
            $exchange = getExchangeVal(strtoupper($r->from_currency), strtoupper($r->to_currrency));
            $fees = $this->getfees($r->amount);
            $amount = ($r->amount - $fees);
            $finalAmount = $exchange * $amount;
            if($finalAmount) :
                $result = [
                    "from_amount"   =>  $r->amount,
                    "to_amount"     =>  number_format($finalAmount, 6),
                    "from_currency" =>  $r->from_currency,
                    "to_currrency"  =>  $r->to_currrency,
                    "rate"          =>  $exchange,
                    "fees"          =>  $fees.$r->from_currency
                ];
                if($request->post()):
                    // perform wallet charging
                    // debit from currency
                    $fromWallet = Balance::where(['user_id' => auth()->id(), 'ticker_name' => $r->from_currency])->first();
                    if($fromWallet->balance >= $r->amount){
                        $fromWallet->decrement("balance", $r->amount);
                        $user = User::find(auth()->id());
                        $swap_data = [
                            'user_id'           =>  $user->id,
                            "reference"         =>  _getTransactionId(),
                            'customerName'      =>  $user->name,
                            'customerEmail'     =>  $user->email,
                            'coin'              =>  $request->from_currency,
                            'currency'          =>  $request->to_currrency,
                            'from_currency'     =>  $request->from_currency,
                            'to_currency'       =>  $request->to_currrency,
                            'from_amount'       =>  $r->amount,
                            'to_amount'         =>  $finalAmount,
                            'order_type'        =>  'currency_swap'
                        ];  
                        Order::create($swap_data);
                    } else {
                        return get_error_response(['msg' => "Insuficient balance"]);
                    }
                    
                    // credit to currency
                    $toWallet = Balance::where(['user_id' => auth()->id(), 'ticker_name' => $r->to_currrency])->first();
                    $toWallet->increment("balance", $finalAmount);
                endif;
                return get_success_response(["msg" => "swap completed successfully", "data" => $result]);
            endif;
            
            return get_error_response(['error' => 'Unable to initiate Transaction at the moment, Please try again later'], 422);
        } catch (\Throwable $th) {
            // Return server error
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    public function getfees($amount)
    {
        $percent = settings('swap_commission');
        $fees = get_commision($amount, $percent);
        return $fees;
    }
}