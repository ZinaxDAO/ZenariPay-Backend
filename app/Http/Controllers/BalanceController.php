<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource. ["USDT", "ZINA", "BUSD", "BTC"] ["USD"]
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Get all user balance
            $where['user_id'] = [];
            $balance = Balance::where($where)->get();
            if(auth('admin')->check()){
                // return all user balance
                $balance = Balance::paginate(15);
            }

            if ($balance) {
                return get_success_response($balance);
            }
            return get_error_response(['msg' => 'Error retreiving user balance'], 410);
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
        //
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
        //
    }
    
    public function balance(Request $request)
    {
        try {
            // Get all user balance
            $where['user_id'] = $request->user()->id;
            $balance = Balance::where($where)->get();
            
            if ($balance) {
                return get_success_response($balance);
            }
            return get_error_response(['msg' => 'Error retreiving user balance'], 410);
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), 500);
        }
    }
}
