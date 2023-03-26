<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return get_error_response($validateUser->errors(), 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return get_error_response([
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if (null == $user->email_verified_at) {
                return get_error_response(["message" => "Please verify your email to continue"], 401);
            }
            $auth_token = explode('|', $user->api_token)[1];
            return response()->json([
                'status'    => true,
                'message'   => 'User Logged In Successfully',
                'token'     => $user->api_token
            ], 200);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'businessName'  =>  'required',
                    'email'         =>  'required|email|unique:users,email',
                    'password'      =>  'required',
                    'country'       =>  'required',
                    'firstName'     =>  'required',
                    'lastName'      =>  'required',
                    'phoneNumber'   =>  'required',
                    'businessType'  =>  'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                "businessName"  =>  $request->businessName,
                "businessType"  =>  $request->businessType,
                "country"       =>  $request->country,
                "firstName"     =>  $request->firstName,
                "lastName"      =>  $request->lastName,
                "email"         =>  $request->email,
                "phoneNumber"   =>  $request->phoneNumber,
                "password"      =>  Hash::make($request->password)
            ]);
            $apiToken = $user->createToken("API TOKEN")->plainTextToken;
            $user->api_token = $apiToken;
            $user->save();
            $this->sendMail($user->toArray());

            // add business data

            $business = Business::create([
                'user_id'       =>  $user->id,
                "businessName"  =>  $request->businessName,
                "businessType"  =>  $request->businessType,
                "businessPhone" =>  $request->phoneNumber,
                "businessEmail" =>  $request->email,
            ]);

            // fire event for successful registration and send mail

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'data'    => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        if ($request->has('phone') && (NULL !== $request->phone)) {
            $_userData['phoneNumber']  = $request->phone;
        }

        if ($request->has('password') && (NULL !== $request->password)) {
            $_userData['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('image') && (NULL !== $request->image)) {
            $img = save_image('profile', $request->image);
            $_userData['profile_image'] = url($img);
        }

        if ($request->has('two_fa') && (NULL !== $request->two_fa)) {
            $_userData['twoFactor'] = boolval($request->two_fa);
        }

        if (empty($_userData)) {
            return get_error_response(["error" => "No data was passed"]);
        }

        $query = ['id' => auth()->id()];
        if ($user = User::where($query)->update($_userData)) {
            return response()->json([
                'status' => true,
                'code'   => http_response_code(),
                'message' => "Profile information updated successfully",
                'data'   => getUsers()
            ]);
        } else {
            return get_error_response($user);
        }
    }

    /**
     * Receive user email address and generate reset token
     */
    public function resend_verification_email(Request $request)
    {
        $user = User::where(['email' => $request->email, 'email_verified_at' => null])->first();
        if (!$user) {
            return get_error_response(['msg' => 'Invalid data supplied or Email already verified.'], 400);
        }
        return $this->sendMail($user->toArray());
    }

    /**
     * Verify if reset token is valid and reset user password
     */
    public function verify_email(Request $request)
    {
        $this->validate($request, [
            'code'  =>  'required|min:6|max:6',
            'email' =>  'required'
        ]);

        $data = DB::table('reg_codes')->where(['code' => $request->code])->first();

        if ($data) {
            if ($request->email == $data->email) {
                DB::table('users')->where(['email' => $data->email])->update([
                    'email_verified_at' => Carbon::now()
                ]);
                //Toastr::success('Password reset successfully.');
                DB::table('reg_codes')->where('email', $request->email)->delete();
                return get_success_response(['message' => 'Email verified successfully.'], 200);
            }
            return get_error_response(['errors' => [
                ['code' => 'mismatch', 'message' => 'Invalid token supplied.']
            ]], 401);
        }
        return get_error_response(['msg' => 'Invalid token supplied.'], 400);
    }


    public function transaction_pin(Request $request, $action = NULL)
    {
        $user = User::find(auth()->id());

        if ($request->has('pin') && strlen($request->pin) != 4) {
            return response()->json(get_error_response("Error PIN can not be longer than 4 digits"))->original;
        }
        if ($action == 'verify') {
            $incomingPin = (int)$request->pin;
            $oldPin = $user->transaction_pin;
            if (Hash::check($incomingPin, $oldPin)) {
                return response()->json([
                    'status' => true,
                    'code'   => http_response_code(),
                    'message' => "Transaction verified successfully",
                    'data'   => ["msg" => "Pin verified successfully"]
                ]);
            } else {
                return get_error_response("Error, Invalid Pin Provided!");
            }
        }
        if ($action == 'update') {
            $incomingPin = (int)$request->pin;
            $user->is_pin_set = 1;
            $user->transaction_pin = bcrypt($incomingPin);
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'code'   => http_response_code(),
                    'message' => "Transaction Pin updated successfully",
                    'data'   => ["msg" => "Pin Updated successfully"]
                ]);
            } else {
                $err = "We're currenctly unable to update your transaction pin please try again later";
                return response()->json(get_error_response($err));
            }
        }
        return response()->json(get_error_response("Unknown action requested. Check your request endpoint please."));
    }

    public static function sendMail(array $customer)
    {
        if (is_array($customer)) {
            $token = rand(100001, 999999);
            $user = User::find($customer['id']);
            DB::table('reg_codes')->insert([
                'email' => $customer['email'],
                'code'  => $token,
                'created_at' => now(),
            ]);
            $msg  = [
                'user'  =>  $customer['id'],
                'name'  =>  $customer['firstName'],
                'title' =>  'Welcome to ZeenahPay',
                'body'  =>  "Please use this code to complete your registration: $token"
            ];
            $send = $user->notify(new Registration($msg));
            return get_success_response(['message' => 'Please check your email for your verification OTP.'], 200);
        }
        return get_error_response(['errors' => [
            ['code' => 'not-found', 'message' => 'failed!']
        ]], 404);
    }
}
