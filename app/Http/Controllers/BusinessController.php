<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id=NULL)
    {
        try {
            // return auth('sanctum')->user();
            $business = Business::where('user_id', auth('sanctum')->id())->first();
            if ($business) {
                return get_success_response($business);
            } 
            return get_error_response(['msg' => 'Business not found'], 404);
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
    public function update(Request $request, $id=NULL)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'businessName'          =>  'required',
                'businessType'          =>  'required',
                'businessPhone'         =>  'required',
                'businessEmail'         =>  'required',
                'businessDescription'   =>  'required',
                'businessCountry'       =>  'required',
                'businessState'         =>  'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 417);
            }
            // add business data

            $business = Business::where('user_id', auth('sanctum')->id())->first();
            $business->businessName          =  $request->businessName;
            $business->businessType          =  $request->businessType; //Business Niche
            $business->businessPhone         =  $request->businessPhone;
            $business->businessEmail         =  $request->businessEmail;
            $business->businessDescription   =  $request->businessDescription;
            $business->businessCountry       =  $request->businessCountry;
            $business->businessState         =  $request->businessState;

            if($business->save()){
                return get_success_response(['Business Data Updated Successfully']);
            } else {
                return get_error_response([
                    'message' => "Unable to update Business Record"
                ], 417);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
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
        //
    }
}
