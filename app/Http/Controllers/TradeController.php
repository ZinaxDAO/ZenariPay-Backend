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
    public function index()
    {
        // Grab list of all Buy Trades active and non active
        try {
            $trade = Trade::where('user_id', $request->user()->id)->paginate(15);
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
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'min_amount'    =>  'required',
                'max_amount'    =>  'required',
                'trade_currency'=>  'required',
                'priceType'     =>  'required',
                'totalAmount'   =>  'required',
                'paymentMethod' =>  'required',
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
            $buy = new Trade();
            $buy->user_id           = auth('sanctum')->id();
            $buy->min_amount        = $request->min_amount;
            $buy->max_amount        = $request->max_amount;
            $buy->trade_currency    = $request->trade_currency;
            $buy->priceType         = $request->priceType; // enum ['float', 'fixed']
            $buy->totalAmount       = $request->totalAmount;
            $buy->paymentMethod     = $request->paymentMethod; // array of IDs
            $buy->tradeType         = $request->tradeType;
            $buy->assetName         = $request->assetName;
            $buy->fiatName          = $request->fiatName;
            $buy->marginPrice       = $request->marginPrice;
             
            if($buy->save()){
                return get_success_response(['msg' => 'New Buy Order created successfully', "data" => $buy]);
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
        $type = !in_array($tradeType, ['buy', 'sell']); return \get_error_response(['msg', 'Unknown Transaction type'], 417);
        $trade = Trade::where('tradeType', $tradeType)->where('min_amount', '>=', $request->amount)->where('trade_currency', $request->currency )->orderBy('cancellation_rate', 'ASC')->get();
        // Amount to be transacted by user
        $amount = '';
    }
}
