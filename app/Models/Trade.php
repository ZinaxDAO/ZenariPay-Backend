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
}
