<?php

use App\Http\Controllers\AuthController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloakingController;
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
Route::get('/coming-soon/{service}',[JustOrangeController::class,'ComingSoonService']);
