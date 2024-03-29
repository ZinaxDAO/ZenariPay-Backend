<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verifcation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DojaController extends Controller
{
    public function send_otp(Request $request)
    {
        return OtpVerificationController::generateOtp($request->email);
        $request->validate([
            'phone' => 'required|numeric',
        ], [
            'phone.required' => 'Phone Number is required!',
            'phone.numeric' => 'Invalid Phone Number provided!',
        ]);

        $resp = [
            'channel'       => 'whatsapp',
            'sender_id'     => getenv("DOJA_SENDER_ID"), //$request->sender_id,
            'destination'   => $request->phone
        ];

        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('DOJA_URL') . "messaging/otp");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($resp));
        $headers = array();
        $headers[] = 'Appid: ' . getenv("DOJA_APP_ID");
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Authorization: ' . getenv("DOJA_PRIVATE_KEY");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = result(curl_exec($ch));
        // return response()->json($result);

        if (array_key_exists('entity', $result) && !empty($result)) {
            $data = [
                'status'    => 'success',
                'code'      =>  http_response_code(),
                'message'   =>  "O.T.P Sent Successfully",
                'data'      =>  result($result)
            ];
        } else {
            $data = get_error_response($result, 400);
        }
        curl_close($ch);
        return response()->json($data);
    }

    public function validate_otp(Request $request)
    {
        $request->validate([
            'reference_id' => 'required',
            'code'  =>  'required'
        ]);

        $data = [];
        $resp = [
            'code'          => $request->code,
            'reference_id'  => $request->reference_id
        ];
        
		$result = validate_otp($request->code, $request->reference_id);

        if (!empty($result) && array_key_exists('valid', $result['entity'])) {
            $mainResult = result($result['entity']);
            if($mainResult['valid'] == true){
                $data = [
                    'status'    => 'success',
                    'code'      =>  http_response_code(),
                    'message'   =>  "O.T.P verified Successfully",
                    'data'      =>  result($result)
                ];
                return response()->json($data);
            } else {
                $data = get_error_response($result, 400)->original;
            }
        } else {
            $data = get_error_response($result, 400)->original;
        }

        return response()->json($data, 404);
    }
    
}