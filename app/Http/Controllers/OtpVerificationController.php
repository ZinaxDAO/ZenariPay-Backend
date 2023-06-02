<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OtpModel;
use Illuminate\Http\Request;
use App\Mail\OtpVerificationMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OtpVerificationController extends Controller
{
    public static function generateOtp($email)
    {
        // $email = $request->email;
        $otp = rand(100000, 999999);
    
        $payload = [
            [
                'reference_id'  =>  _getTransactionId(),
                'destination'   =>  $email,
                'status_id'     => _getTransactionId(),
                'status'        => "OTP sent successfully."
            ]    
        ];
        OtpModel::create([
            'email'     => $email,
            'otp'       => $otp,
            'created_at'=> Carbon::now(),
            'entity'    => $payload
        ]);
    
        $send = Mail::to($email)->send(new OtpVerificationMail($otp));
        
        $response = [
          'status' => 'success',
          'code' => 200,
          'message' => 'O.T.P Sent Successfully',
          'data'    =>  ["entity" => $payload]
        ];
        
        return response()->json($response);
        
        response()->json([
            'message' => 'OTP has been sent to your email address.',
        ]);
    }
    
    public static function verifyOtp(Request $request)
    {
        $email = $request->email;
        $otp_from_user = $request->otp;
    
        $stored_otp = \DB::table('otp_verifications')
            ->where('email', $email)
            ->value('otp');
    
        if ($stored_otp == $otp_from_user) {
            DB::table('users')
                ->where('email', $email)
                ->update(['email_verified_at' => now()]);
    
            DB::table('otp_verifications')
                ->where('email', $email)
                ->delete();
    
            return get_success_response([
                'message' => 'Email has been verified successfully.',
            ]);
        } else {
            return get_error_response([
                'error' => 'Invalid OTP.',
            ], 400);
        }
    }

}