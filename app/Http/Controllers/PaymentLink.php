<?php

namespace App\Http\Controllers;

use App\Models\PaymentLinkButton as ModelsPaymentLinkButton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentLink extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $links = ModelsPaymentLinkButton::where('user_id', auth('sanctum')->id())->get()->makeHidden(['user_id', 'created_at', 'updated_at', 'deleted_at']);
            return get_success_response($links);
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
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'product_id'            => 'required',
                'link_title'            => 'required',
                'link_description'      =>  'required',
                'link_type'             =>  'required|in:donation,standard',
            ]);

            if($validateUser->fails()){
                return get_error_response($validateUser->errors(), 401);
            }

            $save = ModelsPaymentLinkButton::create([
                'user_id'           =>  auth('sanctum')->id(),
                'product_id'        =>  $request->product_id ?? NULL,
                'link_title'        =>  $request->link_title ?? NULL,
                'slug'              =>  slugify($request->link_title),
                'link_description'  =>  $request->link_description ?? NULL,
                'link_type'         =>  $request->link_type ?? NULL,
                'phone_number'      =>  $request->phone_number ?? NULL,
                'shipping_address'  =>  $request->shipping_address ?? NULL,
                'redirect_website'  =>  $request->redirect_website ?? NULL,
                'payment_success_msg'=>  $request->payment_success_msg ?? NULL,
            ]);
            if ($save->makeHidden(['user_id', 'created_at', 'updated_at', 'deleted_at'])) {
                return get_success_response($save);
            }
            return get_error_response($save);
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
            if($data = ModelsPaymentLinkButton::find([$id])){
                return get_success_response($data);
            }
            return get_error_response([
                'msg'   =>  'Link Not Found!'
            ], 400);
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
        //
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
            if(ModelsPaymentLinkButton::destroy([$id])){
                return get_success_response([
                    'msg' => 'Payment Link or Button deleted successfully'
                ]);
            }
            return get_error_response([
                'msg'   =>  'Unable to perform delete action'
            ]);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
}
