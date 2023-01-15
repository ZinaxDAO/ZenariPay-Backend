<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['user_id', 'updated_at', 'deleted_at'];
    protected $guarded =[];
}
