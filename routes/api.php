<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\API\MovieServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JustOrangeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => '/v1/{service}'] , function(){
    Route::get('/theaters',[MovieServiceController::class,'getTheater']);
    Route::get('/recommend',[MovieServiceController::class,'getRecommend']);
    Route::get('/categories',action: [MovieServiceController::class,'getCategory']);
    Route::get('/detail/chapter/{bookId}',[MovieServiceController::class , 'getChapterDetail']);
    Route::get('/detail/{bookId}',[MovieServiceController::class, 'getTheaterDetail']);
    Route::get('/stream/{bookId}',[MovieServiceController::class,'getStream']);
    Route::post('/search',[MovieServiceController::class,'getSearch']);
});

Route::post('/check-license' , [AuthController::class,'checkLicense'])->middleware('api.session');
Route::post('/checkout' , [AuthController::class,'checkout']);
Route::post('/request-withdrawal',[JustOrangeController::class,'requestWithdrawal']);
Route::any('/payment-callback',function()
{
    response()->json(['success' => true],200,[],JSON_PRETTY_PRINT);
});