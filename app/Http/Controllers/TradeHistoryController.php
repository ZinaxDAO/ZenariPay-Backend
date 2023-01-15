<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Trade;
use App\Models\TradeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TradeHistoryController extends Controller
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
            $trade = TradeHistory::where('user_id', $request->user()->id)->paginate(15);
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
            $validateUser = Validator::make(
                $request->all(),
                [
                    'trade_id'      =>  'required',
                    'trade_amount'  =>  'required',
                    'trade_currency' =>  'required',
                    'payment_id'    =>  'required',
                    'trade_type'    =>  'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 417);
            }


            $tradeType = $request->trade_type;

            $type = !in_array($tradeType, ['buy', 'sell']);
            return \get_error_response(['msg', 'Unknown Transaction type'], 417);
            $where = [
                'tradeType'     =>  $tradeType,
                'trade_currency' =>  $request->trade_currency
            ];
            $trade = Trade::where($where)->where('min_amount', '>=', $request->trade_amount)->where('max_amount', '<=', $request->trade_amount)->orderBy('cancellation_rate', 'ASC')->first();

            // create a new Buy Trade
            $trade = new TradeHistory();
            $trade->user_id           = auth('sanctum')->id();
            $trade->trade_id          = $trade->id;
            $trade->trade_status      = 'PENDING';
            $trade->trade_amount      = $request->trade_amount;
            $trade->transaction_id    = _getTransactionId();
            $trade->trade_currency    = $trade->currency;
            $trade->payment_id        = $request->payment_id;
            $trade->trade_type        = $request->trade_type;


            if ($trade->save()) {
                // get order joined with payment data
                $trade = TradeHistory::where('id', $trade->id)->with('payment_method')->first();
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
        $trade = TradeHistory::where('tradeType', $tradeType)->where('min_amount', '>=', $request->amount)->where('trade_currency', $request->currency)->orderBy('cancellation_rate', 'ASC')->get();
        // Amount to be transacted by user
        $amount = '';
    }
}
