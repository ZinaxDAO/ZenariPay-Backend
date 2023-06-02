<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Trade;
use App\Models\PaymentLinkButton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function getPaymentData($slug)
    {
        try {
            $model = new PaymentLinkButton();
            $data = $model->findBySlug($slug);
            if($data){
                return get_success_response($data);
            }
            return get_error_response([
                'msg'   =>  'Link Not Found!'
            ], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
            return ["status" => false];
        }
    }

    public function process(Request $request, $slug)
    {
        $validateUser = Validator::make($request->all(), 
        [
            'customer_name'     =>  'required',
            'customer_email'    =>  'required',
            'coin'              =>  'required',
            'currency'          =>  'required',
            'quantity'          =>  'required',
            'payment_method'    =>  'required',
        ]);

        if($validateUser->fails()){
            return get_error_response($validateUser->errors(), 400);
        }
        
        try {
            $model = new PaymentLinkButton();
            if($request->post()){
                $product_id         = $request->product_id;
                $payment_method     = $request->payment_method;
                $payment_currency   = $request->coin;
                
                $link = $model->findBySlug($slug);
                $pro = Product::find($link->product_id);
                if($link->link_type == "standard"):
                    $amount = $pro->product_price;
                else:
                    $amount = $request->donate_amount;
                endif;
                $currency   = $pro->product_currency;
                // match custumer to agent for P2P
                if($payment_method == 'agent') {
                    return $trade = Trade::where('tradeType', "sell")
                                ->where('min_amount', '>=', $request->amount)
                                ->where('max_amount', '<=', $request->amount)
                                ->where('trade_currency', $request->currency)
                                ->with('payment_info')
                                ->orderBy('cancellation_rate', 'ASC')
                                ->first();
                    //create tradehistory then return trade
                    
                    // update trade max amount will be refunded if trade got cancelled or timeout;
                    $trade->max_amount = ($trade->max_amount - $amount);
                    $trade->save();
                    $order = $trade;
                } else {
                    // payment method is crypto fiat
                    $data = [
                        'user_id'       =>  $link->user_id,
                        'fiat'          =>  $currency,
                        'coin'          =>  $payment_currency,
                        'amount'        =>  ($amount * $request->quantity),
                        'currency'      =>  $currency,
                        'customer_email'=>  $request->customer_email,
                        'customer_name' =>  $request->customer_name,
                    ];
                    
                    $order = (new OrderController())->__processOrder($data, $link->user_id);
                }
                if(isset($order) && isset($order['address'])) {
                    return get_success_response($order);
                }
                return $order;
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    public function mobile(Request $request, $checkoutType=null)
    {
        $validateUser = Validator::make($request->all(), 
        [
            'customer_name'     =>  'required',
            'customer_email'    =>  'required',
            'coin'              =>  'required',
            'currency'          =>  'required',
            'amount'            =>  'required',
        ]);

        if($validateUser->fails()){
            return get_error_response($validateUser->errors(), 400);
        }
        
        // if($checkoutType == 'crypto'){
            // return crypto data for checkout.
            return $this->mobile_crypto($request->only(['currency','coin', 'amount', 'customer_email', 'customer_name']));
        // }
        
        return get_error_response(["error" => "Unknown payment method"], 417);
    }
    
    public function mobile_crypto($data)
    {
        // payment method is crypto fiat
        $user = auth()->user();
        $params = [
            'user_id'       =>  $user->id,
            'fiat'          =>  $data['currency'],
            'coin'          =>  $data['coin'],
            'amount'        =>  $data['amount'],
            'currency'      =>  $data['currency'],
            'customer_email'=>  $data['customer_email'],
            'customer_name' =>  $data['customer_name'],
        ];
        
        $order = (new OrderController())->__processOrder($params, $user->id);
        $_amt = $data['amount'];
        if($data['coin'] == "BTC" OR $data['coin'] == "BCH"):
            $order['qr_code'] = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=bitcoin:$order->address&amount=$_amt&choe=UTF-8";
        else:
            $order['qr_code'] = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$order->address&amount=$_amt&choe=UTF-8";
        endif;
        return response()->json($order);
    }
    
    private function mobile_agent($data)
    {
        // payment method is crypto fiat
        $user = auth()->user();
        $params = [
            'user_id'       =>  $user->id,
            'fiat'          =>  $data['currency'],
            'coin'          =>  $data['coin'],
            'amount'        =>  $data['amount'],
            'currency'      =>  $data['currency'],
            'customer_email'=>  $data['customer_email'],
            'customer_name' =>  $data['customer_name'],
        ];
        
        $order = (new OrderController())->__processOrder($params, $user->id);
        return response()->json($order);
    }
    
    
    public function getStatus($id)
    {
        $order = Order::whereId($id)->first();
        if(!$order) return get_error_response(['error' => "Checkout with the provided data not found."], 404);
        return get_success_response(['status' => $order->status]);
    }
    
}