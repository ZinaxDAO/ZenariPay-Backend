<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DepositController extends Controller
{
    
    public function topup(Request $request)
    {   
        $arr = ["BTC", "BNB", "USDT", "LTC"];
        if(!in_array($request->wallet_type, $arr)){
            return get_error_response(["error" => "Invalid deposit method provided"]);
        }
        
        // check if user has enough funds in there wallet
        $where['user_id'] = $request->user()->id;
        $where['ticker_name']  =   $request->wallet_type;
        $balance = Balance::where($where)->first();
        
        $fees = get_fees($request->wallet_type, $request->amount, $request->fiat);
        $apiRequest = GetDepositAddress::getRandomDepositWallet($request->wallet_type);
        if(empty($apiRequest) OR $apiRequest == false):
            return get_error_response(['msg' => 'Coin currently not available/Supported'], 404);
        endif;
        $amountInCrypto = getExchangeVal($request->fiat, $request->wallet_type, $request->amount);
        
        $deposit = Deposit::create([
            "user_id"           =>  $request->user()->id,
            "fiat"              =>  $request->fiat,
            "amount"            =>  $request->amount,
            "cryptoAmount"      =>  number_format($amountInCrypto, 8),
            "wallet_type"       =>  $request->wallet_type,
            "wallet_address"    =>  $apiRequest,
            "timeout"           =>  Carbon::now()->addMinutes(30)
        ]);
        
        $deposit['id'] = $deposit->id;
        
        if($deposit){
            $user = $request->user();
            $merchant = $user->id;
            $data = [
                'user_id'       =>  $merchant,
                "reference"     =>  $deposit->id,
                'customerName'  =>  $user->name,
                'customerEmail' =>  $user->email,
                'coin'          =>  $request->wallet_type,
                'currency'      =>  $request->fiat,
                'fiatAmount'    =>  $request->amount,
                'cryptoAmount'  =>  number_format($amountInCrypto, 8),
                'feeInCrypto'   =>  $fees['feeInCrypto'],
                'order_type'    =>  'Crypto_Topup',
                'address'       =>  $apiRequest,
            ];    

            Order::create($data);
            
            return get_success_response($deposit);
            // return get_success_response(["msg" => "Your deposit request will be processed as soon as payment is received."]);
        } else {
            return get_error_response(["error" => "Unable to initiate deposit action."]);
        }
    }
    
    public function getDeposit($id)
    {
        $deposit = Deposit::whereId($id)->first();
        if(!$deposit) return get_error_response(['error' => "Top up with the provided data not found."], 404);
        return get_success_response(['status' => $deposit->deposit_status]);
    }
    
    public function deposit_history()
    {
        $data = Deposit::where('user_id', request()->user()->id)->orderBy("created_at", "desc")->paginate(15);
        return get_success_response($data);
    }
}