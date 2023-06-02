<?php

namespace App\Http\Controllers;

use App\Models\WalletAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetDepositAddress extends Controller
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
    public function store(Request $request, $publicKey=null, $secretKey=null)
    {
        $i = 3;
        while($i < 500){ 
            $param = [            
                "label" =>  "BitPowr".uniqid(),
                "asset" =>  $request->asset ??   "USDT_BSC",
                "accountId" =>  "6e43dec6-5482-45c0-9108-10c36d0912f0", //"7df83749-c240-4303-81cd-43e7be2c3975",
            ];
            if (empty($publicKey)) {
                $publicKey = getenv('BIT_POWR_PUBLIC_KEY');
            }
            if (empty($secretKey)) {
                $secretKey = getenv('BIT_POWR_SECRET_KEY');
            }
    
            $bearerToken = base64_encode("$publicKey:$secretKey");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://developers.bitpowr.com/api/v1/addresses');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
    
            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = "Authorization: Bearer $bearerToken";
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $resp = result($result);
            WalletAddress::create([
                'currency'  =>  "USDT", //$resp['data']['assetType'],
                'wallet_address'    =>  $resp['data']['address'],
                'total_use' =>  0,
                'other' =>  $resp
            ]);
            $i++;
            // return response()->json($resp);
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
        //
    }

    /**
     * Grab a wallet address from the address list and create a new order with the address
     */
    public static function getRandomDepositWallet($currency)
    {
        $address = new WalletAddress();
        $address = WalletAddress::where('currency', $currency)->orderBy('last_used', 'ASC')->first();
        if (!empty($address)) {
            $depositAddress = $address->wallet_address;
            $address->last_used = now();
            $address->total_use = ((int)$address->total_use + 1);
            $address->save();
            return $depositAddress;
        }
        return [];
    }
}
