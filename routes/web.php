<?php


use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\JustOrangeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', action: [JustOrangeController::class , 'index']);
Route::get('/detail/{bookId}/{slug}',[JustOrangeController::class,'movieDetail'])->middleware(['auth','subscription']);
Route::get('/play/{bookId}/{episode}/{slug}',[JustOrangeController::class,'moviePlay'])->middleware(['auth','subscription']);
Route::get('/search',[JustOrangeController::class,'search']);
Route::get('/login',[AuthController::class,'loginPage'])->name('login');
Route::get('/get-license',[AuthController::class , 'licensePage'])->name('license');
Route::get('/logout',[AuthController::class,'logout'])->name('logout');
Route::get('/plan', [JustOrangeController::class,'plan'])->name('plan');
Route::get('/limit-devices',[JustOrangeController::class,'limitDevices'])->name('limit-devices');
Route::get('/blocked',[JustOrangeController::class,'blockedPage'])->name('blocked');
Route::get('/invoice/{invoice}',[JustOrangeController::class,'invoicePage'])->name('invoice');
Route::get('/coming-soon/{service}',[JustOrangeController::class,'ComingSoonService']);
Route::get('/stop',[JustOrangeController::class,'stopPage'])->name('stop');
Route::get('/referral',[JustOrangeController::class,'referralPage'])->name('referral');


Route::get('/v', function(Request $request) {

    // 1. Ambil dan Dekode Parameter
    $full_url = urldecode($request->get('src'));
    $serviceName = $request->get('s') ?? 'default';
    $bookId = $request->get('b') ?? null;
    $ep = $request->get('ep') ?? '0';
    $slug = $request->get('slug') ?? md5($full_url);

    $cache_key_url = $serviceName.'_'.$bookId.'-'.$slug.'-episode-'.$ep;
    $file_name = 'videos/'.$serviceName.'/'. $cache_key_url. '.mp4';
    $disk = Storage::disk('local');

    // 3. Logic Cache dan Download
    $max_retries = 3; // Batasi jumlah percobaan ulang
    $attempts = 0;

    // Lakukan loop selama file belum ada, atau ukurannya 0, dan percobaan belum mencapai batas
    while (!($disk->exists($file_name) && $disk->size($file_name) > 0) && $attempts < $max_retries) {
        $attempts++;
        Log::info("Fetching video: " . $full_url . " (Attempt: " . $attempts . ")");

        try {
            // Gunakan Http::get() untuk mengambil konten
            $response = Http::timeout(60)->get($full_url); // Set timeout, misal 60 detik

            if ($response->successful()) {
                $contents = $response->body();
                $disk->put($file_name, $contents);
                if ($disk->size($file_name) > 0) {
                    Log::info("Video saved successfully and is not zero size: " . $file_name);
                    break; // Keluar dari loop jika berhasil
                } else {
                    Log::warning("Downloaded file is zero size. Retrying...");
                    // Hapus file 0-byte jika ada, agar loop dapat mencoba lagi
                    $disk->delete($file_name);
                }
            } else {
                Log::error("Failed to fetch video source. HTTP Status: " . $response->status() . " URL: " . $full_url);
                if ($attempts >= $max_retries) {
                    abort(500, 'Failed to fetch video source after ' . $max_retries . ' attempts. HTTP Status: ' . $response->status());
                }
            }

        } catch (\Exception $e) {
            Log::error("Exception during video fetch: " . $e->getMessage() . " URL: " . $full_url);
            if ($attempts >= $max_retries) {
                abort(500, 'Failed to fetch video source after ' . $max_retries . ' attempts. Exception: ' . $e->getMessage());
            }
        }
    }

    if (!($disk->exists($file_name) && $disk->size($file_name) > 0)) {
         abort(500, 'Video file could not be downloaded or resulted in zero size after multiple attempts.');
    }
    $internal_path = "/protected/videos/" . $serviceName . "/" . $cache_key_url . ".mp4";

    return response("", 200, [
        "Content-Type" => "video/mp4",
        "X-Accel-Redirect" => $internal_path, 
        "Accept-Ranges" => "bytes",
    ]);
});