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
Route::get('getAllTripCompanies',[TripController::class,'getAllTripCompanies']);



// Attraction Operations:
Route::get('getAllAttractions',[AttractionController::class,'getAllAttractions']);




// Hotel Operations:



// Flights Operations:

