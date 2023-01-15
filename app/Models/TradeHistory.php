<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeHistory extends Model
{
    use HasFactory;

    public function payment_method(){
        return $this->belongsTo(PaymentMethodData::class, 'payment_id', 'paymentMethod_id');
    }

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}
