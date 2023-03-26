<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curr_requirement extends Model
{
    use HasFactory;
  	protected $table = 'curr_requirements';
  	protected $casts = [
  	    'payment_info'   => 'array'
  	];
}
