<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Order;
use App\Models\User;
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
        Log::info($payload);
        error_log(json_encode($payload));
        if ($request->post()) {
            if (is_array($payload)) {
                if($payload["event"] == "transaction.incoming"):
                    $data = $payload["data"];
                    $paymentAddress = $data['address'];
                    // check if address exists in deposit model
                    $checkDeposit = Deposit::where(['deposit_status' => 'pending', 'wallet_address' => $paymentAddress])->orderBy('created_at', 'desc')->first();
                    if(!empty($checkDeposit)){
                        // process wallet topup
                        self::processTopUp($checkDeposit->id, $data);
                    }
                    
                    $checkCustomerPayment = Order::where(['status' => 'pending', 'address' => $paymentAddress])->orderBy('created_at', 'desc')->first();
                    if(!empty($checkDeposit)){
                        // process wallet topup
                        self::processCheckout($checkDeposit->id, $data);
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
        $deposit = Deposit::find($id);
        $deposit->deposit_status = "success";
        $deposit->save();
        self::topup($deposit->user_id, $data['amount'], $data['assetType']);
    }
    
    private function processCheckout($id, $data): void
    {
        $checkout = Order::find($id);
        if($data['amount'] >= $checkout->cryptoAmount){
            $checkout->status = "completed";
        } else {
            $checkout->status = "processing";
        }
        $checkout->received_payment = $data['amount'];
        $checkout->save();
        self::topup($checkout->user_id, $data['amount'], $data['assetType']);
    }
    
    private function topup($userId, $amount): void
    {
        $wallet = Balance::where(["user_id" => $userId, "ticker_name" => $wallet])->first();
        $wallet->wallet = $wallet->wallet + $amount;
        $wallet->save();
    }
}