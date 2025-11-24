<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'license_key',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }
    public function userDevices()
    {
        return $this->hasMany(UserDevice::class);
    }
    public static function makeRandomuser()
    {
        $setting = json_decode(file_get_contents(storage_path('app/settings.json')), true);
        $faker = fake();
        $username = str()->slug($faker->userName() . rand(100, 999));
        $domain = ltrim( str_replace(['http://', 'https://', 'www.'], '', $setting['site_url']),'/');

        $email = $username . '@' . $domain;
        $password = bcrypt('password123');
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $prefix = strtoupper(substr($domain, 0, 4));

        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $seg = '';
            for ($j = 0; $j < 4; $j++) {
                $seg .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $segments[] = $seg;
        }

        $licenseKey = $prefix . '-' . implode('-', $segments);

        // 5. Buat user baru
        $user = User::create([
            'name' => $username,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'license_key' => $licenseKey,
        ]);

        return $user;
    }

    public function regenerateLicenseKey()
    {
        $setting = json_decode(file_get_contents(storage_path('app/settings.json')), true);
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $domain = ltrim( str_replace(['http://', 'https://', 'www.'], '', $setting['site_url']),'/');
        $prefix = strtoupper(substr($domain, 0, 4));

        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $seg = '';
            for ($j = 0; $j < 4; $j++) {
                $seg .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $segments[] = $seg;
        }

        $newLicenseKey = $prefix . '-' . implode('-', $segments);
        $this->license_key = $newLicenseKey;
        $this->save();

        return $newLicenseKey;
    }
    // --- Relasi ke Referral (Wajib Ada) ---
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'user_id');
    }
    
    // --- Methods Perhitungan Komisi Baru ---

    /**
     * Menghitung total komisi yang diperoleh (completed, withdrawn, waiting_payment).
     */
    public function totalCommission(): float
    {
        $finalStatuses = [
            'completed', 
            'withdrawn',
            'waiting_payment'
        ];
        
        // Memanggil relasi referrals() lalu menerapkan whereIn
        return $this->referrals()
                    ->whereIn('status', $finalStatuses)
                    ->sum('bonus_amount');
    }

    /**
     * Menghitung komisi yang tersedia dan belum ditarik (hanya completed).
     */
    public function availableCommission(): float
    {
        return $this->referrals()
                    ->where('status', 'completed')
                    ->sum('bonus_amount');
    }

    /**
     * Menghitung total komisi yang sudah ditarik.
     */
    public function withdrawnCommission(): float
    {
        return $this->referrals()
                    ->where('status', 'withdrawn')
                    ->sum('bonus_amount');
    }
}
