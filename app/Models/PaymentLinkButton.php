<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLinkButton extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['user_id', 'updated_at', 'deleted_at'];
    protected $guarded = [];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public static function findBySlug($slug){
        $data = self::where('slug', $slug)->with(['product'])->first();
        return $data;
    }
}
