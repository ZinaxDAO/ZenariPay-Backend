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
            $get = Order::where('user_id', auth('sanctum')->id())->paginate();
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

            $fees = get_fees($request->coin, $request->amount, $request->fiat);
            $apiRequest = GetDepositAddress::getRandomDepositWallet($request->coin);
            if(empty($apiRequest) OR $apiRequest == false):
                return get_error_response(['msg' => 'Coin currently not available/Supported'], 404);
            endif;

            // TRasnsaction data
            $merchant = auth('sanctum')->id();
            $data = [
                'user_id'                   =>  $merchant,
                'address'                   =>  $apiRequest,
                "reference"                 =>  _getTransactionId(),
                'customerName'              =>  $request->customer_name,
                'customerEmail'             =>  $request->customer_email,
                'coin'                      =>  $request->coin,
                'currency'                  =>  $request->currency,
                'fiatAmount'                =>  $request->amount,
                'cryptoAmount'              =>  $fees['cryptoAmount'],
                'feeInCrypto'               =>  $fees['feeInCrypto'],
            ];    

            // add customer to customer DB
            $customer = new Customers();
                
            $customer->user_id          = $merchant;
            $customer->customer_name    = $request->customer_name;
            $customer->customer_email   = $request->customer_email;
            $customer->save();

            // add order to database
            $orderRequest = Order::create($data);

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