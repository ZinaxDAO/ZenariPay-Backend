<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplianceController extends Controller
{
    public function get()
    {
        try {
            $data = Compliance::where('user_id', request()->user()->id)->first();
            return get_success_response($data);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'fullName'              =>  'required',
                'email'                 =>  'required',
                'phone'                 =>  'required',
                'dob'                   =>  'required',
                'country_of_residence'  =>  'required',
                'state_of_residence'    =>  'required',
                'city_of_residence'     =>  'required',
                'street_address'        =>  'required',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }
            $data = Compliance::where('user_id', request()->user()->id)->count();
            if($data < 1){
                $save = Compliance::create([
                    'user_id'               =>  $request->user()->id,
                    'fullName'              =>  $request->fullName,
                    'email'                 =>  $request->email,
                    'phone'                 =>  $request->phone,
                    'dob'                   =>  $request->dob,
                    'country_of_residence'  =>  $request->country_of_residence,
                    'state_of_residence'    =>  $request->state_of_residence,
                    'city_of_residence'     =>  $request->city_of_residence,
                    'street_address'        =>  $request->street_address,
                ]);
            } else {
                // update user meta
                $bis = Compliance::where('user_id', $request->user()->id)->first();
                $bis->fullName              =  $request->fullName;
                $bis->email                 =  $request->email;
                $bis->phone                 =  $request->phone;
                $bis->dob                   =  $request->dob;
                $bis->country_of_residence  =  $request->country_of_residence;
                $bis->state_of_residence    =  $request->state_of_residence;
                $bis->city_of_residence     =  $request->city_of_residence;
                $bis->street_address        =  $request->street_address;
                $save = $bis->save();
            }
            if ($save) {
                return get_success_response(['msg' => 'Personal data Updated Successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }

    public function update(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'legal_business_name'   =>  'required',
                'operating_biss_as'     =>  'required',
                'registration_country'  =>  'required',
                'state'                 =>  'required',
                'street_address'        =>  'required',
                'business_usecase'      =>  'required',
                'website_address'       =>  'nullable|url',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }
            $data = Compliance::where('user_id', request()->user()->id)->count();
            if($data < 1){
                $save = Compliance::create([
                    'user_id'               =>  $request->user()->id,
                    'legal_business_name'   =>  $request->legal_business_name,
                    'operating_biss_as'     =>  $request->operating_biss_as,
                    'registration_country'  =>  $request->registration_country,
                    'state'                 =>  $request->state,
                    'street_address'        =>  $request->street_address,
                    'business_usecase'      =>  $request->business_usecase,
                    'website_address'       =>  $request->website_address,
                ]);
            } else {
                // update user meta
                $bis = Compliance::where('user_id', $request->user()->id)->first();
                $bis->legal_business_name   =  $request->legal_business_name;
                $bis->operating_biss_as     =  $request->operating_biss_as;
                $bis->registration_country  =  $request->registration_country;
                $bis->state                 =  $request->state;
                $bis->street_address        =  $request->street_address;
                $bis->business_usecase      =  $request->business_usecase;
                $bis->website_address       =  $request->website_address;
                $save = $bis->save();
            }
            if ($save) {
                return get_success_response(['msg' => 'Business Data Updated Successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }

    public function identity(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'nationality'   =>  'required',
                'idType'        =>  'required',
                'id_front'      =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'id_back'       =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'selfie_image'  =>  'required|mimes:jpeg,bmp,png,gif,pdf'
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }
            $data = Compliance::where('user_id', request()->user()->id)->count();
            if($data < 1){
                $save = Compliance::create([
                    'user_id'       =>  $request->user()->id,
                    'nationality'   =>  $request->nationality,
                    'idType'        =>  $request->idType,
                    'id_front'      =>  save_image('compliance', $request->id_front),
                    'id_back'       =>  save_image('compliance', $request->id_back),
                    'selfie_image'  =>  save_image('compliance', $request->selfie_image)
                ]);
            } else {
                // update user meta
                $bis = Compliance::where('user_id', $request->user()->id)->first();
                $bis->nationality   =  $request->nationality;
                $bis->idType        =  $request->idType;
                if($request->hasFile('id_front')):
                    $bis->id_front      =  save_image('compliance', $request->id_front);
                endif;
                if($request->hasFile('id_back')):
                    $bis->id_back       =  save_image('compliance', $request->id_back);
                endif;
                if($request->hasFile('selfie_image')):
                    $bis->selfie_image  =  save_image('compliance', $request->selfie_image);
                endif;
                $save = $bis->save();
            }
            if ($save) {
                return get_success_response(['msg' => 'Processing, we would revert back shortly']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }

    public function agent(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'payment_method'    =>  'required',
                'payment_channel'   =>  'required',
                'market_place_1'    =>  'required',
                'market_place_2'    =>  'required',
                'market_place_3'    =>  'required',
                'username_1'        =>  'required',
                'username_2'        =>  'required',
                'username_3'        =>  'required',
                'major_income_sources'  =>  'required',
                'expected_transaction_volume'   =>  'required',
                'exchange_statement'  =>  'required|mimes:jpeg,bmp,png,gif,pdf'
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }
            $data = Compliance::where('user_id', request()->user()->id)->first();
            if($data){
                $_save = [
                    'payment_method'        =>  $request->payment_method,
                    'payment_channel'       =>  $request->payment_channel,
                    'market_place_1'        =>  $request->market_place_1,
                    'market_place_2'        =>  $request->market_place_2,
                    'market_place_3'        =>  $request->market_place_3,
                    'username_1'            =>  $request->username_1,
                    'username_2'            =>  $request->username_2,
                    'username_3'            =>  $request->username_3,
                    'major_income_sources'  =>  $request->major_income_sources,
                    'exchange_statement'    =>  save_image('compliance', $request->exchange_statement),
                    'expected_transaction_volume'=>  $request->expected_transaction_volume,
                ];
                $save = $data->update($_save);
            }
            if ($save) {
                return get_success_response(['msg' => 'Processing, we would revert back shortly']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }


    public function address(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'zipcode'       =>  'required',
                'utilityType'   =>  'required',
                'utility_image' =>  'required|mimes:jpeg,bmp,png,gif,pdf'
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->zipcode       =  $request->zipcode;
            $save->utilityType   =  $request->utilityType;
            if($request->hasFile('utility_image')):
                $save->utility_image =  save_image('compliance', $request->utility_image);
            endif;

            if ($save->save()) {
                return get_success_response(['msg' => 'Address updated successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }


    public function directors(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'director_1' =>  'required',
                'director_2' =>  'required',
                'director_3' =>  'required',
                'd_image_1'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_image_2'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_image_3'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'share_holder_1'    =>  'required',
                'share_holder_2'    =>  'required',
                'share_holder_3'    =>  'required',
                'd_share_holder_1'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_share_holder_2'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_share_holder_3'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->director_1    =  $request->director_1;
            $save->director_2    =  $request->director_2 ?? null;
            $save->director_3    =  $request->director_3 ?? null;
            if($request->hasFile('d_image_1')):
                $save->d_image_1     =  save_image('compliance', $request->d_image_1);
            endif;
            if($request->hasFile('d_image_2')):
                $save->d_image_2     =  save_image('compliance', $request->d_image_2) ?? null;
            endif;
            if($request->hasFile('d_image_3')):
                $save->d_image_3     =  save_image('compliance', $request->d_image_3) ?? null;
            endif;
            
            // $save = Compliance::where('user_id', $request->user()->id)->first();
            
            $save->share_holder_1    =  $request->share_holder_1;
            $save->share_holder_2    =  $request->share_holder_2 ?? null;
            $save->share_holder_3    =  $request->share_holder_3 ?? null;
            if($request->has("d_share_holder_1")):
                $save->d_share_holder_1  =  save_image('compliance', $request->d_share_holder_1);
            endif;
            if($request->has("d_share_holder_2")):
                $save->d_share_holder_2  =  save_image('compliance', $request->d_share_holder_2) ?? null;
            endif;
            if($request->has("d_share_holder_3")):
                $save->d_share_holder_3  =  save_image('compliance', $request->d_share_holder_3) ?? null;
            endif;
            
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Directors added successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }


    public function share_holders(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'share_holder_1'    =>  'required',
                'share_holder_2'    =>  'required',
                'share_holder_3'    =>  'required',
                'd_share_holder_1'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_share_holder_2'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'd_share_holder_3'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            $save->share_holder_1    =  $request->share_holder_1;
            $save->share_holder_2    =  $request->share_holder_2 ?? null;
            $save->share_holder_3    =  $request->share_holder_3 ?? null;
            if($request->has("d_share_holder_1")):
                $save->d_share_holder_1  =  save_image('compliance', $request->d_share_holder_1);
            endif;
            if($request->has("d_share_holder_2")):
                $save->d_share_holder_2  =  save_image('compliance', $request->d_share_holder_2) ?? null;
            endif;
            if($request->has("d_share_holder_3")):
                $save->d_share_holder_3  =  save_image('compliance', $request->d_share_holder_3) ?? null;
            endif;
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Share Holders added successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }


    public function business_docs(Request $request)
    {
        try {
            //validate incoming requests firstly
            $validator = Validator::make($request->all(), [
                'incoporation'      =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'address_proof'     =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'business_license'  =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'tax_id'            =>  'required|mimes:jpeg,bmp,png,gif,pdf',
                'aml_policy'        =>  'required|mimes:jpeg,bmp,png,gif,pdf',
            ]);

            if ($validator->fails()) {
                return get_error_response($validator->errors(), 400);
            }

            $save = Compliance::where('user_id', $request->user()->id)->first();
            if($request->has("incoporation")):
                $save->incoporation      =  save_image('compliance', $request->incoporation);
            endif;
            if($request->has("address_proof")):
                $save->address_proof     =  save_image('compliance', $request->address_proof);
            endif;
            if($request->has("business_license")):
                $save->business_license  =  save_image('compliance', $request->business_license);
            endif;
            if($request->has("tax_id")):
                $save->tax_id            =  save_image('compliance', $request->tax_id);
            endif;
            if($request->has("aml_policy")):
                $save->aml_policy        =  save_image('compliance', $request->aml_policy);
            endif;
            
            if ($save->save()) {
                return get_success_response(['msg' => 'Business Documents submitted successfully']);
            }
            return get_error_response(['msg' => 'Unable to process your request at the moment, please try again later'], 400);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 400);
        }
    }
}
