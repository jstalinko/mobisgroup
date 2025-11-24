<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Referral;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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
        $device_id = $request->device_id;

        // Cari user berdasarkan license_key
        $user = User::where('license_key', $license_key)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'License key tidak ditemukan.',
            ], 404);
        }
    Auth::login($user,true);
    session()->put('device_id', $device_id);
    

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
                'device_id' => $device_id,
            ],
        ], 200);
    }

    public function logout()
    {
        $user = auth()->user();
        if($user){
            UserDevice::where('user_id', $user->id)
                ->where('device_id', session('device_id'))
                ->update(['revoked' => true]);
        }else{
            // For API logout with token
            $token = request()->user()->currentAccessToken();
            if ($token) {
                $token->delete();
            }
        }
        session()->forget('device_id');
        auth()->logout();
        @session_destroy();
        return redirect('/');
    }

    public function checkout(Request $request)
    {
        $referralCode = $request->referral_code;
        if($referralCode){
            $refUser = User::where('name',$referralCode)->first();
            if(!$refUser){
                return response()->json([
                    'success' => false,
                    'message' => 'Kode referral tidak valid.',
                ], 400);
            }
        }
        $user = auth()->user();
        if(!$user){
           $user_id = null;
        }else{
           $user_id = $user->id;
        }
        $kodeUniq = rand(100,700);
        $plan_id = $request->plan_id;
        $order = new Order();
        $order->user_id = $user_id;
        $order->plan_id = $plan_id;
        $order->price = Plan::find($plan_id)->price;
        $order->total_amount = ($order->price+$kodeUniq);
        $order->status = 'pending';
        $order->payment_method = $request->payment_method ?? 'qris';
        $order->kode_unik = $kodeUniq;
        $order->invoice = 'INV-' . strtoupper(uniqid());
        $order->referral_code = $referralCode ?? null;
        $order->save();
        return response()->json([
            'success' => true,
            'message' => 'Checkout initiated for plan ID: ' . $plan_id,
            'redirect' =>url('invoice/'.$order->invoice)
        ], 200);
    }
}
