<?php

use App\Http\Controllers\Trips\UserTripsController;
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

Route::group( [],function(){
    Route::get('trip/index',[UserTripsController::class,'index']);
    Route::post('trip/search',[UserTripsController::class,'searchForTrip']);
    Route::get('trip/viewTripDetails',[UserTripsController::class,'viewTripDetails']);
    Route::get('trip/viewDeparturesAndDates',[UserTripsController::class,'viewDeparturesAndDatesForSomeTrip']);
});

// authentication needed
Route::group( ['middleware' => ['auth:user-api'] ],function(){
    Route::post('trip/makeReservation',[UserTripsController::class,'makeReservation']);
    Route::post('trip/sendReview',[UserTripsController::class,'addReview']);
    Route::post('trip/cancellingReservation',[UserTripsController::class,'cancellingReservation']);
});

