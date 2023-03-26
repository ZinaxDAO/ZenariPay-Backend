<?php

use App\Models\WebSettings;
use App\Models\Balance;
use App\Models\User;
use App\Models\Rate;
use Illuminate\Support\Facades\Http;


if (!function_exists('get_error_response')) {
    /**
     * Return success response for the requested action
     * @param boolen status false
     * @param string Error message
     * @param array error response
     */
    function get_error_response($msg, $code = 400)
    {
        $msg = [
            'status' => false,
            'message' => 'Please check your request',
            'errors' => $msg
        ];
        return response()->json($msg, $code);
    }
}



if (!function_exists('getUser')) {
    function getUsers()
    {
        $users = User::where('id', auth()->id())->first();
        return $users;
    }
}

if (!function_exists('save_db_rate')) {
   /*
    * Convert fee to BTC
    * @param string currency
    * @param float-decimal amount
    */
   function save_db_rate($fromCurrency, $toCurrency, $amount=null)
   {
        $request = file_get_contents("https://min-api.cryptocompare.com/data/price?fsym=$fromCurrency&tsyms=$toCurrency");
        
        // return $url = to_array($request);
        $result = $url[$toCurrency];
        if(!in_array($toCurrency, $url)){
            return 0;
        }
        if($amount != null){
            $result = $result * $amount;
        }
        
        return $result;
   }
}

if (!function_exists('getExchangeVal')) {
   /*
    * Convert fee to BTC
    * @param string currency
    * @param float-decimal amount
    */
   function getExchangeVal($fromCurrency, $toCurrency, $amount=null)
   {
        $request = Rate::where(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency])->first();
        $result = 0;
        if(!empty($rate)){
            $rate = to_array($request);
            if(!in_array($toCurrency, $rate)){
                return 0;
            }
            $result = $rate[$toCurrency];
            if($amount != null){
                $result = $result * $amount;
            }
        }
        
        return $result;
   }
}

if (!function_exists('addOrderToDB')) {
    /**
     * Return success response for the requested action
     * @param boolen status true
     * @param string message
     * @param array data response
     */
    function addOrderToDB($data)
    {
        $msg = [
            'status'    => true,
            'message'   => 'Request successful',
            'data'      => $data
        ];
        return response()->json($msg, 200);
    }
}

if (!function_exists('get_success_response')) {
    /**
     * Return success response for the requested action
     * @param boolen status true
     * @param string message
     * @param array data response
     */
    function get_success_response($msg)
    {
        $msg = [
            'status'    => true,
            'message'   => 'Request successful',
            'data'      => $msg
        ];
        return response()->json($msg, 200);
    }
}

if (!function_exists('to_array')) {
    /**
     * convert object to array
     */
    function to_array($data) : array
    {
        if (is_array($data)) {
            return $data;
        } else if (is_object($data)) {
            return json_decode(json_encode($data), true);
        } else {
            return json_decode($data, true);
        }
    }
}

if (!function_exists('result')) {
    /**
     * convert object to array
     */
    function result($data) : array
    {
        if (is_array($data)) {
            return $data;
        } else if (is_object($data)) {
            return json_decode(json_encode($data), true);
        } else {
            return json_decode($data, true);
        }
    }
}

if (!function_exists('_getTransactionId')) {
    /**
     * return uuid()
     */
    function _getTransactionId()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

if (!function_exists('save_image')) {
    function save_image($path, $image)
    {
        $fileOrignalName = $image->getClientOriginalName();
        $image_path = '/storage/app/public/' . $path;
        // if(!is_dir($image_path)){
        //       mkdir($image_path);
        // }
        $path = public_path($image_path);
        $filename = sha1(time()) . '.jpg';
        $image->move($path, $filename);
        $paths = $image_path . '/' . $filename;
        return asset($paths);
    }
}

if (!function_exists('get_fees')) {
    /**
     * @param string crypto #Ex: BUSD
     * @param string|float|int amount
     * @param string fiat #Ex:  USD
     */
    function get_fees($coin, $amount, $fiat)
    {
        //convert amount to crypto and calculate the fee
        try { 
            return [
                'cryptoAmount'  =>  $amount,
                'feeInCrypto'   =>  0,
            ];
            $fee = 0;
            $gas_fee = settings('gas_fee');
            $calculateCryptoRate = app('bitpowr');
            $calculateCryptoRate = $calculateCryptoRate->marketPrice($fiat);
            $cryptoAmount = $calculateCryptoRate[$coin]*$amount;
            if(!empty($gas_fee)){
                $fee = (($gas_fee->value / 100 ) * $cryptoAmount);
            }
            // $feeInCrypto = $cryptoAmount;
            return [
                'cryptoAmount'  =>  $cryptoAmount,
                'feeInCrypto'   =>  $fee,
            ];
        } catch (\Throwable $th) {
            echo get_error_response($th->getMessage(), 500); exit;
        }
    }
}

if (!function_exists('decode_status')) {
    function decode_status($status)
    {
        switch ($status) {
            case 1:
                return 'success';
                break;
            
            default:
                return "pending";
                break;
        }
    }
}

if (!function_exists('settings')) {
    /**
     * Gera a paginação dos itens de um array ou collection.
     *
     * @param array|Collection      $items
     * @param int   $perPage
     * @param int  $page
     * @param array $options
     *
     * @return Strings
     */
    function settings(string $key):string
    {
        $setting = WebSettings::where('key', $key)->first();
        if (!empty($setting)) {
            $setting = $setting->value;
        } else {
            return "$key not Found!";
        }

        return $setting;
    }
}

if (!function_exists('per_page')) {
    /**
     * Gera a paginação dos itens de um array ou collection.
     *
     * @param array|Collection      $items
     * @param int   $perPage
     * @param int  $page
     * @param array $options
     *
     * @return Strings
     */
    function per_page($per_page = 5):string
    {
        return $per_page;
    }
}

if (!function_exists('slugify')) {
    /**
     * Gera a paginação dos itens de um array ou collection.
     *
     * @param array|Collection      $items
     * @param int   $perPage
     * @param int  $page
     * @param array $options
     *
     */
    function slugify(string $title):string
    {
        return \Str::slug($title).Str::random(4);
    }
}

if (!function_exists('get_commision')) {
    /* 
     * @param array $options
     *
     */
    function get_commision($amount, $percentage)
    {
        $commission = (($amount / 100) * $percentage);
        return $commission;
    }
}

if (!function_exists('balanceTopup')) {
    /**
     * Charge user wallet balance
     * @param amount, 
     * @param string currency
     * @param optional $user_id
     */

    function balanceTopup($amount, $currency, $user_id = NULL)
    {
        $uid = $user_id ?? auth()->id();
        $currency = $currency ?? 'USD';
        $user = Balance::where(['user_id' => $uid, 'code' => $currency])->first();
        if($user) :
            $user->balance = ($user->balance + $amount);
            if ($user->save()) :
                TransactionModel::insert([
                    'user_id'   => $uid,
                    'type'      => 'credit',
                    'action'    => 'topup',
                    'amount'    => $amount,
                    'fee'       => 0,
                    'receiver'  => 'self',
                    'rate'      => 0,
                    'reference' => session()->get('tranx_id') ?? _getTransactionId(),
                    'details'   => json_encode($user)
                ]);
                return true;
            endif;
        endif;
        return false;
    }
}

if (!function_exists('validate_otp')) {
    function validate_otp($code, $reference_id)
    {
        $ch = curl_init();
        $url = getenv('DOJA_URL')."/messaging/otp/validate?code=$code&reference_id=$reference_id";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $headers = array();
        $headers[] = 'Appid: ' . getenv("DOJA_APP_ID");
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Authorization: ' . getenv("DOJA_PRIVATE_KEY");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = result(curl_exec($ch));
        return $result;
        curl_close($ch);
    }
}