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
    // 1. Ambil URL lengkap dan decode (URL ini yang mengandung token)
    // Hasil decode: https://alicdn.netshort.com/o84IjuedBGcCkgfFB3LcVOEIbfADQR3eUnFSYu?a=0&auth_key=...
    $full_url = urldecode($request->get('src'));

    // 2. Tentukan Kunci Cache Statis
    // Kita ambil semua string sebelum tanda '?'
    // Hasilnya: https://alicdn.netshort.com/o84IjuedBGcCkgfFB3LcVOEIbfADQR3eUnFSYu
    $cache_key_url = Str::before($full_url, '?');

    // 3. Buat Nama File Cache (Hash dari Kunci Statis)
    // Ini akan menjadi nama file cache yang konsisten.
    $file_name = 'videos/' . md5($cache_key_url) . '.mp4'; 
    
    // Periksa apakah file cache statis sudah ada di storage/app/videos/
    if (!Storage::disk('local')->exists($file_name)) {
        
        // --- Cache Miss: Unduh dan Simpan (Menggunakan $full_url) ---
        
        // Gunakan URL LENGKAP ($full_url) untuk mengunduh karena ia membawa 'auth_key'
        $contents = file_get_contents($full_url); 
        
        if ($contents === false) {
             // Tangani error jika gagal fetch
             abort(500, 'Failed to fetch video source.');
        }
        
        // Simpan konten menggunakan $file_name (kunci statis)
        Storage::disk('local')->put($file_name, $contents);
    }
    
    // --- Cache Hit: Stream dari Cache Lokal ---
    
    $path = Storage::disk('local')->path($file_name);

    // Stream menggunakan response()->file()
    return response()->file($path, [
        "Content-Type" => "video/mp4",
        "Accept-Ranges" => "bytes",
        "Cache-Control" => "public, max-age=604800", // Cache 7 hari
    ]);
});
