<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeHistory extends Model
{
    use HasFactory;

    // public function payment_method($id){
    //     return Paymentmethod::whereId($id)->first();
    // }
    
    public function payment_method()
    {
        return $this->belongsTo(Paymentmethod::class);
    }

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
    
    public function agent()
    {
        return $this->belongsTo(User::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
