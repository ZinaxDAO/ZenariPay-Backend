<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return get_error_response($validateUser->errors(), 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return get_error_response([
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
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
            $validateUser = Validator::make($request->all(), 
            [
                'businessName'  =>  'required',
                'email'         =>  'required|email|unique:users,email',
                'password'      =>  'required',
                'country'       =>  'required',
                'firstName'     =>  'required',
                'lastName'      =>  'required',
                'phoneNumber'   =>  'required',
                'businessType'  =>  'required',
            ]);

            if($validateUser->fails()){
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

            // add business data

            $business = Business::create([
                'user_id'       =>  $user->id,
                "businessName"  =>  $request->businessName,
                "businessType"  =>  $request->businessType,
                "businessPhone" =>  $request->phoneNumber,
                "businessEmail" =>  $request->email,
            ]);

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

    /**
     * Receive user email address and generate reset token
     */
    public function forgot_password()
    {
        
    }

    /**
     * Verify if reset token is valid and reset user password
     */
    public function reset_password()
    {
        //
    }
}
