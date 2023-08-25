<?php

use App\Http\Controllers\Trips\TripAdminController;
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



Route::middleware(['localization'])->group(function(){
    // User Operations

    Route::group( [],function(){
        Route::get('index',[UserTripsController::class,'index']);
        Route::post('search',[UserTripsController::class,'searchForTrip']);
        Route::get('viewTripDetails',[UserTripsController::class,'viewTripDetails']);
    });

    Route::group( ['middleware' => ['auth:user-api'] ],function(){
        Route::post('makeReservation',[UserTripsController::class,'makeReservation']);
        Route::post('sendReview',[UserTripsController::class,'addReview']);
        Route::post('cancellingReservation',[UserTripsController::class,'cancellingReservation']);
        Route::post('addToFavourites',[UserTripsController::class,'addToFavourites']);
        Route::post('removeFromFavourites',[UserTripsController::class,'removeFromFavourites']);
    });

// Trip Admin Operations

    Route::group( ['middleware' => ['auth:trip_admin-api'] ],function(){

        Route::post('addTripCompany',[TripAdminController::class,'addTripCompany']);

        Route::group( ['middleware' => ['just registered trip companies'] ],function(){
            Route::get('getTripCompanyDetails',[TripAdminController::class,'getTripCompanyDetails']);
            Route::post('editCompanyDetails',[TripAdminController::class,'editCompanyDetails']);
            Route::get('getUpdatingList',[TripAdminController::class,'getUpdatingList']);
            Route::get('getAllTrips',[TripAdminController::class,'getAllTrips']);
            Route::get('getTripDetails',[TripAdminController::class,'getTripDetails']);
            Route::get('getTripDates',[TripAdminController::class,'getTripDates']);
            Route::get('getLatestReservations',[TripAdminController::class,'getLatestReservations']);
            Route::post('editCompanyDetails',[TripAdminController::class,'editCompanyDetails']);
            Route::post('editTripDetails',[TripAdminController::class,'editTripDetails']);
            Route::post('editOfferDetails',[TripAdminController::class,'editOfferDetails']);
            Route::post('addNewTrip',[TripAdminController::class,'addNewTrip']);
            Route::post('addNewOffer',[TripAdminController::class,'addNewOffer']);
            Route::post('addNewDate',[TripAdminController::class,'addNewDate']);
            Route::get('deleteTheCompany',[TripAdminController::class,'deleteTheCompany']);
            Route::get('deleteSomeTrip',[TripAdminController::class,'deleteSomeTrip']);
            Route::get('deleteSomeOffer',[TripAdminController::class,'deleteSomeOffer']);
            Route::get('deleteSomeDate',[TripAdminController::class,'deleteSomeDate']);
        });
    });


    Route::post('adminRegister',[TripAdminController::class,'adminRegister']);
});

