<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Trade;
use App\Models\Balance;
use App\Models\TradeHistory;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TradeHistoryController extends Controller
{
    public function paid(Request $request)
    {
        if($request->post()){
            $trade = TradeHistory::where("id", $request->trade_id)->where('user_id', $request->user()->id)->first();
            if(!$trade){
                return get_error_response(["error" => "Trade not found"], 404);
            }
            if($trade->trade_status == "SUCCESS"){
                return get_error_response(["error" => "Trade is already completed"], 400);
            }
            if($trade->is_paid == 1){
                return get_error_response(["error" => "Trade is already marked as paid"], 400);
            }
            if(strtolower($trade->trade_status) == "pending"){
                $trade->is_paid = 1;
                $trade->save();
                
                return get_success_response(["msg" => "Trade as been marked as paid successfully"]);
            }
            return get_error_response(["error" => "Unable to mark trade as paid, please contact support"], 400);
        }
    }
    
    public function trade_status(Request $request)
    {

        $trade = TradeHistory::where("id", $request->trade)->where('user_id', $request->user()->id)->first();
        if(!$trade){
            return get_error_response(["error" => "Trade not found"], 404);
        }
        if($trade->trade_status == "SUCCESS"){
            return get_error_response(["error" => "Trade is already completed"], 400);
        }
        
        return get_success_response(["status" => $trade->trade_status]);
     
    }
    
    public function received(Request $request)
    {
        if($request->post()){
            $trade = TradeHistory::where("id", $request->trade_id)->where('agent_id', $request->user()->id)->first();
            if(!$trade){
                return get_error_response(["error" => "Trade not found"], 404);
            }
            if((int)$trade->is_paid != 1){
                return get_error_response(["error" => "The customer needs to mark trade as paid firstly"], 400);
            }
            if($trade->trade_status == "SUCCESS"){
                return get_error_response(["error" => "Trade is already completed"], 400);
            }
            if(strtolower($trade->trade_status) == "pending" && (int)$trade->is_paid == 1){
                $agent = balanceTopup($trade->trade_amount, $trade->trade_currency, $trade->agent_id, 'dr');
                $customer = balanceTopup($trade->trade_amount, $trade->trade_currency, $trade->user_id);
                $trade->is_received = 1;
                $trade->trade_status = "SUCCESS";
                $trade->save();
                
                
                return get_success_response(["msg" => "Trade as been marked as completed successfully"]);
            }
            return get_error_response(["error" => "Unable to complete trade, please contact support"], 400);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Grab list of all Buy Trades active and non active
        try {
            $where = [];
            if($request->has('status') && !empty($request->has('status'))):
                $where['trade_status'] = strtoupper($request->input('status'));
            endif;
            
            $user = $request->user();
            $whereAgent['agent_id'] = $user->id;
            $whereUser['user_id'] = $user->id;
            $trades = TradeHistory::where($where)->where($whereAgent)->orWhere($whereUser)->with('trade', 'user', 'agent')->orderBy('created_at', 'desc')->paginate(per_page());
            
            return get_success_response($trades);
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
        // return response()->json($request->only(['trade_currency', 'trade_amount', 'trade_type', 'payment_id']));
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'trade_amount'  =>  'numeric|required',
                    'trade_type'    =>  'required',
                    'trade_currency'=>  'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 417);
            }


            $tradeType = strtolower($request->trade_type);
            $user = $request->user();
            
            //check if customer has enough balance for the specified wallet.
            $bl['user_id'] = $request->user()->id;
            $bl['balance_type']  =   $request->type;
            $customer_balance = Balance::where($bl)->get();
            if($customer_balance < $request->trade_amount){
                return get_error_response(['error' => 'Insufficient balance'], 402);
            }

            if(!in_array(strtolower($tradeType), ['buy', 'sell'])):
                return get_error_response(['error' => 'Unknown Transaction type'], 417);
            endif;
            
            if($tradeType == "sell" && !$request->has('payment_id')){
                return get_error_response([
                    "payment_id" => [
                        "The payment id field is required."
                    ]
                ]);
            }
            
            $where['trade_currency'] =  $request->trade_currency;
            
            // convert trade amount to float and store as variable $t
            $t =  floatval($request->trade_amount);

            $query = Trade::where($where)->where('tradeType', '!=', $tradeType)->where('user_id', '!=', $user->id)->orderBy('cancellation_rate', 'ASC');
            // $get_trade = $query->where('min_amount', '>=', $request->trade_amount)->where('max_amount', '>=', $request->trade_amount)->first();
            // $get_trade = $query->whereRaw('? between min_amount and max_amount', $request->trade_amount)->first();
            $get_trade = $query->where(function ($query) use ($t) {
                                $query->where('min_amount', '<=', $t);
                                $query->where('max_amount', '>=', $t);
                            })->first();

            if(!$get_trade){
                return get_error_response(["error" => "Sorry we can't process your request at the moment please try a different amount or another currency"], 404);
            }
            
            if(!$get_trade OR $get_trade->user_id == $user->id):
                return get_error_response(['error' => "Selected Agent is currently unavailable to accept new order"], 404);
            endif;
            
            $get_trade->max_amount = (floatval($get_trade->max_amount) - floatval($request->trade_amount));
            $get_trade->save();
            
            // create a new Buy Trade
            $uniquePaymentId = $request->payment_id;
            if($request->trade_type == 'buy'){
                $uniquePaymentId = $get_trade->paymentMethod;
            }
            
            $trade = new TradeHistory();
            $trade->user_id         = $user->id;
            $trade->trade_id        = $get_trade->id;
            $trade->agent_id        = $get_trade->user_id;
            $trade->trade_status    = 'PENDING';
            $trade->trade_amount    = $request->trade_amount;
            $trade->transaction_id  = _getTransactionId();
            $trade->trade_currency  = $get_trade->trade_currency;
            $trade->payment_id      = $uniquePaymentId;
            $trade->trade_type      = $request->trade_type;


            if ($trade->save()) {
                // get order joined with payment data paymentMethod
                $trade = TradeHistory::where('id', $trade->id)->with('payment_method', 'trade', 'user', 'agent')->first()->makeHidden(['created_at', 'updated_at', 'deleted_at']);
                // var_dump($trade->trade->paymentMethod); exit;
                $paymentMethod = $trade->trade->paymentMethod;
                
                $trade = to_array($trade);
                $trade['payment_method'] = Paymentmethod::find($paymentMethod);
                
                // if($tradeType == 'sell'){
                //     $trade->payment_id = $trade->trade->paymentMethod;
                //     $trade['payment_method'] = Paymentmethod::find($paymentMethod);
                //     $trade->payment_id = $paymentMethod;
                // } else if($tradeType == 'buy'){
                //     $trade->payment_id = $request->payment_id;
                //     $trade['payment_method'] = Paymentmethod::find($request->payment_id);
                // }
                
                // $trade->save();
                
                // return $trade;
                // send notification to both agent and customer
                return get_success_response(['msg' => 'Trade created successfully', "data" => $trade]);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $trade = TradeHistory::where('tradeType', $request->tradeType)
                // ->where('currency', $request->currency)
                ->paginate(15);
            return get_success_response($trade);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
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
        if (Trade::destroy([$id])) {
            return get_success_response(['msg' => 'Trade deleted successfully']);
        }
        return get_error_response(['msg', "Unable to delete Trade"], 410);
    }

    public function init(Request $request, $tradeType)
    {
        $type = !in_array($tradeType, ['buy', 'sell']);
        return \get_error_response(['msg', 'Unknown Transaction type'], 417);
        $trade = TradeHistory::where('tradeType', '!=', $tradeType)->where('min_amount', '>=', $request->amount)->where('trade_currency', $request->currency)->orderBy('cancellation_rate', 'ASC')->get();
        // Amount to be transacted by user
         $trade->max_amount = ($trade->max_amount - $amount);
        $trade->save();
    }
    
    public function getTrade($tradeId)
    {
        $trade = TradeHistory::where('id', $tradeId)->with('payment_method', 'trade', 'user', 'agent')->first();
        $trade = to_array($trade);
        $trade['payment_method'] = Paymentmethod::find($trade['payment_id']);
        return get_success_response($trade);
    }
}
