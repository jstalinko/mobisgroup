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
  public function totalCommission()
{
    $finalStatuses = [
        'completed', 
        'withdrawn',
        'waiting_payment'
    ];
    
    return self::where('user_id', $this->user_id)
        // Gunakan whereIn untuk logika "status SAMA DENGAN completed ATAU SAMA DENGAN withdrawed"
        ->whereIn('status', $finalStatuses)
        ->sum('bonus_amount');
}
    public function availableCommission()
    {
        return self::where('user_id', $this->user_id)
            ->where('status', 'completed')
            ->sum('bonus_amount');
    }
    public function withdrawnCommission()
    {
        return self::where('user_id', $this->user_id)
            ->where('status', 'withdrawn')
            ->sum('bonus_amount');
    }
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }
    public function markAsPending()
    {
        $this->status = 'pending';
       return  $this->save();
    }
    public function markAsWithdrawn()
    {
        $this->status = 'withdrawn';
        return $this->save();
    }
    
}
