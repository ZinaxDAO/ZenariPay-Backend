<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSettings extends Model
{
    use HasFactory;
    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['user_id', 'updated_at', 'deleted_at'];
}
