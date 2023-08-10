<?php

//use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Hotel\UserController;
use App\Http\Controllers\Hotel\HotelController;



//Hotel Admin Routes:
Route::post('hotel/adminlogin',[AdminController::class, 'AdminLogin']);
Route::post('admin/addMultiRooms',[AdminController::class, 'addMultiRoomsByType']);
Route::post('admin/addingFeatures',[AdminController::class, 'addingFeatures']);
Route::post('admin/addPhoto',[AdminController::class, 'addPhotos']);
Route::post('admin/addRoomPhoto',[AdminController::class, 'addRoomPhotos']);
Route::post('admin/SeeAllRooms',[AdminController::class, 'SeeAllRooms']);
Route::get('admin/getHotelType',[AdminController::class, 'getHotelType']);
Route::get('admin/getRoomType',[AdminController::class, 'getRoomType']);
Route::get('admin/getRoomFeatures',[AdminController::class, 'getRoomFeatures']);


//Hotel user Routes:
Route::post('user/addReview',[UserController::class, 'addReview']);
Route::get('TopRatedAndTypes',[HotelController::class, 'TopRatedAndTypes']);
Route::get('TopRated',[HotelController::class, 'TopRated']);
Route::get('ShowHotelTypes',[HotelController::class, 'ShowHotelTypes']);
Route::get('ShowRoomsTypes',[HotelController::class, 'ShowRoomsTypes']);
Route::get('ShowAllHotel',[HotelController::class, 'ShowALLHotel']);
Route::post('ShowHotelRooms',[HotelController::class, 'ShowHotelRooms']);
Route::post('hotel/Hotelsearch',[UserController::class, 'Hotelsearch']);
Route::post('AllHotelInfo',[UserController::class, 'GetALLHotelInfo']);
Route::post('ShowOneRoom',[UserController::class, 'ShowOneRoom']);
Route::post('bookingRoom',[UserController::class, 'bookingRoom'])->middleware('auth:user-api');


Route::group( ['middleware' => ['auth:hotel_admin-api'] ],function()
{
    Route::post('admin/createhotel',[AdminController::class, 'CreateHotel']);
    Route::post('admin/OneHotel',[AdminController::class, 'getHotelWithAllInfoByToken']);
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');

});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

});
