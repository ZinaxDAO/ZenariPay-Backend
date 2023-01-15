<?php

use App\Models\WebSettings;


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

if (!function_exists('result')) {
    /**
     * convert object to array
     */
    function result($result) : array
    {
        if (is_array($result)) :
            return ($result);
        else :
            return json_decode($result, true);
        endif;
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
