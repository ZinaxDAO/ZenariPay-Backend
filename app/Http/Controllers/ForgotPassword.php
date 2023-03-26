<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notifications\password;

class ForgotPassword extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => error_processor($validator)], 403);
        }

        $customer = User::Where(['email' => $request['email']])->first();
        if(!empty($customer))
            return $this->sendMail($customer->toArray());
        else 
            return response()->json(['message' => 'If Email exist you should receive your reset token in 5minutes.'], 200);
    }

    public function reset_password_submit(Request $request)
    {
        $this->validate($request,[
            'reset_token'       =>  'required|min:6|max:6',
            'email'             =>  'required',
            'password'          =>  'required',
            'confirm_password'  =>  'required'
        ]);
        
        $data = DB::table('password_resets')->where(['token' => $request->reset_token])->first();
        
        if ($data) {
            if ($request->password == $request->confirm_password) {
                DB::table('users')->where(['email' => $data->email])->update([
                    'password' => bcrypt($request['confirm_password'])
                ]);
                //Toastr::success('Password reset successfully.');
                DB::table('password_resets')->where(['token' => $request['reset_token']])->delete();
                return response()->json(['message' => 'Password changed successfully.'], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'mismatch', 'message' => 'Password did,t match!']
            ]], 401);
        }
        return get_error_response(['msg' => 'Invalid token.'], 400);
    }

    public static function sendMail(array $customer)
    {
        if (is_array($customer)) {
            $token = rand(100001, 999999);
            $user = User::find($customer['id']);
            DB::table('password_resets')->insert([
                'email' => $customer['email'],
                'token' => $token,
                'created_at' => now(),
            ]);
            $msg  = [
                'user'  =>  $customer['id'],
                'name'  =>  $customer['firstName'],
                'title' =>  'Password Reset',
                'body'  =>  "Please use this code to reset your password: $token"
            ];
            $send = $user->notify(new password($msg));
            return response()->json(['message' => 'If Email exist you should receive your reset token in 5minutes.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'Email not found!']
        ]], 404);
    }
}
