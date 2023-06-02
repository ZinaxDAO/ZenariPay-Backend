<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    use HasFactory;
    
    protected $table = "otp_verifications";
    
    protected $guarded = [];
    
    protected $casts = [
        'entity'    =>  'array'
    ];
}