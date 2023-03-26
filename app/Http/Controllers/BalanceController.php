<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource. ["USDT", "ZINA", "BUSD", "BTC"] ["USD"]
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Get all user balance
            
            $where['user_id'] = $request->user()->id;
            if(null != ($request->has('type'))){
                $where['balance_type']  =   $request->type;
            }
            $balance = Balance::where($where)->get();
            if(auth('admin')->check()){
                // return all user balance
                $balance = Balance::paginate(15);
            }

            if ($balance) {
                return get_success_response($balance);
            }
            
            return get_error_response(['msg' => 'Error retreiving user balance'], 410);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function balance(Request $request)
    {
        try {
            $allowedWallet = [
                "BTC"   =>  [
                    "wallet"            =>  "Bitcoin",
                    "balance"           =>  "0.00000000",
                    "ticker_icon"       =>  "https://cryptologos.cc/logos/bitcoin-btc-logo.svg",
                    "ticker_name"       =>  "BTC",
                    "ticker_full_name"  =>  "Bitcoin",
                    "balance_type"      =>  "Crypto"
                ], 
                "USDT"  =>  [
                    "wallet"            =>  "Tether",
                    "balance"           =>  "0.00000000",
                    "ticker_icon"       =>  "https://cryptologos.cc/logos/tether-usdt-logo.svg",
                    "ticker_name"       =>  "USDT",
                    "ticker_full_name"  =>  "Tether",
                    "balance_type"      =>  "Crypto"
                ],
                "LTC"   =>  [
                    "wallet"            =>  "Litecoin",
                    "balance"           =>  "0.00000000",
                    "ticker_icon"       =>  "https://cryptologos.cc/logos/litecoin-ltc-logo.svg",
                    "ticker_name"       =>  "LTC",
                    "ticker_full_name"  =>  "Litecoin",
                    "balance_type"      =>  "Crypto"
                ],
                "BNB"   =>  [
                    "wallet"            =>  "Binance coin",
                    "balance"           =>  "0.00000000",
                    "ticker_icon"       =>  "https://cryptologos.cc/logos/bnb-bnb-logo.svg?v=024",
                    "ticker_name"       =>  "BNB",
                    "ticker_full_name"  =>  "Binance coin",
                    "balance_type"      =>  "Crypto"
                ],
                "USD"   =>  [
                    "wallet"            =>  "United States Dollar",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://catamphetamine.github.io/country-flag-icons/3x2/US.svg",
                    "ticker_name"       =>  "USD",
                    "ticker_full_name"  =>  "United States Dollar",
                    "balance_type"      =>  "Fiat"
                ],
                "NGN"   =>  [
                    "wallet"            =>  "Nigeria Naira",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://catamphetamine.github.io/country-flag-icons/3x2/NG.svg",
                    "ticker_name"       =>  "NGN",
                    "ticker_full_name"  =>  "Nigeria Naira",
                    "balance_type"      =>  "Fiat"
                ],
                "CLP"   =>  [
                    "wallet"            =>  "Chilean Peso",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://catamphetamine.github.io/country-flag-icons/3x2/CL.svg",
                    "ticker_name"       =>  "CLP",
                    "ticker_full_name"  =>  "Chilean Peso",
                    "balance_type"      =>  "Fiat"
                ],
                "EUR"   =>  [
                    "wallet"            =>  "Euro",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://flagicons.lipis.dev/flags/4x3/eu.svg",
                    "ticker_name"       =>  "EUR",
                    "ticker_full_name"  =>  "Euro",
                    "balance_type"      =>  "Fiat"
                ],
                "GBP"   =>  [
                    "wallet"            =>  "British pound sterling",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://catamphetamine.github.io/country-flag-icons/3x2/GB.svg",
                    "ticker_name"       =>  "GBP",
                    "ticker_full_name"  =>  "British pound sterling",
                    "balance_type"      =>  "Fiat"
                ],
                "INR"   =>  [
                    "wallet"            =>  "Indian Rupee",
                    "balance"           =>  "0.00",
                    "ticker_icon"       =>  "https://catamphetamine.github.io/country-flag-icons/3x2/IN.svg",
                    "ticker_name"       =>  "INR",
                    "ticker_full_name"  =>  "Indian Rupee",
                    "balance_type"      =>  "Fiat"
                ],
            ];
            // Get all user balance
            $where['user_id'] = $request->user()->id;
            if(null != ($request->has('type')) && !empty($request->type)){
                $where['balance_type']  =   $request->type;
            }
            $balance = Balance::where($where)->get();
            
            if($balance->isEmpty()){
                foreach($allowedWallet as $data){
                    $curr["wallet"]             =  $data["wallet"];
                    $curr["user_id"]            =  auth()->id();
                    $curr["balance"]            =  $data["balance"];
                    $curr["ticker_icon"]        =  $data["ticker_icon"];
                    $curr["ticker_name"]        =  $data["ticker_name"];
                    $curr["balance_type"]       =  $data["balance_type"];
                    $curr["ticker_full_name"]   =  $data["ticker_full_name"];
                    Balance::create($curr);
                }
                $balance = Balance::where($where)->get();
            }
            
            if ($balance) {
                // $getTotalBalance = $this->total_balance($balance);
                // $balance['total_balance'] = $getTotalBalance;
                return get_success_response($balance);
            }
            return get_error_response(['msg' => 'Error retreiving user balance'], 410);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    public function total_balance(Request $request)
    {
        $where['user_id'] = $request->user()->id;
        if(null != ($request->has('type')) && !empty($request->type)){
            $where['balance_type']  =   $request->type;
        }
        if(null != ($request->has('currency')) && !empty($request->currency)){
            $curr  =   strtoupper($request->currency);
        }
        $balance = Balance::where($where)->get();
        $rate = 0;
        if(!empty($balance)){
            $curr = "USD";
            foreach($balance as $totalBalance){
                return getExchangeVal($totalBalance['ticker_name'],  $curr, $totalBalance['balance']);
                return $rate += getExchangeVal($totalBalance['ticker_name'],  $curr, $totalBalance['balance']);
            }
        }
        
        return get_success_response(["currency" => $curr ?? "USD", "balance" => number_format($rate,2)]);
    }
}
