<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bonus_amount',
        'referred_user_id',
        'plan_id',
        'status',
        'notes'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
