<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Spatie\WebhookServer\WebhookCall;

class SendWebHookController extends Controller
{
    public static function send_webhook($txnId, $type, $payload, $method = 'crypto')
    {
        $getty = self::get_data($type, $txnId);
        if(empty($getty)){
            exit;
        }
        $data = [
            'id'        =>  $getty->id,
            'status'    =>  $payload['data']['status'],
            'currency'  =>  $getty->currency,
            'amount'    =>  $payload['data']['amount'],
            'method'    =>  $method
        ];
        $user = User::find($getty->user_id);
        $send = ['not_webhook_url' => $user];
        if(!empty($user->webhook_url)){
            $send = WebhookCall::create()->url($user->webhook_url)->payload($data)->useSecret('sign-using-this-secret')->dispatch();
        } else {
            error_log('webhook_url is empty');
        }
        // error_log(json_encode(['send_webhook' => $send]));
        return $send;
    }
    
    private static function get_data($type, $txnId)
    {
        $result = [];
        if($type == 'deposit'){
            $result = Deposit::find($txnId);
        } else if($type == 'order'){
            $result = Order::find($txnId);
        } 
        return $result;
    }
}
