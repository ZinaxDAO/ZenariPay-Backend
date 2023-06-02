<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $raw_payload = file_get_contents('php://input');
        $payload = json_decode($raw_payload, true);
        if ($request->post()) {
            if (is_array($payload)) {
                if($payload["event"] == "transaction.incoming"):
                    $data = $payload["data"];
                    $paymentAddress = $data['address'];
                    // check if address exists in deposit model
                    $checkDeposit = Deposit::where(['deposit_status' => 'pending', 'wallet_address' => $paymentAddress])->orderBy('created_at', 'desc')->first();
                    if(!empty($checkDeposit)){
                        // process wallet topup
                        self::processTopUp($checkDeposit->id, $payload);
                        // send notification
                        // send webhook if needed
                        SendWebHookController::send_webhook($checkDeposit->id, 'deposit', $payload, 'crypto');
                    }
                    
                    $checkCustomerPayment = Order::where(['status' => 'pending', 'address' => $paymentAddress])->orderBy('created_at', 'desc')->first();
                    if(!empty($checkCustomerPayment)){
                        // process wallet topup
                        error_log(json_encode(['send_webhook' => $payload]));
                        self::processCheckout($checkCustomerPayment->id, $payload);
                        SendWebHookController::send_webhook($checkCustomerPayment->id, 'order', $payload, 'crypto');
                    }
                    
                    return http_response_code(200);
                endif;
            }
            return http_response_code(200);
        }
        return get_error_response(["error" => "Requested page does not exist.", "requestId" => _getTransactionId()]);
    }
    
    private function processTopUp($id, $data): void
    {
        $currency = $data['data']['assetType'];
        $amount = $data['data']['amount'];
        
        if($currency == "USDT_BSC"){
            $currency = "USDT";
        }
        if($currency == "BSC"){
            $currency = "BNB";
        }
        
        $deposit = Deposit::find($id);
        $deposit->deposit_status = "success";
        $save = $deposit->save();
        // self::topup($deposit->user_id, $amount, $currency);
        self::processCheckout($deposit->id, $data);
    }
    
    private function processCheckout($id, $data): void
    {
        $currency = $data['data']['assetType'];
        $amount = $data['data']['amount'];
        
        if($currency == "USDT_BSC"){
            $currency = "USDT";
        }
        if($currency == "BSC"){
            $currency = "BNB";
        }
        
        $checkout = Order::find($id);
        $checkout->status = "completed";
        $checkout->received_payment = $amount;
        $checkout->save();
        self::topup($checkout->user_id, $amount, $currency);
    }
    
    private function topup($userId, $amount, $currency): void
    {
        $wallet = Balance::where(["user_id" => $userId, "ticker_name" => $currency])->increment('balance', $amount);
        error_log(json_encode($wallet));
    }
}