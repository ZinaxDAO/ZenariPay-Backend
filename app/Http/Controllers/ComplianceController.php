<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplianceController extends Controller
{
    public function identity(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request, [
                'nationality'   =>  'required',
                'idType'        =>  'required',
                'id_front'      =>  'required',
                'id_back'       =>  'required',
                'selfie_image'  =>  'required'
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::create([
                'user_id'       =>  $request->user()->id,
                'nationality'   =>  $request->nationality,
                'idType'        =>  $request->idType,
                'id_front'      =>  $request->id_front,
                'id_back'       =>  $request->id_back,
                'selfie_image'  =>  $request->selfie_image
            ]);
            if ($save) {
                return get_success_response(['msg' => 'Processing, we would revert back shortly']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }


    public function address(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request, [
                'zipcode'       =>  'required',
                'utilityType'   =>  'required',
                'utility_image' =>  'required'
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->zipcode       =  $request->zipcode;
            $save->utilityType   =  $request->utilityType;
            $save->utility_image =  $request->utility_image;

            if ($save->save()) {
                return get_success_response(['msg' => 'Address updated successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }


    public function directors(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request, [
                'director_1' =>  'required',
                'director_2' =>  'required',
                'director_3' =>  'required',
                'd_image_1'  =>  'required',
                'd_image_2'  =>  'required',
                'd_image_3'  =>  'required',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->director_1    =  $request->director_1;
            $save->director_2    =  $request->director_2 ?? null;
            $save->director_3    =  $request->director_3 ?? null;
            $save->d_image_1     =  $request->d_image_1;
            $save->d_image_2     =  $request->d_image_2 ?? null;
            $save->d_image_3     =  $request->d_image_3 ?? null;
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Directors added successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }


    public function share_holders(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request, [
                'share_holder_1'    =>  'required',
                'share_holder_2'    =>  'required',
                'share_holder_3'    =>  'required',
                'd_share_holder_1'  =>  'required',
                'd_share_holder_2'  =>  'required',
                'd_share_holder_3'  =>  'required',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->share_holder_1    =  $request->director_1;
            $save->share_holder_2    =  $request->director_2 ?? null;
            $save->share_holder_3    =  $request->director_3 ?? null;
            $save->d_share_holder_1  =  $request->d_image_1;
            $save->d_share_holder_2  =  $request->d_image_2 ?? null;
            $save->d_share_holder_3  =  $request->d_image_3 ?? null;
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Directors added successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }


    public function business_docs(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request, [
                'incoporation'      =>  'required',
                'address_proof'     =>  'required',
                'business_license'  =>  'required',
                'tax_id'            =>  'required',
                'aml_policy'        =>  'required',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->incoporation      =  $request->incoporation;
            $save->address_proof     =  $request->address_proof;
            $save->business_license  =  $request->business_license;
            $save->tax_id            =  $request->tax_id;
            $save->aml_policy        =  $request->aml_policy;
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Directors added successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }
}
