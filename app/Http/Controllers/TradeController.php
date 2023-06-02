<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Grab list of all Buy Trades active and non active
        try {
            $trade = Trade::where('user_id', $request->user()->id)->orderBy('created_at', 'DESC')->paginate(per_page());
            return get_success_response($trade);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted(Request $request)
    {
        // Grab list of all Buy Trades active and non active
        try {
            $trade = Trade::where('user_id', $request->user()->id)->orderBy('created_at', 'DESC')->onlyTrashed()->paginate(per_page());
            return get_success_response($trade);
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
        // return $req
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'min_amount'    =>  'required',
                'max_amount'    =>  'required',
                'trade_currency'=>  'required',
                'priceType'     =>  'required',
                'totalAmount'   =>  'required|lte:max_amount',
                'paymentMethod' =>  'required|int',
                'tradeType'     =>  'required',
                'assetName'     =>  'required',
                'fiatName'      =>  'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 417);
            }
    
            // if(get_add_balance($request->trade_currency)){
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'validation error',
            //         'errors' => [
            //             'balance' => 'Insuficient balance'
            //         ]
            //     ], 417);
            // }

            // create a new Buy Trade
            $trade = new Trade();
            $trade->user_id           = auth('sanctum')->id();
            $trade->min_amount        = $request->min_amount;
            $trade->max_amount        = $request->max_amount;
            $trade->trade_currency    = $request->trade_currency;
            $trade->priceType         = $request->priceType; // enum ['float', 'fixed']
            $trade->totalAmount       = $request->totalAmount;
            $trade->paymentMethod     = $request->paymentMethod; // array of IDs
            $trade->tradeType         = $request->tradeType;
            $trade->time_limit        = $request->time_limit;
            $trade->fiatName          = $request->fiatName;
            $trade->terms             = $request->terms;
            $trade->marginPrice       = $request->marginPrice;
             
            if($trade->save()){
                return get_success_response(['msg' => 'New Buy Order created successfully', "data" => $trade]);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $req
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'min_amount'    =>  'required',
                'max_amount'    =>  'required',
                'trade_currency'=>  'required',
                'priceType'     =>  'required',
                'totalAmount'   =>  'required|lte:max_amount',
                'paymentMethod' =>  'required|int',
                'tradeType'     =>  'required',
                'assetName'     =>  'required',
                'fiatName'      =>  'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 417);
            }

            // create a new Buy Trade
            $buy = Trade::whereId($id)->first();
            $buy->user_id           = auth('sanctum')->id();
            $buy->min_amount        = $request->min_amount;
            $buy->max_amount        = $request->max_amount;
            $buy->trade_currency    = $request->trade_currency;
            $buy->priceType         = $request->priceType; // enum ['float', 'fixed']
            $buy->totalAmount       = $request->totalAmount;
            $buy->paymentMethod     = $request->paymentMethod; // array of IDs
            $buy->tradeType         = $request->tradeType;
            $buy->time_limit        = $request->time_limit;
            $buy->fiatName          = $request->fiatName;
            $buy->terms             = $request->terms;
            $buy->marginPrice       = $request->marginPrice;
             
            if($buy->save()){
                return get_success_response(['msg' => 'Trade updated successfully', "data" => $buy]);
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
            $trade = Trade::where('tradeType', $request->tradeType)
                        ->where('user_id', $request->user()->id)
                        ->with('payment_info')
                        ->paginate(15);
            return get_success_response($trade);
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
    public function agent(Request $request)
    {
        try {
            $trade = Trade::where('agent_id', $request->user()->id)
                        ->paginate(15);
            return get_success_response($trade);
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
    public function user(Request $request)
    {
        try {
            $trade = Trade::where('user_id', $request->user()->id)
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
        $type = !in_array($tradeType, ['buy', 'sell']); return \get_error_response(['msg', 'Unknown Transaction type'], 417);
        $trade = Trade::where('tradeType', $tradeType)->where('min_amount', '>=', $request->amount)->where('trade_currency', $request->currency )->with('payment_info')->orderBy('cancellation_rate', 'ASC')->get();
        // Amount to be transacted by user
        $amount = '';
    }
}
