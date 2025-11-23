<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public static function makeSubscription($user_id,$plan_id,$plan_price)
    {
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $codeParts = [];
                for ($i = 0; $i < 3; $i++) {
                    $p = '';
                    for ($j = 0; $j < 4; $j++) {
                        $p .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    $codeParts[] = $p;
                }
                $setting = json_decode(file_get_contents(storage_path('app/settings.json')), true);
                $domain = ltrim(str_replace(['http://','https://','www.'], '', $setting['site_url']),'/');
                $prefix = strtoupper(substr($domain, 0, 4));
                $subscriptionCode = $prefix.'-' . implode('-', $codeParts);

                // 8. Insert ke subscriptions
                $sub = Subscription::create([
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'start_at' => Carbon::now(),
                    'end_at' => Carbon::now()->addMonth(),
                    'status' => 'active',
                    'subscription_code' => $subscriptionCode,
                    'price' => $plan_price,
                ]);
            return $sub;
    }
}
