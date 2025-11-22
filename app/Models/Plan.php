<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'price',
        'description',
        'feature_dramabox',
        'feature_netshort',
        'duration',
        'max_devices',
        'active',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'subscriptions');
    }
    public function isActive()
    {
        return $this->active;
    }
}
