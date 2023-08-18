<?php

//use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Hotel\UserController;
use App\Http\Controllers\Hotel\HotelController;



//Hotel Admin Routes:
Route::post('hotel/adminlogin',[AdminController::class, 'AdminLogin']);
Route::post('hotel/addMultiRooms',[AdminController::class, 'addMultiRoomsByType']);
Route::post('hotel/addingFeatures',[AdminController::class, 'addingFeatures']);
Route::post('hotel/addPhoto',[AdminController::class, 'addPhotos']);
Route::post('hotel/addRoomPhoto',[AdminController::class, 'addRoomPhotos']);
Route::post('hotel/SeeAllRooms',[AdminController::class, 'SeeAllRooms']);
 Route::get('hotel/getHotelType',[AdminController::class, 'getHotelType']);
 Route::post('hotel/SeeOneRoom',[AdminController::class, 'SeeOneRoom']);
 Route::get('hotel/getRoomType',[AdminController::class, 'getRoomType']);
 Route::get('hotel/getRoomFeatures',[AdminController::class, 'getRoomFeatures']);


//Hotel user Routes:

Route::get('TopRatedAndTypes',[HotelController::class, 'TopRatedAndTypes']);
Route::get('TopRated',[HotelController::class, 'TopRated']);
Route::get('ShowHotelTypes',[HotelController::class, 'ShowHotelTypes']);
Route::get('ShowRoomsTypes',[HotelController::class, 'ShowRoomsTypes']);
Route::get('ShowAllHotel',[HotelController::class, 'ShowALLHotel']);
Route::post('ShowHotelRooms',[HotelController::class, 'ShowHotelRooms']);
Route::post('hotel/Hotelsearch',[UserController::class, 'Hotelsearch']);
Route::post('AllHotelInfo',[UserController::class, 'GetALLHotelInfo']);
Route::post('ShowOneRoom',[UserController::class, 'ShowOneRoom']);


Route::group( ['middleware' => ['auth:user-api'] ],function()
{
    Route::post('bookingRoom',[UserController::class, 'bookingRoom']);
    // ->middleware('auth:user-api');
    Route::post('user/addReview',[UserController::class, 'addReview']);
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel_admin-api'] ],function()
{
    Route::post('createhotel',[AdminController::class, 'CreateHotel']);
    Route::post('OneHotel',[AdminController::class, 'getHotelWithAllInfoByToken']);
    Route::post('addFacilitis',[AdminController::class, 'addFacilitisForHotel']);
    Route::get('getAllFacilities',[AdminController::class, 'getAllFacilitiesForThisHotel']);
    Route::post('addOneFacility',[AdminController::class, 'addOneFacility']);
    Route::post('deleteFacility',[AdminController::class, 'deleteFacility']);
    Route::post('deleteFeature',[AdminController::class, 'deleteFeatureFromRoom']);
    Route::post('deleteRoom',[AdminController::class, 'DeleteRoom']);
    Route::post('admin/DeleteHotelPhoto',[AdminController::class, 'DeleteHotelPhoto']);
    Route::post('admin/DeleteRoomPhoto',[AdminController::class, 'DeleteRoomPhoto']);
    Route::post('admin/SeeAllReservations',[AdminController::class, 'SeeAllReservations']);

});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');

});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

});
