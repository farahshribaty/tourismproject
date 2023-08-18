<?php

use App\Http\Controllers\Attractions\AttractionAdminController;
use App\Http\Controllers\Attractions\UserAttractionController;
use App\Http\Middleware\RegisteredAttractionCompanies;
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

//Route::post('adminRegister',[AttractionAdminController::class,'adminRegister']);  // not official


//Route::group( ['prefix' => 'attraction','middleware' => ['auth:attraction-api'] ],function(){
//    Route::post('dashboard',[AttractionAdminController::class, 'dashboard'])->name('attraction.dashboard');
//});
//
//Route::post('addingAttraction',[\App\Http\Controllers\Attractions\AdminAttractionController::class,'addAttraction']);


//Route::middleware(['localization'])->group(function(){
    // User Operations

    Route::group( [],function(){
        Route::get('index',[UserAttractionController::class,'index']);
        Route::post('search',[UserAttractionController::class,'searchForAttractions']);
        Route::post('viewAttractionDetails',[UserAttractionController::class,'viewAttractionDetails']);
    });

    Route::group( ['middleware' => ['auth:user-api'] ],function(){
        Route::post('rateAttraction',[UserAttractionController::class,'addReview']);
        Route::post('sendReview',[UserAttractionController::class,'addReview']);
        Route::post('bookingTicket',[UserAttractionController::class,'bookingTicket']);
    });

// Attraction Admin Operation

    Route::group( ['middleware' => ['auth:attraction_admin-api'] ],function(){
        Route::post('addAttractionCompany',[AttractionAdminController::class,'addAttractionCompany']);
        Route::get('getUpdatingList',[AttractionAdminController::class,'getUpdatingList']);
        Route::group( ['middleware' => ['just registered attraction companies'] ],function(){
            Route::get('getAttractionDetails',[AttractionAdminController::class,'getAttractionDetails']);
            Route::post('editAttractionDetails',[AttractionAdminController::class,'editAttractionDetails']);
            Route::post('uploadMultiplePhotos',[AttractionAdminController::class,'uploadMultiplePhotos']);
            Route::post('uploadOnePhoto',[AttractionAdminController::class,'uploadOnePhoto']);
            Route::post('deleteOnePhoto',[AttractionAdminController::class,'deleteOnePhoto']);
            Route::get('getLatestReservations',[AttractionAdminController::class,'getLatestReservations']);
            Route::get('getAttractionTypes',[AttractionAdminController::class,'getAttractionTypes']);
        });
    });

//});
