<?php

namespace App\Http\Controllers;

use App\Models\Paymentmethod;
use App\Models\Curr_requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     * Get Payment method for users
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $getMethods = Paymentmethod::where('user_id', $request->user()->id)->get();
            return get_success_response($getMethods);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sys_currencies(Request $request)
    {
        try {
            $where = [];
            if($request->has('currency') && !empty($request->input('currency'))):
                $where['currency'] = $request->currency;
                $getMethods = Curr_requirement::where($where)->get()->makeHidden(['created_at', 'updated_at', 'deleted_at']);
                return get_success_response($getMethods);
            else:
                $getMethods = Curr_requirement::get()->makeHidden(['created_at', 'updated_at', 'deleted_at']);
                return get_success_response($getMethods);
            endif;
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //validate data
            // $validateUser = Validator::make($request->all(), 
            // [
            //     'currency'  =>  'required',
            // ]);

            // if($validateUser->fails()){
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'validation error',
            //         'errors' => $validateUser->errors()
            //     ], 400);
            // }

            $payment_info = [];
            foreach ($request->post() as $key => $v) {
                $payment_info[$key] = $v;
            }
            // create new payment method for user
            $method = new Paymentmethod();
            $method->user_id        = $request->user()->id;
            $method->currency       = $request->currency;
            $method->method_name    = $request->method_name;
            $method->payment_info   = $payment_info;
            if ($method->save()) {
                return get_success_response(['msg' => 'Payment method added successfull']);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //validate data

            $method = Paymentmethod::whereId($id);
            if ($method) {
                return get_success_response($method);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            //validate data

            $payment_info = [];
            foreach ($request->post() as $key => $v) {
                $payment_info[$key] = $v;
            }
            // create new payment method for user
            $method = new Paymentmethod();
            $method->user_id = $request->user()->id;
            $method->method_name = $request->method_name;
            $method->payment_info = $payment_info;
            if ($method->save()) {
                return get_success_response(['msg' => 'Payment method added successfull']);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $method = Paymentmethod::whereId($id);
            if ($method->delete()) {
                return get_success_response(['msg' => 'Payment method deleted successfully']);
            }
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
}
