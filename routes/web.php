<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CloakingController;
use App\Http\Controllers\JustOrangeController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    $full_url = urldecode($request->get('src'));
    $serviceName = $request->get('s') ?? 'default';
    $bookId = $request->get('b') ?? null;

    $cache_key_url = Str::before($full_url, '?');
    $file_name = 'videos/'.$serviceName.'/'. md5($cache_key_url) . '_' . $bookId . '.mp4'; 

    if (!Storage::disk('local')->exists($file_name)) {

        $contents = file_get_contents($full_url);

        if ($contents === false) {
            abort(500, 'Failed to fetch video source.');
        }

        Storage::disk('local')->put($file_name, $contents);
    }

    $internal_path = "/protected/videos/" . $serviceName . "/" . md5($cache_key_url) . "_" . $bookId . ".mp4";

    return response("", 200, [
        "Content-Type" => "video/mp4",
        "X-Accel-Redirect" => $internal_path,
        "Accept-Ranges" => "bytes",
    ]);
});
