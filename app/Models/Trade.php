<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use HasFactory, SoftDeletes;
    
    public function payment_info()
    {
        return $this->belongsTo(Paymentmethod::class, 'paymentMethod');
    }
    
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
