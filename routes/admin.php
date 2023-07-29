<?php

//use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Admin\AttractionController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('createadmin',[AdminController::class, 'CreateAdmin']);
Route::post('adminLogin',[AdminController::class, 'AdminLogin']);
Route::post('country',[AdminController::class, 'AddCountry']);
Route::post('city',[AdminController::class, 'AddCity']);
Route::get('showcity',[AdminController::class, 'ShowCities']);


// Admin Operations:
Route::post('login',[AdminController::class,'login']);
Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api'] ],function(){

});


// User Operations:
Route::get('getAllUsers',[UserController::class,'getAllUsers']);
Route::post('addUser',[UserController::class,'addUser']);
Route::get('deleteUserAccount',[UserController::class,'deleteUserAccount']);
Route::post('editUserAccount',[UserController::class,'editUserAccount']);
Route::get('getUserInfo',[UserController::class,'getUserInfo']);

// Trips Operations:
Route::post('makeNewAdmin',[TripController::class,'makeNewAdmin']);
Route::get('showTripUpdates',[TripController::class,'getUpdatingList']);
Route::post('acceptTripCompanyUpdate',[TripController::class,'acceptTripCompanyUpdate']);
Route::get('getAllTripAdmins',[TripController::Class,'getAllTripAdmins']);


Route::get('getAllTripCompanies',[TripController::class,'getAllTripCompanies']);
Route::get('getTripCompanyDetails',[TripController::class,'getTripCompanyDetails']);
Route::post('editCompanyDetails',[TripController::class,'editCompanyDetails']);
Route::get('deleteTheCompany',[TripController::class,'deleteTheCompany']);


// Attraction Operations:
Route::post('makeNewAdmin',[AttractionController::class,'makeNewAdmin']);
Route::get('showAttractionUpdates',[AttractionController::class,'getUpdatingList']);
Route::get('getUpdatingDetails',[AttractionController::class,'getUpdatingDetails']);
Route::post('acceptUpdate',[AttractionController::class,'acceptingAttraction']);
Route::get('getAllAttractions',[AttractionController::class,'getAllAttractions']);
Route::get('getAllAdmins',[AttractionController::Class,'getAllAdmins']);
Route::get('getAttractionDetails',[AttractionController::class,'getAttractionDetails']);
Route::post('editAttractionDetails',[AttractionController::class,'editAttractionDetails']);
Route::get('deleteAdmin',[AttractionController::class,'deleteAdmin']);
Route::get('deleteAttraction',[AttractionController::class,'deleteAttraction']);
Route::post('uploadMultiplePhotos',[AttractionController::class,'uploadMultiplePhotos']);
Route::post('uploadOnePhoto',[AttractionController::class,'uploadOnePhoto']);
Route::post('deleteOnePhoto',[AttractionController::class,'deleteOnePhoto']);
Route::get('getLatestReservations',[AttractionController::class,'getLatestReservations']);






// Hotel Operations:



// Flights Operations:

