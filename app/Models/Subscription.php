<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_at',
        'end_at',
        'status',
        'subscription_code',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function isActive()
    {
        return $this->status === 'active';
    }
    public function isExpired()
    {
        return $this->status === 'expired';
    }
    public function isPastDue()
    {
        return $this->status === 'past_due';
    }
    public function isCanceled()
    {
        return $this->status === 'canceled';
    }
}
