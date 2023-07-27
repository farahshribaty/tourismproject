<?php

use App\Http\Controllers\Attractions\AttractionAdminController;
use App\Http\Controllers\Attractions\UserAttractionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

// User Operations

Route::post('register',[AttractionAdminController::class,'register']);
Route::post('addPhoto',[AttractionAdminController::class,'addPhotos'])->middleware('auth:attraction-api');
Route::get('index',[UserAttractionController::class,'index']);
Route::post('rateAttraction',[UserAttractionController::class,'addReview'])->middleware('auth:user-api');
Route::post('search',[UserAttractionController::class,'searchForAttractions']);
Route::post('sendReview',[UserAttractionController::class,'addReview'])->middleware('auth:user-api');
Route::post('viewAttractionDetails',[UserAttractionController::class,'viewAttractionDetails']);
Route::post('bookingTicket',[UserAttractionController::class,'bookingTicket'])->middleware('auth:user-api');


// Attraction Admin Operation

Route::get('getAttractionDetails',[AttractionAdminController::class,'getAttractionDetails'])->middleware('auth:attraction_admin-api');
Route::post('editAttractionDetails',[AttractionAdminController::class,'editAttractionDetails'])->middleware('auth:attraction_admin-api');
Route::post('uploadMultiplePhotos',[AttractionAdminController::class,'uploadMultiplePhotos'])->middleware('auth:attraction_admin-api');
Route::post('uploadOnePhoto',[AttractionAdminController::class,'uploadOnePhoto'])->middleware('auth:attraction_admin-api');
Route::post('deleteOnePhoto',[AttractionAdminController::class,'deleteOnePhoto'])->middleware('auth:attraction_admin-api');
Route::get('getLatestReservations',[AttractionAdminController::class,'getLatestReservations'])->middleware('auth:attraction_admin-api');


Route::post('adminRegister',[AttractionAdminController::class,'adminRegister']);  // not official


Route::group( ['prefix' => 'attraction','middleware' => ['auth:attraction-api'] ],function(){

    Route::post('dashboard',[AttractionAdminController::class, 'dashboard'])->name('attraction.dashboard');
});

Route::post('addingAttraction',[\App\Http\Controllers\Attractions\AdminAttractionController::class,'addAttraction']);
