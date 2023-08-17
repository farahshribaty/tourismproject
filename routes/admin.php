<?php

//use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Admin\AttractionController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('createadmin',[AdminController::class, 'CreateAdmin1']);
Route::post('adminLogin',[AdminController::class, 'AdminLogin']);
Route::post('country',[AdminController::class, 'AddCountry']);
Route::post('city',[AdminController::class, 'AddCity']);
Route::get('showcity',[AdminController::class, 'ShowCities']);


// Admin Operations:
Route::post('login',[AdminController::class,'login']);
Route::get('getUpdatesList',[AdminController::class,'getUpdatingList']);
Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api'] ],function(){

});


// User Operations:
Route::get('getAllUsers',[UserController::class,'getAllUsers']);
Route::post('addUser',[UserController::class,'addUser']);
Route::get('deleteUserAccount',[UserController::class,'deleteUserAccount']);
Route::post('editUserAccount',[UserController::class,'editUserAccount']);
Route::get('getUserInfo',[UserController::class,'getUserInfo']);

// Trips Operations:
Route::post('makeNewTripAdmin',[TripController::class,'makeNewAdmin']);
Route::get('showTripUpdates',[TripController::class,'getUpdatingList']);
Route::get('getTripUpdatingDetails',[TripController::class,'getUpdatingDetails']);
Route::post('acceptTripCompanyUpdate',[TripController::class,'acceptTripCompanyUpdate']);
Route::get('getAllTripCompanies',[TripController::class,'getAllTripCompanies']);
Route::get('getTripCompanyDetails',[TripController::class,'getTripCompanyDetails']);
Route::get('getAllTripAdmins',[TripController::class,'getAllTripAdmins']);
Route::get('getTripsForCompany',[TripController::class,'getTripsForCompany']);
Route::get('getTripDetails',[TripController::class,'getTripDetails']);
Route::get('getTripDates',[TripController::class,'getTripDates']);
Route::get('getLatestTripReservations',[TripController::class,'getLatestReservations']);
Route::post('editCompanyDetails',[TripController::class,'editCompanyDetails']);
Route::post('editTripDetails',[TripController::class,'editTripDetails']);
Route::post('editDayDetails',[TripController::class,'editDayDetails']);
Route::post('editOfferDetails',[TripController::class,'editOfferDetails']);
Route::post('addNewTrip',[TripController::class,'addNewTrip']);
Route::post('addNewOffer',[TripController::class,'addNewOffer']);
Route::post('addNewDate',[TripController::class,'addNewDate']);
Route::post('uploadOneTripPhoto',[TripController::class,'uploadOnePhoto']);
Route::post('uploadMultipleTripPhotos',[TripController::class,'uploadMultiplePhotos']);
Route::get('deleteSomeCompany',[TripController::class,'deleteSomeCompany']);
Route::get('deleteSomeTrip',[TripController::class,'deleteSomeTrip']);
Route::get('deleteSomeOffer',[TripController::class,'deleteSomeOffer']);
Route::get('deleteSomeDate',[TripController::class,'deleteSomeDate']);
Route::get('deleteOneTripPhoto',[TripController::class,'deleteOnePhoto']);



// Attraction Operations:
Route::post('makeNewAdmin',[AttractionController::class,'makeNewAdmin']);
Route::get('showAttractionUpdates',[AttractionController::class,'getUpdatingList']);
Route::get('getUpdatingDetails',[AttractionController::class,'getUpdatingDetails']);
Route::post('acceptUpdate',[AttractionController::class,'acceptingAttraction']);
Route::get('getAllAttractions',[AttractionController::class,'getAllAttractions']);
Route::get('getAllAdmins',[AttractionController::class,'getAllAdmins']);
Route::get('getAttractionDetails',[AttractionController::class,'getAttractionDetails']);
Route::post('editAttractionDetails',[AttractionController::class,'editAttractionDetails']);
Route::get('deleteAdmin',[AttractionController::class,'deleteAdmin']);
Route::get('deleteAttraction',[AttractionController::class,'deleteAttraction']);
Route::post('uploadMultiplePhotos',[AttractionController::class,'uploadMultiplePhotos']);
Route::post('uploadOnePhoto',[AttractionController::class,'uploadOnePhoto']);
Route::post('deleteOnePhoto',[AttractionController::class,'deleteOnePhoto']);
Route::get('getLatestReservations',[AttractionController::class,'getLatestReservations']);






// Hotel Operations:
Route::post('makeNewAdminHotel',[HotelController::class,'makeNewAdmin']);
Route::post('acceptUpdateHotel',[HotelController::class,'acceptingHotel']);
Route::get('AllHotels',[HotelController::class, 'getAllHotelsWithMainInfo']);
Route::post('OneHotelByAdmin',[HotelController::class, 'getHotelWithAllInfo2']);
Route::get('getAllHotelAdmins',[HotelController::class,'getAllHotelAdmins']);
Route::get('getHotelUpdatingDetails',[HotelController::class,'getUpdatingDetails']);
Route::post('addFacilities',[HotelController::class,'addFacilitiesForHotel']);
Route::get('getAllFacilities',[HotelController::class,'getAllFacilitiesForHotel']);







// Flights Operations:

