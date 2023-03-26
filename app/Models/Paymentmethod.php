<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paymentmethod extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'payment_info'  =>  'array',
        'currency'      =>  'string'
    ];
    
    protected $guarded = [];
    
    protected $hidden = [
        "user_id",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
