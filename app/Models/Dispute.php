<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispute extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $guarded = [];

    // protected $fillable = [
    //     'user_id',
    //     'order_id', // the ID of the disputed order
    //     'dispute_type', // the type of dispute (e.g., delivery issue, quality issue, etc.)
    //     'dispute_details', // the details of the dispute (e.g., what went wrong)
    //     'status', // the status of the dispute (e.g., open, resolved, closed)
    //     'winner_id', // the ID of the user who won the dispute (if applicable)
    // ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}