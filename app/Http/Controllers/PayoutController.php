<?php

namespace App\Http\Controllers;

use App\Models\Withdraw;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PayoutController extends Controller
{
    public function withdraw(Request $request)
    {   
        $arr = ["BTC", "BNB", "USDT", "LTC"];
        if(!in_array($request->wallet_type, $arr)){
          return get_error_response(["error" => "Invalid withdrawal method provided"]);
        }
        
        // check if user has enough funds in there wallet
        $where['user_id'] = $request->user()->id;
        $where['ticker_name']  =   $request->wallet_type;
        $balance = Balance::where($where)->first();
        
        if($balance && $balance->wallet >= $request->amount){
            Withdraw::create([
                "user_id"         =>  $request->user()->id,
                "amount"          =>  $request->amount,
                "wallet_type"     =>  $request->wallet_type,
                "wallet_address"  =>  $request->wallet_address,
            ]);
            return get_success_response(["msg" => "Your withdrawal request is received and will be processed soon."]);
        } else {
            return get_error_response(["error" => "Insufficient wallet balance"]);
        }
    }
    
    public function withdraw_history()
    {
        $data = Withdraw::where('user_id', request()->user()->id)->orderBy("created_at", "desc")->paginate(15);
        return get_success_response($data);
    }
}