<?php

use App\Http\Controllers\Attractions\AttractionAdminController;
use App\Http\Controllers\Attractions\UserAttractionController;
use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorLoginController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('attraction/register',[AttractionAdminController::class, 'register'])->name('attraction.login');

Route::post('attraction/addPhoto',[AttractionAdminController::class,'addPhotos'])->middleware('auth:attraction-api');

Route::get('attraction/index',[UserAttractionController::class,'index']);

Route::post('attraction/rateAttraction',[UserAttractionController::class,'addReview'])->middleware('auth:user-api');

Route::post('attraction/search',[UserAttractionController::class,'searchForAttractions']);

Route::group( ['prefix' => 'attraction','middleware' => ['auth:attraction-api'] ],function(){

    Route::post('dashboard',[AttractionAdminController::class, 'dashboard'])->name('attraction.dashboard');
});
