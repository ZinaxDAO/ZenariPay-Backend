<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Balance;
use Closure;
use Illuminate\Http\Request;

class DebitWallet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->has('amount') && $request->has('wallet_type')){
            $user = User::findorFail(auth()->id());

            $where['user_id'] = $request->user()->id;
            $where['ticker_name']  =   $request->wallet_type;
            $balance = Balance::where($where)->first();
            
            $balance->increment('balance', $amountInCrypto);
            $balance->save();
        }
        return $next($request);
    }
}
