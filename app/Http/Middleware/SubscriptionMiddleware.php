<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Helper;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ==============================
        // 1. Cek session login (WEB)
        // ==============================
        $user = auth()->user();

        // ==============================
        // 2. Jika tidak login (API)
        // ==============================
        if (!$user) {
            $user = $request->user(); // Sanctum
        }

        if (!$user) {
            return $this->error($request, 'Silakan login terlebih dahulu.', 401, 'login');
        }

        // ==============================
        // 3. Validasi License Key API
        // ==============================
        if ($request->is('api/*')) {
            $license = $request->header('X-LICENSE-KEY');

            if (!$license) {
                return $this->error($request, 'License key tidak ditemukan.', 400);
            }

            if ($license !== $user->license_key) {
                return $this->error($request, 'License key tidak valid.', 403);
            }
        }

        // ==============================
        // 4. Ambil subscription aktif
        // ==============================
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return $this->error($request, 'Anda tidak memiliki subscription aktif.', 403, 'plan');
        }

        // ==============================
        // 5. Check expired
        // ==============================
        if (now()->greaterThan($subscription->end_at)) {
            return $this->error($request, 'Subscription Anda sudah berakhir.', 403, 'plan');
        }

        // ==============================
        // 6. CHECK DEVICE LIMIT
        // ==============================
        $plan = $subscription->plan;
        $maxDevices = $plan->max_devices ?? 1;

        // Ambil Device ID (web & API)
        $deviceId = $this->getDeviceId($request);

        if (!$deviceId) {
            return $this->error($request, 'Device ID tidak terdeteksi.', 400);
        }

        // Cek device di DB
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        // Jika device belum terdaftar → daftarkan bila masih slot tersedia
        if (!$device) {
            $countActive = UserDevice::where('user_id', $user->id)
                ->where('revoked', false)
                ->count();

            if ($countActive >= $maxDevices) {
                return $this->error(
                    $request,
                    "Batas perangkat ($maxDevices) telah tercapai.",
                    403,
                    'limit-devices'
                );
            }

            // Register device baru
            UserDevice::create([
                'user_id'     => $user->id,
                'device_id'   => $deviceId,
                'user_agent'  => $request->userAgent(),
                'device_name' => Helper::getDeviceName($request->userAgent()),
                'ip'          => $request->ip(),
                'last_active' => now(),
            ]);
        } else {
            // Device ditemukan → cek apakah revoked
            if ($device->revoked) {
                return $this->error($request, 'Perangkat ini sudah diblokir.', 403,'blocked');
            }

            // Update last active
            $device->update([
                'last_active' => now()
            ]);
        }

        return $next($request);
    }

    /**
     * Ambil device ID dari header / session / cookie
     */
    private function getDeviceId($request)
    {
        // API → pakai Header
        if ($request->is('api/*')) {
            return $request->header('X-DEVICE-ID');
        }

        // WEB → pakai session atau cookie
        return session('device_id') ?? $request->cookie('device_id');
    }

    /**
     * Handle JSON or Web response depending on request
     */
    private function error($request, $message, $status = 400, $redirect = null)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => $message
            ], $status);
        }

        if ($redirect) {
            return redirect()->route($redirect)->withErrors(['error' => $message]);
        }

        return back()->withErrors(['error' => $message]);
    }
}
