<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $links = Product::where('user_id', auth('sanctum')->id())->get()->makeHidden(['user_id', 'created_at', 'updated_at', 'deleted_at']);
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
        try {
            // validation firstly

            $this->validate($request, [
                'product_name'          =>  'required',
                'product_currency'      =>  'required',
                'product_price'         =>  'required',
                'product_description'   =>  'required',
            ]);
            $data = [
                'user_id'               =>  auth('sanctum')->id(),
                'product_name'          =>  $request->product_name ?? NULL,
                'product_currency'      =>  $request->product_currency ?? NULL,
                'product_price'         =>  $request->product_price ?? NULL,
                'product_social'        =>  $request->product_social ?? NULL,
                'product_description'   =>  $request->product_description ?? NULL,
            ];
            if($request->hasFile('product_image')){
                $data['product_image']  =  save_image('products', $request->file('product_image'));
            }
            $save = Product::create($data);
            if ($save) {
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
        //
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
            if(Product::destroy([$id])){
                return get_success_response([
                    'msg' => 'Product deleted successfully'
                ]);
            }
            return get_error_response([
                'msg'   =>  'Product not found'
            ], 404);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
}
