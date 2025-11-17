<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ==============================
        // 1. Cek session login (web)
        // ==============================
        $user = auth()->user();

        // ==============================
        // 2. Jika tidak login, cek Bearer Token (API)
        // ==============================
        if (!$user) {
            $user = $request->user(); // Sanctum API token
        }

        if (!$user) {
            return $this->error($request, 'Silakan login terlebih dahulu.', 401, 'login');
        }

        // ==============================
        // 3. Validasi License Key Header
        // ==============================
        if ($request->is('api/*')) {
            $license = $request->header('X-LICENSE-KEY');

            if (!$license) {
                return $this->error($request, 'License key tidak ditemukan di header.', 400);
            }

            if ($license !== $user->license_key) {
                return $this->error($request, 'License key tidak valid untuk user ini.', 403);
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

        return $next($request);
    }

    /**
     * Handle JSON or Web response depending on request type
     */
    private function error($request, $message, $status = 400, $redirect = null)
    {
        // Response untuk API
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => $message
            ], $status);
        }

        // Response untuk WEB
        if ($redirect) {
            return redirect()->route($redirect)->withErrors(['error' => $message]);
        }

        dd($message);
        return back()->withErrors(['error' => $message]);
    }
}
