<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        if(Auth::check())
        {
            return redirect('/');
        }
        return Inertia::render('login');
    }


    public function checkLicense(Request $request)
    {
        $license_key = $request->license_key;

        // Cari user berdasarkan license_key
        $user = User::where('license_key', $license_key)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'License key tidak ditemukan.',
            ], 404);
        }
    Auth::login($user,true);

        // Mengambil subscription aktif melalui relasi activeSubscription()
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'License ditemukan, tetapi tidak memiliki subscription aktif.',
            ], 403);
        }

          // -------------------------------
    // AUTO LOGIN (Sanctum Token)
    // -------------------------------
    // Hapus token lama (optional)
    $user->tokens()->delete();

    // Buat token baru
    $token = $user->createToken('license_login')->plainTextToken;



        // Jika valid
        return response()->json([
            'status' => true,
            'message' => 'License valid.',
            'token' => $token,
            'data' => [
                'user' => [
                    'id'          => $user->id,
                    'username'    => $user->username,
                    'email'       => $user->email,
                    'license_key' => $user->license_key,
                ],
                'subscription' => [
                    'plan_id'    => $subscription->plan_id,
                    'plan_name'  => $subscription->plan->name ?? null,
                    'start_at'   => $subscription->start_at,
                    'end_at'     => $subscription->end_at,
                    'status'     => $subscription->status,
                    'subscription_code' => $subscription->subscription_code,
                ],
            ],
        ], 200);
    }
}
