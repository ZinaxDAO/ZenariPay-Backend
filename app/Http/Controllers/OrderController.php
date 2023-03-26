<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->bitpowr = app('bitpowr');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_all()
    {
        try {
            $get = Order::where('user_id', auth('sanctum')->id())->orderBy('created_at', 'desc')->paginate();
            if(!empty($get)){
                $get->makeHidden(['updated_at', 'id', 'created_at', 'user_id', 'deleted_at']);
                return get_success_response($get);
            }
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
            $validateUser = Validator::make($request->all(), 
            [
                'customer_name'             =>  'required',
                'customer_email'            =>  'required',
                'coin'                      =>  'required',
                'currency'                  =>  'required',
                'amount'                    =>  'required',
            ]);

            if($validateUser->fails()){
                return get_error_response($validateUser->errors(), 400);
            }
            // add order to database
            // TRasnsaction data
            $merchant = auth('sanctum')->id();
            $data = [
                'user_id'                   =>  $merchant,
                "reference"                 =>  _getTransactionId(),
                'customer_name'             =>  $request->customer_name,
                'customer_email'            =>  $request->customer_email,
                'coin'                      =>  $request->coin,
                'currency'                  =>  $request->currency,
                'fiatAmount'                =>  $request->amount,
                'cryptoAmount'              =>  $fees['cryptoAmount'],
                'feeInCrypto'               =>  $fees['feeInCrypto'],
            ];    

            $orderRequest = $this->__processOrder($data, $merchant);

            if($orderRequest):
                // convert API Response to array if it's not in array
                $decode = result($orderRequest->makeHidden(['updated_at', 'id', 'created_at', 'user_id', 'deleted_at']));
                return get_success_response($decode);
            endif;
            return get_error_response(['msg' => 'Unable to initiate Transaction at the moment, Please try again later'], 422);
        } catch (\Throwable $th) {
            // Return server error
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    public function __processOrder($data, $sellerId=null)
    {   

        $fees = get_fees($data['coin'], $data['amount'], $data['fiat']);
        $apiRequest = GetDepositAddress::getRandomDepositWallet($data['coin']);
        if(empty($apiRequest) OR $apiRequest == false):
            return get_error_response(['msg' => 'Coin currently not available/Supported'], 404);
        endif;

        // TRasnsaction data
        $merchant = $sellerId ?? auth('sanctum')->id();
        $amountInCrypto = getExchangeVal($data['currency'], $data['coin'], $data['amount']);
        $data = [
            'user_id'                   =>  $data['user_id'],
            'address'                   =>  $apiRequest,
            "reference"                 =>  _getTransactionId(),
            'customerName'              =>  $data['customer_name'],
            'customerEmail'             =>  $data['customer_email'],
            'coin'                      =>  $data['coin'],
            'currency'                  =>  $data['currency'],
            'fiatAmount'                =>  $data['amount'],
            'cryptoAmount'              =>  number_format($amountInCrypto, 8),
            'feeInCrypto'               =>  $fees['feeInCrypto'],
        ];    

        // add customer to customer DB
        $customer = new Customers();
            
        $addr["country"]    = request()->country;
        $addr["state"]      = request()->state;
        $addr["line1"]      = request()->line1;
        $addr["line2"]      = request()->line2;
        $addr["city"]       = request()->city;
        
        $customer->user_id          = $merchant;
        $customer->customer_email   = request()->customer_email;
        $customer->customer_name    = request()->customer_name;
        $customer->customer_data    = $addr;
        $customer->save();

        // add order to database
        $orderRequest = Order::create($data);

        return $orderRequest;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($order)
    {
        try {
            $orderRequest = Order::where([
                'reference' => $order,
                'user_id' => auth('sanctum')->id()
            ])->first();
            if(!empty($result)){
                // $result = result($orderRequest);
                $order = result($orderRequest->makeHidden(['updated_at', 'id', 'created_at', 'user_id', 'deleted_at']));
                return get_success_response($order);
            }
            return get_error_response([
                'msg'   =>  'Transaction not found'
            ], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($order)
    {
        try {
            $orderRequest = Order::where([
                'reference' => $order,
                'user_id' => auth('sanctum')->id()
            ])->first();

            if(!empty($orderRequest)){
                $order = $orderRequest->makeHidden(['updated_at', 'id', 'created_at', 'user_id', 'deleted_at']);
                return get_success_response([
                    'status' => ($order['status'])
                ]);
            }
            
            return get_error_response([
                'msg'   =>  "Transaction not found"
            ], 404);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($order, $id=NULL)
    {
        try {
            $orderRequest = Order::where([
                'reference' => $order,
                'user_id' => auth('sanctum')->id()
            ])->first();
            
            if(!empty($orderRequest)){
                $order->status = request()->status;
                $order->save();
                return get_success_response($order);
            }
            return get_error_response([
                'msg'   =>  'Transaction not found'
            ], 400);
        } catch (\Throwable $th) {
            $code = 400;
            if($th->getCode() > 100 && $th->getCode() < 510){
                $code = $th->getCode();
            }
            return get_error_response($th->getMessage(), $code);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try {
            if ($order->delete()) {
                return get_success_response(['msg' => "Transaction deleted successfully"]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}