<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisputeMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $guarded = [];

    protected $fillable = [
        'dispute_id',
        'user_id',
        'message',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}