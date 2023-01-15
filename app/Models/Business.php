<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['id', 'user_id', 'updated_at', 'api_token', 'deleted_at'];
    protected $guarded = [];
}
